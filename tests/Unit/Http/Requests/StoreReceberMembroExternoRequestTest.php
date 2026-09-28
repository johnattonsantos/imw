<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\StoreReceberMembroExternoRequest;
use App\Models\NotificacaoTransferencia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreReceberMembroExternoRequestTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_rejeita_data_futura_no_recebimento(): void
    {
        $validator = $this->validatorFor('2026-11-28');

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('dt_resposta', $validator->errors()->toArray());
    }

    public function test_rejeita_data_anterior_a_transferencia(): void
    {
        $validator = $this->validatorFor('2026-09-19');

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('dt_resposta', $validator->errors()->toArray());
    }

    public function test_aceita_data_entre_a_transferencia_e_hoje(): void
    {
        $validator = $this->validatorFor('2026-09-28');

        $this->assertFalse($validator->fails());
    }

    private function validatorFor(string $dataResposta)
    {
        Carbon::setTestNow('2026-09-28');

        $notificacao = new NotificacaoTransferencia();
        $notificacao->membro_id = 'membro-id';
        $notificacao->dt_abertura = '2026-09-20';

        $request = new StoreReceberMembroExternoRequest();
        $request->setRouteResolver(fn () => new class($notificacao)
        {
            public function __construct(private NotificacaoTransferencia $notificacao) {}

            public function parameter(string $name)
            {
                return $name === 'notificacao' ? $this->notificacao : null;
            }
        });

        return Validator::make(
            ['dt_resposta' => $dataResposta],
            ['dt_resposta' => $request->rules()['dt_resposta']],
            $request->messages()
        );
    }
}
