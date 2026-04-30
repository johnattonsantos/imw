<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/cep/{cep}', function (string $cep) {
    $cep = preg_replace('/\D/', '', $cep);

    if (strlen($cep) !== 8) {
        return response()->json([
            'erro' => true,
            'message' => 'Formato de CEP inválido.',
        ], 422);
    }

    try {
        $response = Http::timeout(8)->acceptJson()->get("https://viacep.com.br/ws/{$cep}/json/");

        if (!$response->ok()) {
            return response()->json([
                'erro' => true,
                'message' => 'Falha ao consultar o serviço de CEP.',
            ], 502);
        }

        return response()->json($response->json());
    } catch (\Throwable $e) {
        return response()->json([
            'erro' => true,
            'message' => 'Não foi possível consultar o serviço de CEP.',
        ], 502);
    }
})->name('api.cep.show');
