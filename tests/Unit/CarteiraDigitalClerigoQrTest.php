<?php

namespace Tests\Unit;

use App\Http\Controllers\PerfilController;
use App\Services\ServicePerfil\ListPerfilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Mockery;
use Tests\TestCase;

class CarteiraDigitalClerigoQrTest extends TestCase
{
    public function test_wallet_contains_png_qr_and_signed_validation_link(): void
    {
        $service = Mockery::mock(ListPerfilService::class);
        $service->shouldReceive('carteiraDigital')->once()->andReturn(['pessoa_id' => 12]);
        $this->app->instance(ListPerfilService::class, $service);
        $data = (new PerfilController)->carteiraDigital(Request::create('/'))->getData();

        $this->assertStringStartsWith('data:image/png;base64,', $data['qrCode']);
        $this->assertNotFalse(getimagesizefromstring(base64_decode(substr($data['qrCode'], strlen('data:image/png;base64,')))));
        $this->assertTrue(URL::hasValidSignature(Request::create($data['validacaoUrl'])));
        $this->assertSame('12', Request::create($data['validacaoUrl'])->query('clerigo'));
        $this->assertSame('31/10/2027', $data['validadeCarteira']);
    }

    public function test_non_clergy_user_does_not_receive_a_validation_link(): void
    {
        $service = Mockery::mock(ListPerfilService::class);
        $service->shouldReceive('carteiraDigital')->once()->andReturn(['pessoa_id' => null]);
        $this->app->instance(ListPerfilService::class, $service);
        $data = (new PerfilController)->carteiraDigital(Request::create('/'))->getData();
        $this->assertNull($data['qrCode']);
        $this->assertNull($data['validacaoUrl']);
    }
}
