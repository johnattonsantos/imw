<?php

use App\Http\Controllers\Patrimonio\PatrimonioBaixasController;
use App\Http\Controllers\Patrimonio\PatrimonioBenfeitoriasController;
use App\Http\Controllers\Patrimonio\PatrimonioBensImoveisController;
use App\Http\Controllers\Patrimonio\PatrimonioBensMoveisController;
use App\Http\Controllers\Patrimonio\PatrimonioDashboardController;
use App\Http\Controllers\Patrimonio\PatrimonioDocumentosController;
use App\Http\Controllers\Patrimonio\PatrimonioConfiguracoesController;
use App\Http\Controllers\Patrimonio\PatrimonioRelatoriosController;
use App\Http\Controllers\Patrimonio\PatrimonioRiscosJuridicosController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('patrimonio')->name('patrimonio.')->group(function () {
    Route::get('/', [PatrimonioDashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware(['seguranca:patrimonio-dashboard']);

    Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
        Route::get('/', [PatrimonioConfiguracoesController::class, 'hub'])
            ->name('hub')
            ->middleware(['seguranca:patrimonio.visualizar']);

        Route::get('{tipo}', [PatrimonioConfiguracoesController::class, 'index'])
            ->name('tipos.index')
            ->whereIn('tipo', PatrimonioConfiguracoesController::tiposPermitidos())
            ->middleware(['seguranca:patrimonio.visualizar']);

        Route::get('{tipo}/create', [PatrimonioConfiguracoesController::class, 'create'])
            ->name('tipos.create')
            ->whereIn('tipo', PatrimonioConfiguracoesController::tiposPermitidos())
            ->middleware(['seguranca:patrimonio.criar']);

        Route::post('{tipo}', [PatrimonioConfiguracoesController::class, 'store'])
            ->name('tipos.store')
            ->whereIn('tipo', PatrimonioConfiguracoesController::tiposPermitidos())
            ->middleware(['seguranca:patrimonio.criar']);

        Route::get('{tipo}/{configuracao}/edit', [PatrimonioConfiguracoesController::class, 'edit'])
            ->name('tipos.edit')
            ->whereIn('tipo', PatrimonioConfiguracoesController::tiposPermitidos())
            ->middleware(['seguranca:patrimonio.editar']);

        Route::put('{tipo}/{configuracao}', [PatrimonioConfiguracoesController::class, 'update'])
            ->name('tipos.update')
            ->whereIn('tipo', PatrimonioConfiguracoesController::tiposPermitidos())
            ->middleware(['seguranca:patrimonio.editar']);

        Route::delete('{tipo}/{configuracao}', [PatrimonioConfiguracoesController::class, 'destroy'])
            ->name('tipos.destroy')
            ->whereIn('tipo', PatrimonioConfiguracoesController::tiposPermitidos())
            ->middleware(['seguranca:patrimonio.excluir']);
    });

    Route::resource('bens-imoveis', PatrimonioBensImoveisController::class)
        ->only(['create', 'store'])
        ->names('bens-imoveis')
        ->parameters(['bens-imoveis' => 'bemImovel'])
        ->middleware(['seguranca:patrimonio-bens-imoveis']);
    Route::resource('bens-imoveis', PatrimonioBensImoveisController::class)
        ->only(['index', 'show'])
        ->names('bens-imoveis')
        ->parameters(['bens-imoveis' => 'bemImovel'])
        ->middleware(['seguranca:patrimonio-bens-imoveis']);
    Route::resource('bens-imoveis', PatrimonioBensImoveisController::class)
        ->only(['edit', 'update'])
        ->names('bens-imoveis')
        ->parameters(['bens-imoveis' => 'bemImovel'])
        ->middleware(['seguranca:patrimonio-bens-imoveis']);
    Route::resource('bens-imoveis', PatrimonioBensImoveisController::class)
        ->only(['destroy'])
        ->names('bens-imoveis')
        ->parameters(['bens-imoveis' => 'bemImovel'])
        ->middleware(['seguranca:patrimonio-bens-imoveis']);

    Route::resource('bens-moveis', PatrimonioBensMoveisController::class)
        ->only(['create', 'store'])
        ->names('bens-moveis')
        ->parameters(['bens-moveis' => 'bemMovel'])
        ->middleware(['seguranca:patrimonio-bens-moveis']);
    Route::resource('bens-moveis', PatrimonioBensMoveisController::class)
        ->only(['index', 'show'])
        ->names('bens-moveis')
        ->parameters(['bens-moveis' => 'bemMovel'])
        ->middleware(['seguranca:patrimonio-bens-moveis']);
    Route::resource('bens-moveis', PatrimonioBensMoveisController::class)
        ->only(['edit', 'update'])
        ->names('bens-moveis')
        ->parameters(['bens-moveis' => 'bemMovel'])
        ->middleware(['seguranca:patrimonio-bens-moveis']);
    Route::resource('bens-moveis', PatrimonioBensMoveisController::class)
        ->only(['destroy'])
        ->names('bens-moveis')
        ->parameters(['bens-moveis' => 'bemMovel'])
        ->middleware(['seguranca:ppatrimonio-bens-moveis']);

    Route::get('documentos/{documento}/download', [PatrimonioDocumentosController::class, 'download'])
        ->name('documentos.download')
        ->middleware(['seguranca:patrimonio-documentos', 'patrimonio-documentos']);
    Route::resource('documentos', PatrimonioDocumentosController::class)
        ->only(['create', 'store'])
        ->names('documentos')
        ->parameters(['documentos' => 'documento'])
        ->middleware(['seguranca:patrimonio-documentos', 'seguranca:patrimonio-documentos']);
    Route::resource('documentos', PatrimonioDocumentosController::class)
        ->only(['index', 'show'])
        ->names('documentos')
        ->parameters(['documentos' => 'documento'])
        ->middleware(['seguranca:patrimonio-documentos', 'patrimonio-documentos']);
    Route::resource('documentos', PatrimonioDocumentosController::class)
        ->only(['edit', 'update'])
        ->names('documentos')
        ->parameters(['documentos' => 'documento'])
        ->middleware(['seguranca:patrimonio-documentos', 'seguranca:patrimonio-documentos']);
    Route::resource('documentos', PatrimonioDocumentosController::class)
        ->only(['destroy'])
        ->names('documentos')
        ->parameters(['documentos' => 'documento'])
        ->middleware(['seguranca:patrimonio-documentos', 'seguranca:patrimonio-documentos']);

    Route::resource('riscos-juridicos', PatrimonioRiscosJuridicosController::class)
        ->only(['create', 'store'])
        ->names('riscos-juridicos')
        ->parameters(['riscos-juridicos' => 'riscoJuridico'])
        ->middleware(['seguranca:patrimonio.juridico', 'seguranca:patrimonio.criar']);
    Route::resource('riscos-juridicos', PatrimonioRiscosJuridicosController::class)
        ->only(['index', 'show'])
        ->names('riscos-juridicos')
        ->parameters(['riscos-juridicos' => 'riscoJuridico'])
        ->middleware(['seguranca:patrimonio.juridico', 'seguranca:patrimonio.visualizar']);
    Route::resource('riscos-juridicos', PatrimonioRiscosJuridicosController::class)
        ->only(['edit', 'update'])
        ->names('riscos-juridicos')
        ->parameters(['riscos-juridicos' => 'riscoJuridico'])
        ->middleware(['seguranca:patrimonio.juridico', 'seguranca:patrimonio.editar']);
    Route::resource('riscos-juridicos', PatrimonioRiscosJuridicosController::class)
        ->only(['destroy'])
        ->names('riscos-juridicos')
        ->parameters(['riscos-juridicos' => 'riscoJuridico'])
        ->middleware(['seguranca:patrimonio.juridico', 'seguranca:patrimonio.excluir']);

    Route::get('benfeitorias/{benfeitoria}/download', [PatrimonioBenfeitoriasController::class, 'download'])
        ->name('benfeitorias.download')
        ->middleware(['seguranca:patrimonio-benfeitoria']);
    Route::resource('benfeitorias', PatrimonioBenfeitoriasController::class)
        ->only(['create', 'store'])
        ->names('benfeitorias')
        ->parameters(['benfeitorias' => 'benfeitoria'])
        ->middleware(['seguranca:patrimonio-benfeitoria']);
    Route::resource('benfeitorias', PatrimonioBenfeitoriasController::class)
        ->only(['index', 'show'])
        ->names('benfeitorias')
        ->parameters(['benfeitorias' => 'benfeitoria'])
        ->middleware(['seguranca:patrimonio-benfeitoria']);
    Route::resource('benfeitorias', PatrimonioBenfeitoriasController::class)
        ->only(['edit', 'update'])
        ->names('benfeitorias')
        ->parameters(['benfeitorias' => 'benfeitoria'])
        ->middleware(['seguranca:patrimonio-benfeitoria']);
    Route::resource('benfeitorias', PatrimonioBenfeitoriasController::class)
        ->only(['destroy'])
        ->names('benfeitorias')
        ->parameters(['benfeitorias' => 'benfeitoria'])
        ->middleware(['seguranca:patrimonio-benfeitoria']);

    Route::get('baixas/{baixa}/download', [PatrimonioBaixasController::class, 'download'])
        ->name('baixas.download')
        ->middleware(['seguranca:patrimonio-baixa', 'seguranca:patrimonio-baixa']);
    Route::resource('baixas', PatrimonioBaixasController::class)
        ->only(['create', 'store'])
        ->names('baixas')
        ->parameters(['baixas' => 'baixa'])
        ->middleware(['seguranca:patrimonio-baixa', 'patrimonio-baixa']);
    Route::resource('baixas', PatrimonioBaixasController::class)
        ->only(['index', 'show'])
        ->names('baixas')
        ->parameters(['baixas' => 'baixa'])
        ->middleware(['seguranca:patrimonio-baixa', 'seguranca:patrimonio-baixa']);
    Route::resource('baixas', PatrimonioBaixasController::class)
        ->only(['edit', 'update'])
        ->names('baixas')
        ->parameters(['baixas' => 'baixa'])
        ->middleware(['seguranca:patrimonio-baixa', 'seguranca:patrimonio-baixa']);
    Route::resource('baixas', PatrimonioBaixasController::class)
        ->only(['destroy'])
        ->names('baixas')
        ->parameters(['baixas' => 'baixa'])
        ->middleware(['seguranca:patrimonio-baixa', 'patrimonio-baixa']);

    Route::get('/relatorios', [PatrimonioRelatoriosController::class, 'index'])
        ->name('relatorios.index')
        ->middleware(['seguranca:patrimonio-relatorios']);
    Route::get('/relatorios/{relatorio}', [PatrimonioRelatoriosController::class, 'lista'])
        ->name('relatorios.lista')
        ->whereIn('relatorio', [
            'imoveis_cadastrados',
            'bens_moveis_cadastrados',
            'imoveis_regularizacao_pendente',
            'documentos_vencidos',
            'avcb_vencido',
            'bens_depreciados',
            'baixas_patrimoniais',
            'valor_total_por_categoria',
            'bens_por_igreja_unidade',
        ])
        ->middleware(['seguranca:patrimonio-relatorios']);
    Route::get('/relatorios/export/xlsx', [PatrimonioRelatoriosController::class, 'exportXlsx'])
        ->name('relatorios.export.xlsx')
        ->middleware(['seguranca:patrimonio-relatorios']);
    Route::get('/relatorios/export/pdf', [PatrimonioRelatoriosController::class, 'exportPdf'])
        ->name('relatorios.export.pdf')
        ->middleware(['seguranca:patrimonio-relatorios']);
});
