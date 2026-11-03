<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePerfilRequest;
use App\Models\PerfilUser;
use App\Services\ServicePerfil\ListPerfilService;
use App\Services\ServicePerfil\UpdatePerfilService;
use App\Support\SimpleQrCode;
use App\Support\CredencialClerigo;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerfilController extends Controller
{
    public function index(Request $request) {
        $usuario = app(ListPerfilService::class)->execute();
        $perfisUsuarios = PerfilUser::with(['perfil', 'instituicao'])
        ->where('user_id', $usuario->id)
        ->get();

        return view('perfil.index', compact('usuario', 'perfisUsuarios'));
    }

    public function update(UpdatePerfilRequest $request, $id) {
        app(UpdatePerfilService::class)->execute($request, $id);
        return redirect()->route('perfil.index')->with('success', __('Perfil atualizado!'));
    }

    public function carteiraDigital(Request $request) {
        $usuario = app(ListPerfilService::class)->carteiraDigital();
        $validacaoUrl = null;
        $qrCode = null;
        if (!empty($usuario['pessoa_id'])) {
            $validacaoUrl = URL::signedRoute('validar-clerigo.show', ['clerigo' => $usuario['pessoa_id']]);
            try {
                $qrCode = SimpleQrCode::pngDataUri($validacaoUrl, 5);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return view('perfil.carteira-digital', [
            'usuario' => $usuario, 'qrCode' => $qrCode, 'validacaoUrl' => $validacaoUrl,
            'validadeCarteira' => \Carbon\Carbon::parse(CredencialClerigo::VALIDADE)->format('d/m/Y'),
        ]);
    }

    public function cartaoMembro(Request $request) {
        $membro = app(ListPerfilService::class)->cartaoMembro();
        if ($membro) {
            try {
                $membro->qr_code = SimpleQrCode::pngDataUri(route('validar-membro.show', ['membro' => $membro->id]), 4);
            } catch (\Throwable $e) {
                $membro->qr_code = null;
            }
        }

        return view('perfil.cartao-membro', ['membro' => $membro]);
    }

}
