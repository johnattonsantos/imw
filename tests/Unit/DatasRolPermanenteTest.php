<?php

namespace Tests\Unit;

use App\Support\DatasRolPermanente;
use Carbon\Carbon;
use Tests\TestCase;

class DatasRolPermanenteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-10-14 12:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** @dataProvider historyProvider */
    public function test_action_dates_preserve_history_and_stamp_new_events($recepcao, $exclusao, bool $inativo, bool $novo, array $esperado): void
    {
        $this->assertSame($esperado, DatasRolPermanente::paraRegistro($recepcao, $exclusao, $inativo, $novo));
    }

    public function historyProvider(): array
    {
        return [
            'new member' => [null, null, false, false, ['dt_recepcao' => '2026-10-14', 'dt_exclusao' => null]],
            'personal data edit' => ['2001-09-01', null, false, false, ['dt_recepcao' => '2001-09-01', 'dt_exclusao' => null]],
            'new exclusion' => ['2024-08-29', null, true, false, ['dt_recepcao' => '2024-08-29', 'dt_exclusao' => '2026-10-14']],
            'inactive data edit' => ['2001-09-01', '2024-08-29', true, false, ['dt_recepcao' => '2001-09-01', 'dt_exclusao' => '2024-08-29']],
            'reintegration creates new reception' => ['2001-09-01', '2024-08-29', false, true, ['dt_recepcao' => '2026-10-14', 'dt_exclusao' => null]],
            'validation creates inactive record' => ['2001-09-01', '2024-08-29', true, true, ['dt_recepcao' => '2026-10-14', 'dt_exclusao' => '2026-10-14']],
        ];
    }

    /** @dataProvider requestProvider */
    public function test_action_requests_ignore_forged_and_missing_dates(string $class, string $campo): void
    {
        $request = new $class;
        $method = new \ReflectionMethod($class, 'prepareForValidation');
        $method->setAccessible(true);
        foreach (['2001-01-01', '2099-01-01', null] as $value) {
            $request->merge([$campo => $value]);
            $method->invoke($request);
            $this->assertSame('2026-10-14', $request->input($campo));
        }
    }

    public function requestProvider(): array
    {
        return [
            [\App\Http\Requests\StoreReceberNovoMembroRequest::class, 'dt_recepcao'],
            [\App\Http\Requests\StoreReintegracaoRequest::class, 'dt_recepcao'],
            [\App\Http\Requests\StoreExclusaoPorTransferenciaRequest::class, 'dt_notificacao'],
            [\App\Http\Requests\StoreTransferenciaInternaRequest::class, 'dt_transferencia'],
            [\App\Http\Requests\StoreReceberMembroExternoRequest::class, 'dt_resposta'],
            [\App\Http\Requests\DeletarMembroRequest::class, 'dt_exclusao'],
        ];
    }

    public function test_reception_and_reintegration_services_ignore_forged_dates(): void
    {
        session(['session_perfil' => (object) ['instituicoes' => (object) [
            'regiao' => (object) ['id' => 1], 'distrito' => (object) ['id' => 2], 'igrejaLocal' => (object) ['id' => 3],
        ]]]);
        foreach ([
            [\App\Services\ServiceMembros\StoreReceberNovoMembroService::class, 'fetchCreateParams'],
            [\App\Services\ServiceMembros\StoreReintegracaoService::class, 'fetchParams'],
        ] as [$class, $metodo]) {
            $method = new \ReflectionMethod($class, $metodo);
            $method->setAccessible(true);
            $params = $method->invoke(new $class, ['dt_recepcao' => '2001-01-01', 'dt_exclusao' => '2002-01-01']);
            $this->assertSame('2026-10-14', $params['dt_recepcao']);
            $this->assertNull($params['dt_exclusao']);
        }
    }
}
