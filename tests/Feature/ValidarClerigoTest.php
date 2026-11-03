<?php

namespace Tests\Feature;

use App\Services\ServiceBase\GetBaseParamsService;
use Carbon\Carbon;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Mockery;
use Tests\TestCase;

class ValidarClerigoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        $base = Mockery::mock(GetBaseParamsService::class);
        $base->shouldReceive('execute')->andReturn((object) []);
        $this->app->instance(GetBaseParamsService::class, $base);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_valid_qr_link_shows_current_data_without_login(): void
    {
        $this->consulta($this->clerigo());
        $this->get(URL::signedRoute('validar-clerigo.show', ['clerigo' => 12]))
            ->assertOk()->assertSeeText('Clerigo Teste')->assertSeeText('Regiao Teste')
            ->assertSeeText('Ativo')->assertSeeText('31/10/2027')
            ->assertDontSeeText('CPF')->assertDontSeeText('Telefone')
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_inactive_registration_is_identified_instead_of_confirmed_active(): void
    {
        $this->consulta($this->clerigo('2026-11-01 10:00:00'));
        $this->get(URL::signedRoute('validar-clerigo.show', ['clerigo' => 12]))
            ->assertOk()->assertSeeText('Inativo')->assertSeeText('Consulte a administração regional');
    }

    public function test_unknown_clergy_has_clear_not_found_response(): void
    {
        $this->consulta(null);
        $this->get(URL::signedRoute('validar-clerigo.show', ['clerigo' => 12]))
            ->assertNotFound()->assertSeeText('Não foi possível localizar');
    }

    public function test_missing_signature_is_rejected_before_database_lookup(): void
    {
        DB::shouldReceive('table')->never();
        $this->get(route('validar-clerigo.show', ['clerigo' => 12]))->assertForbidden();
    }

    public function test_changing_the_clergy_id_invalidates_the_qr_link(): void
    {
        DB::shouldReceive('table')->never();
        $url = URL::signedRoute('validar-clerigo.show', ['clerigo' => 12]);
        $this->get(str_replace('clerigo=12', 'clerigo=13', $url))->assertForbidden();
    }

    public function test_expired_credential_is_identified(): void
    {
        Carbon::setTestNow(Carbon::parse('2027-11-01 12:00:00'));
        $this->consulta($this->clerigo());
        $this->get(URL::signedRoute('validar-clerigo.show', ['clerigo' => 12]))
            ->assertOk()->assertSeeText('Expirada');
    }

    private function clerigo($deletedAt = null): object
    {
        return (object) [
            'nome' => 'Clerigo Teste', 'rol' => 123, 'categoria' => 'pastor',
            'deleted_at' => $deletedAt, 'regiao_nome' => 'Regiao Teste', 'situacao_nome' => 'Ativo',
        ];
    }

    private function consulta(?object $clerigo): void
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        DB::shouldReceive('table')->with('pessoas_pessoas as pessoa')->once()
            ->andReturnUsing(fn ($table) => $connection->table($table));
        $connection->shouldReceive('select')->once()->andReturn($clerigo ? [$clerigo] : []);
    }
}
