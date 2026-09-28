<?php

namespace Tests\Unit\Services\ServiceMembrosGeral;

use App\Exceptions\RecadastramentoJaValidadoException;
use App\Services\ServiceMembrosGeral\UpdateMembroRecadastramentoService;
use Mockery;
use PHPUnit\Framework\TestCase;

class UpdateMembroRecadastramentoServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     */
    public function test_rejeita_recadastramento_ja_validado_depois_de_bloquear_o_registro(): void
    {
        $query = Mockery::mock();
        $query->shouldReceive('lockForUpdate')->once()->andReturnSelf();
        $query->shouldReceive('first')->once()->andReturn((object) ['validado' => true]);

        $model = Mockery::mock('alias:App\Models\MembresiaMembroRecadastramento');
        $model->shouldReceive('where')
            ->once()
            ->with('id', 'membro-migracao-id')
            ->andReturn($query);

        $this->expectException(RecadastramentoJaValidadoException::class);
        $this->expectExceptionMessage('Este recadastramento já foi validado.');

        (new UpdateMembroRecadastramentoService())->execute(
            ['membro_id' => 'membro-migracao-id'],
            'M'
        );
    }
}
