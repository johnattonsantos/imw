<?php

namespace Tests\Unit;

use App\Http\Controllers\CarteirasClerigosController;
use App\Models\InstituicoesInstituicao;
use App\Models\PessoasPessoa;
use App\Services\ServiceBase\GetBaseParamsService;
use App\Services\ServiceClerigosRegiao\CarteirasClerigosService;
use App\Services\ServicePerfil\ListPerfilService;
use App\Support\CredencialClerigo;
use App\Support\SimpleQrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\MySqlConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CarteirasClerigosImpressaoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $regiao = (new InstituicoesInstituicao)->forceFill(['id' => 10, 'nome' => 'Regiao Teste']);
        session(['session_perfil' => (object) ['instituicoes' => (object) ['regiao' => $regiao]]]);
        $base = Mockery::mock(GetBaseParamsService::class);
        $base->shouldReceive('execute')->andReturn((object) []);
        $this->app->instance(GetBaseParamsService::class, $base);
    }

    public function test_selection_is_scoped_to_logged_region_and_excludes_inactive_registrations(): void
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        $connection->shouldReceive('select')->once()->withArgs(function ($sql, $bindings) {
            $this->assertStringContainsString('`pessoas_pessoas`.`regiao_id` = ?', $sql);
            $this->assertStringContainsString('`pessoas_pessoas`.`id` in (?, ?)', $sql);
            $this->assertStringContainsString('`pessoas_pessoas`.`deleted_at` is null', $sql);
            $this->assertSame([10, 12, 13], $bindings);
            return true;
        })->andReturn([]);
        $original = Model::getConnectionResolver();
        $resolver = new ConnectionResolver(['test' => $connection]);
        $resolver->setDefaultConnection('test');
        Model::setConnectionResolver($resolver);
        try {
            $this->assertCount(0, (new CarteirasClerigosService)->listar(10, [12, 13]));
        } finally {
            Model::setConnectionResolver($original);
        }
    }

    public function test_unavailable_or_outside_region_ids_are_blocked_before_pdf_generation(): void
    {
        $service = Mockery::mock(CarteirasClerigosService::class);
        $service->shouldReceive('listar')->once()->with(10, [99])->andReturn(collect());
        $service->shouldNotReceive('preparar');
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(0);
        try {
            (new CarteirasClerigosController)->pdf(Request::create('/', 'POST', ['clerigos' => [99]]), $service);
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
            throw $e;
        }
    }

    /** @dataProvider invalidSelectionProvider */
    public function test_empty_or_duplicate_selection_is_rejected(array $ids): void
    {
        $service = Mockery::mock(CarteirasClerigosService::class);
        $service->shouldNotReceive('listar');
        $this->expectException(ValidationException::class);
        (new CarteirasClerigosController)->pdf(Request::create('/', 'POST', ['clerigos' => $ids]), $service);
    }

    public function invalidSelectionProvider(): array
    {
        return [[[]], [[12, 12]], [['invalid']]];
    }

    /** @dataProvider pageProvider */
    public function test_pdf_preserves_portrait_layout_and_four_cards_per_page(int $count, int $pages): void
    {
        $qr = SimpleQrCode::pngDataUri(URL::signedRoute('validar-clerigo.show', ['clerigo' => 12]), 5);
        $carteiras = collect(range(1, $count))->map(fn ($id) => [
            'nome' => 'Clerigo Teste '.$id, 'cpf' => '52998224725', 'rol' => $id,
            'nome_regiao_formatado' => '6ª Região', 'foto_pdf' => null,
            'qr_code' => $qr, 'validacao_url' => URL::signedRoute('validar-clerigo.show', ['clerigo' => 12]),
            'telefone_sede_administrativa' => '(21) 99999-9999', 'superintendente_regional_nome' => 'Superintendente Teste',
        ]);
        $pdf = Pdf::loadView('clerigos.carteiras.pdf', [
            'carteiras' => $carteiras, 'background' => (new CarteirasClerigosService)->background(),
            'validade' => CredencialClerigo::VALIDADE,
        ])->setPaper('a4', 'portrait');
        $conteudo = $pdf->output();
        $this->assertStringStartsWith('%PDF-', $conteudo);
        $this->assertGreaterThanOrEqual(2, substr_count($conteudo, '/Subtype /Image'));
        $this->assertSame($pages, $pdf->getDomPDF()->getCanvas()->get_page_count());
    }

    public function pageProvider(): array
    {
        return [[1, 1], [4, 1], [5, 2]];
    }

    public function test_print_routes_use_existing_clergy_access_rule(): void
    {
        foreach (['clerigos.carteiras.index', 'clerigos.carteiras.pdf'] as $name) {
            $route = app('router')->getRoutes()->getByName($name);
            $this->assertContains('seguranca:menu-instituicoes', $route->gatherMiddleware());
        }
    }

    public function test_pdf_preparation_uses_shared_digital_wallet_data_and_signed_qr(): void
    {
        $regiao = session('session_perfil')->instituicoes->regiao;
        $contexto = Mockery::mock(ListPerfilService::class);
        $contexto->shouldReceive('contextoCarteira')->once()->with($regiao)->andReturn([
            'nome_regiao_formatado' => '6ª Região',
            'superintendente_regional_nome' => 'Superintendente Teste',
        ]);
        $this->app->instance(ListPerfilService::class, $contexto);
        $pessoa = (new PessoasPessoa)->forceFill(['id' => 12, 'nome' => 'Clerigo Teste', 'foto' => null]);
        $carteira = (new CarteirasClerigosService)->preparar(collect([$pessoa]), $regiao)->first();
        $this->assertSame('6ª Região', $carteira['nome_regiao_formatado']);
        $this->assertSame('Superintendente Teste', $carteira['superintendente_regional_nome']);
        $this->assertNull($carteira['foto_pdf']);
        $this->assertStringStartsWith('data:image/png;base64,', $carteira['qr_code']);
        $this->assertTrue(URL::hasValidSignature(Request::create($carteira['validacao_url'])));
        $this->assertSame('12', Request::create($carteira['validacao_url'])->query('clerigo'));
    }
}
