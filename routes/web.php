<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ClerigosRegiaoController;
use App\Http\Controllers\CongregacoesController;
use App\Http\Controllers\CongregadosController;
use App\Http\Controllers\ClerigoPerfilController;
use App\Http\Controllers\ComunicacaoController;
use App\Http\Controllers\ContabilidadeController;
use App\Http\Controllers\DistritoRelatorioController;
use App\Http\Controllers\FinanceiroCaixasController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\FinanceiroPlanoContaController;
use App\Http\Controllers\FinanceiroRelatorioController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\GceuController;
use App\Http\Controllers\HandleInstituicoesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IgrejasController;
use App\Http\Controllers\IgrejasRegiaoController;
use App\Http\Controllers\ImpostoDeRendaController;
use App\Http\Controllers\InformeRendimentosController;
use App\Http\Controllers\InstituicaoController;
use App\Http\Controllers\InstituicaoRegiaoController;
use App\Http\Controllers\InstituicaoRegiaoDistritosController;
use App\Http\Controllers\InstituicaoRegiaoIgrejasController;
use App\Http\Controllers\InstituicaoRegiaoSecretariasController;
use App\Http\Controllers\MembresiaGeralController;
use App\Http\Controllers\MembrosController;
use App\Http\Controllers\NomeacoesClerigosController;
use App\Http\Controllers\NotificacoesTranferenciaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PrebendaController;
use App\Http\Controllers\PrebendasClerigosController;
use App\Http\Controllers\RegiaoEstatisticasController;
use App\Http\Controllers\RegiaoRelatorioController;
use App\Http\Controllers\RelatorioClerigoPrebendasController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\TotalizacaoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VisitantesController;
use App\Http\Middleware\VerificaInstituicao;
use App\Http\Middleware\VerificaPerfil;
use Illuminate\Support\Facades\Route;

// Rotas de autenticação

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rota para mostrar o formulário de esqueci a senha
Route::get('/esqueci-senha', [AuthController::class, 'showResetRequestForm'])->name('password.request');

// Rota para enviar o link de redefinição de senha
Route::post('/esqueci-senha', [AuthController::class, 'submitResetRequest'])->name('password.email');

// Rota para processar a redefinição de senha (GET)
Route::get('/redefinir-senha', [AuthController::class, 'showResetForm'])->name('password.reset');

// Rota para processar a redefinição de senha
Route::post('/redefinir-senha', [AuthController::class, 'reset'])->name('password.reset-post');


// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {
    Route::middleware([VerificaPerfil::class])->group(function () {

        //Estas duas rotas estão com VerificaPerfil Middleware desabilitados
        Route::get('/selecionarPerfil', [HomeController::class, 'selecionarPerfil'])->withoutMiddleware([VerificaPerfil::class])->name('selecionarPerfil');
        Route::post('/postPerfil', [HomeController::class, 'postPerfil'])->withoutMiddleware([VerificaPerfil::class])->name('postPerfil');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index')->middleware(['seguranca:admin-index']);
            Route::get('/novo', [AdminController::class, 'novo'])->name('novo')->middleware(['seguranca:usuarios-cadastrar']);
            Route::post('/update/{id}', [AdminController::class, 'update'])->name('update')->middleware(['seguranca:usuarios-atualizar']);
            Route::post('/store', [AdminController::class, 'store'])->name('store')->middleware(['seguranca:usuarios-cadastrar']);
            Route::delete('/deletar/{id}', [AdminController::class, 'deletar'])->name('deletar')->middleware(['seguranca:usuarios-excluir']);
            Route::get('/editar/{id}', [AdminController::class, 'editar'])->name('editar')->middleware(['seguranca:usuarios-editar']);
        });


        // Grupo de rotas para 'membresia'
        Route::prefix('secretaria/membro')->name('membro.')->group(function () {
            Route::get('', [MembrosController::class, 'index'])->name('index')->middleware(['seguranca:membros-index']);
            Route::get('list', [MembrosController::class, 'list'])->name('list')->middleware(['seguranca:membros-index']);
            Route::get('editar/{id}', [MembrosController::class, 'editar'])->name('editar')->middleware(['seguranca:membros-editar'])->can('checkSameChurch', [\App\Models\MembresiaMembro::class, 'id']);
            Route::get('receber-novo/{id}', [MembrosController::class, 'receberNovo'])->name('receber_novo')->middleware(['seguranca:membros-recebernovo']);
            Route::post('receber-novo-store/{id}', [MembrosController::class, 'storeReceberNovo'])->name('receber_novo.store')->middleware(['seguranca:membros-recebernovo']);
            Route::post('atualizar/{id}', [MembrosController::class, 'update'])->name('update')->middleware(['seguranca:membros-atualizar']);
            Route::get('exclusao/{id}', [MembrosController::class, 'exclusao'])->name('exclusao')->middleware(['seguranca:membros-excluir']);
            Route::delete('exclusao/store/{id}', [MembrosController::class, 'storeExclusao'])->name('exclusao.store')->middleware(['seguranca:membros-excluir']);
            Route::get('reintegrar/{id}', [MembrosController::class, 'reintegrar'])->name('reintegrar')->middleware(['seguranca:membros-reintegrar']);
            Route::post('reintegrar/store/{id}', [MembrosController::class, 'storeReintegracao'])->name('reintegrar.store')->middleware(['seguranca:membros-cadastrar']);
            Route::get('transferencia/interna/{id}', [MembrosController::class, 'transferenciaInterna'])->name('transferencia_interna')->middleware(['seguranca:membros-transferenciainterna']);
            Route::post('transferencia/interna/store/{id}', [MembrosController::class, 'storeTransferenciaInterna'])->name('transferencia_interna.store')->middleware(['seguranca:membros-transferenciainterna']);
            Route::get('exclusao/transferencia/{id}', [MembrosController::class, 'exclusaoPorTransferencia'])->name('exclusao_transferencia')->middleware(['seguranca:membros-exclusaotransferencia']);
            Route::post('exclusao/transferencia/store/{id}', [MembrosController::class, 'storeExclusaoPorTransferencia'])->name('exclusao_transferencia.store')->middleware(['seguranca:membros-exclusaotransferencia']);
            Route::delete('exclusao/transferencia/cancel/{notificacaoTransferencia}', [MembrosController::class, 'cancelExclusaoPorTransferencia'])->name('exclusao_transferencia.cancel')->middleware(['seguranca:membros-exclusaotransferencia']);
            Route::get('receber-membro-externo/{notificacao}', [MembrosController::class, 'receberMembroExterno'])->name('receber_membro_externo')->middleware(['seguranca:membros-recebermembroexterno']);
            Route::post('receber-membro-externo/store/{notificacao}', [MembrosController::class, 'storeReceberMembroExterno'])->name('receber_membro_externo.store')->middleware(['seguranca:membros-recebermembroexterno']);
            Route::get('disciplinar/{id}', [MembrosController::class, 'disciplinar'])->name('disciplinar')->middleware(['seguranca:membros-disciplinar'])->can('checkSameChurch', [\App\Models\MembresiaMembro::class, 'id']);
            Route::post('disciplinar/store/{id}', [MembrosController::class, 'storeDisciplinar'])->name('disciplinar.store')->middleware(['seguranca:membros-disciplinar']);
            Route::put('disciplinar/update/{id}', [MembrosController::class, 'updateDisciplinar'])->name('disciplinar.update')->middleware(['seguranca:membros-disciplinar']);
        });

        // Grupo de rotas para 'membresia recadastramento'
        Route::prefix('secretaria/recadastramento-membro')->name('recadastramento-membro.')->group(function () {
            Route::get('', [MembrosController::class, 'indexRecadastramento'])->name('indexRecadastramento')->middleware(['seguranca:membros-index']);
            Route::get('list-recadastramento', [MembrosController::class, 'listRecadastramento'])->name('listRecadastramento')->middleware(['seguranca:membros-index']);
            Route::get('editar/{id}', [MembrosController::class, 'editarRecadastramento'])->name('editar')->middleware(['seguranca:membros-editar']);
            Route::post('atualizar/{id}', [MembrosController::class, 'updateRecadastramento'])->name('update')->middleware(['seguranca:membros-atualizar']);
        });

        Route::controller(HomeController::class)->group(function () {
            Route::get('/', 'dashboard')->name('dashboard');
            Route::get('perfil', 'perfil')->name('perfil');
        });

        Route::prefix('usuario/perfis')->name('perfil.')->group(function () {
            Route::get('/', [PerfilController::class, 'index'])->name('index');
            Route::post('/perfil/{id}', [PerfilController::class, 'update'])->name('update');
        });

        Route::prefix('usuario/perfil')->name('perfil.')->group(function () {
            Route::get('/carteira-digital', [PerfilController::class, 'carteiraDigital'])->name('carteira-digital');/*->middleware(['seguranca:carteira-digital']);*/
        });

        Route::prefix('instituicoes')->name('instituicoes.')->group(function () {
            Route::get('/', [InstituicaoController::class, 'index']);
        });

        Route::prefix('instituicoesLocais')->name('instituicoesLocais.')->group(function () {
            Route::get('/', [InstituicaoController::class, 'instituicoesLocais']);
        });

        // Grupo de rotas para 'visitantes'
        Route::prefix('secretaria/visitante')->name('visitante.')->group(function () {
            Route::get('/', [VisitantesController::class, 'index'])->name('index')->middleware(['seguranca:visitantes-index']);
            Route::get('list', [VisitantesController::class, 'list'])->name('list')->middleware(['seguranca:visitantes-index']);
            Route::get('/novo', [VisitantesController::class, 'novo'])->name('novo')->middleware(['seguranca:visitantes-cadastrar']);
            Route::post('/salvar', [VisitantesController::class, 'store'])->name('store')->middleware(['seguranca:visitantes-cadastrar']);
            Route::get('/editar/{id}', [VisitantesController::class, 'editar'])->name('editar')->middleware(['seguranca:visitantes-atualizar'])->can('checkSameChurch', [\App\Models\MembresiaMembro::class, 'id']);
            Route::post('/visitante/{id}', [VisitantesController::class, 'update'])->name('update')->middleware(['seguranca:visitantes-atualizar']);
            Route::post('/deletar/{id}', [VisitantesController::class, 'deletar'])->name('deletar')->middleware(['seguranca:visitantes-excluir']);
        });

        // Grupo de rotas para 'congregado'
        Route::prefix('secretaria/congregado')->name('congregado.')->group(function () {
            Route::get('/', [CongregadosController::class, 'index'])->name('index')->middleware(['seguranca:congregados-index']);
            Route::get('list', [CongregadosController::class, 'list'])->name('list')->middleware(['seguranca:congregados-index']);
            Route::get('/novo', [CongregadosController::class, 'novo'])->name('novo')->middleware(['seguranca:congregados-cadastrar']);
            Route::post('/update', [CongregadosController::class, 'update'])->name('update')->middleware(['seguranca:congregados-atualizar']);
            Route::post('/store', [CongregadosController::class, 'store'])->name('store')->middleware(['seguranca:congregados-cadastrar']);
            Route::delete('/deletar/{id}', [CongregadosController::class, 'deletar'])->name('deletar')->middleware(['seguranca:congregados-excluir']);
            Route::get('/editar/{id}', [CongregadosController::class, 'editar'])->name('editar')->middleware(['seguranca:congregados-editar'])->can('checkSameChurch', [\App\Models\MembresiaMembro::class, 'id']);
        });

        Route::prefix('membresia-geral')->name('membresia-geral.')->group(function () {
            Route::get('visualizar-html/{membro}', [MembresiaGeralController::class, 'visualizarHtml'])->name('visualizar-html');
        });

        Route::prefix('notificacoes-tranferencia')->name('notificacoes-tranferencia.')->controller(NotificacoesTranferenciaController::class)->group(function () {
            Route::get('', 'index')->name('index');
        });

        Route::prefix('comunicacao')->name('comunicacao.')->controller(ComunicacaoController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/novo', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/detalhes/{comunicacao}', 'show')->name('show');
            Route::get('/editar/{comunicacao}', 'edit')->name('edit');
            Route::put('/update/{comunicacao}', 'update')->name('update');
            Route::delete('/deletar/{comunicacao}', 'destroy')->name('destroy');
            Route::get('/download/{comunicacao}', 'download')->name('download');
            Route::get('/visualizar/{comunicacao}', 'visualizar')->name('visualizar');
            Route::get('/export/xlsx', 'exportXlsx')->name('export.xlsx');
            Route::get('/export/pdf', 'exportPdf')->name('export.pdf');
        });

        /* Por enquanto somente visualiações */
        Route::prefix('financeiro')->name('financeiro.')->group(function () {
            Route::get('/movimento-caixa', [FinanceiroController::class, 'movimentocaixa'])->name('movimento.caixa')->middleware(['seguranca:financeiro-movimentocaixa-index']);
            Route::get('/consolidar-caixa', [FinanceiroController::class, 'consolidarcaixa'])->name('consolidar.caixa')->middleware(['seguranca:financeiro-consolidarcaixa']);
            Route::post('/consolidar/store', [FinanceiroController::class, 'consolidarstore'])->name('consolidar.store')->middleware(['seguranca:financeiro-caixas']);
            Route::get('/entrada', [FinanceiroController::class, 'entrada'])->name('entrada')->middleware(['seguranca:financeiro-caixas']);
            Route::post('/entrada/store', [FinanceiroController::class, 'storeEntrada'])->name('entrada.store')->middleware(['seguranca:financeiro-caixas']);
            Route::put('/entrada/{id}', [FinanceiroController::class, 'updateEntrada'])->name('entrada.update')->middleware(['seguranca:financeiro-caixas']);
            Route::get('/saida', [FinanceiroController::class, 'saida'])->name('saida')->middleware(['seguranca:financeiro-caixas']);
            Route::put('/saida/{id}', [FinanceiroController::class, 'updateSaida'])->name('saida.update')->middleware(['seguranca:financeiro-caixas']);
            Route::post('/saida/store', [FinanceiroController::class, 'storeSaida'])->name('saida.store')->middleware(['seguranca:financeiro-caixas']);
            Route::get('/transferencia', [FinanceiroController::class, 'transferencia'])->name('transferencia')->middleware(['seguranca:financeiro-caixas']);
            Route::post('/transferencia/store', [FinanceiroController::class, 'storeTransferencia'])->name('transferencia.store')->middleware(['seguranca:financeiro-caixas']);
            Route::get('/saldo', [FinanceiroController::class, 'saldo'])->name('saldo')->middleware(['seguranca:financeiro-caixas']);
            Route::get('/editar-movimento/{id}/{tipo_lancamento}', [FinanceiroController::class, 'editarMovimento'])->name('editarMovimento')->middleware(['seguranca:financeiro-caixas']);
            Route::delete('/excluir-movimento/{id}', [FinanceiroController::class, 'excluirMovimento'])->name('excluirMovimento')->middleware(['seguranca:financeiro-caixas']);
            Route::get('/anexos/{id_lancamento}', [FinanceiroController::class, 'buscarAnexos'])->name('buscarAnexos')->middleware(['seguranca:financeiro-caixas']);
            Route::get('/anexos/html/{lancamento}', [FinanceiroController::class, 'htmlManipularAnexos'])->name('htmlManipularAnexos')->middleware(['seguranca:financeiro-caixas']);
            Route::get('/anexos/download/{anexo}', [FinanceiroController::class, 'downloadAnexo'])->name('downloadAnexo')->middleware(['seguranca:financeiro-caixas']);
            Route::post('/anexos/store/{lancamento}', [FinanceiroController::class, 'storeNewAnexo'])->name('storeNewAnexo')->middleware(['seguranca:financeiro-caixas']);
            Route::delete('/anexos/{anexo}', [FinanceiroController::class, 'deleteAnexo'])->name('anexo.delete')->middleware(['seguranca:financeiro-caixas']);
            //Plano de Conta
            Route::get('/plano-conta', [FinanceiroPlanoContaController::class, 'index'])->name('plano.conta')->middleware(['seguranca:financeiro-planoconta']);

            //Caixas
            Route::get('/caixas', [FinanceiroCaixasController::class, 'index'])->name('caixas')->middleware(['seguranca:financeiro-caixas']);
            Route::get('/caixas/novo', [FinanceiroCaixasController::class, 'novo'])->name('caixas.novo')->middleware(['seguranca:financeiro-caixas']);
            Route::post('/caixas/store', [FinanceiroCaixasController::class, 'store'])->name('caixas.store')->middleware(['seguranca:financeiro-caixas']);
            Route::delete('/caixas/deletar/{id}', [FinanceiroCaixasController::class, 'deletar'])->name('caixas.deletar')->middleware(['seguranca:financeiro-caixas']);
            Route::get('/editar/{id}', [FinanceiroCaixasController::class, 'editar'])->name('caixas.editar')->middleware(['seguranca:financeiro-caixas']);
            Route::post('/update/{id}', [FinanceiroCaixasController::class, 'update'])->name('caixas.update')->middleware(['seguranca:financeiro-caixas']);

            //Orcamentos
            Route::get('/cota-orcamentaria', [FinanceiroController::class, 'CotaOrcamentaria'])->name('cota.orcamentaria')->middleware(['seguranca:cota-orcamentaria']);

            //Relatorios
            Route::get('/relatorio/movimento-diario', [FinanceiroRelatorioController::class, 'movimentodiario'])->name('relatorio-movimento-diario')->middleware(['seguranca:menu-relatorios']);
            Route::get('/relatorio/movimento-diario/pdf', [FinanceiroRelatorioController::class, 'movimentoDiarioPdf'])->name('relatorio-movimento-diario-pdf')->middleware(['seguranca:menu-relatorios']);

            Route::get('/relatorio/livrorazao', [FinanceiroRelatorioController::class, 'livrorazao'])->name('relatorio-livrorazao')->middleware(['seguranca:menu-relatorios']);
            Route::get('/relatorio/livrocaixa', [FinanceiroRelatorioController::class, 'livrocaixa'])->name('relatorio-livrocaixa')->middleware(['seguranca:menu-relatorios']);
            Route::get('/relatorio/livrocaixa/pdf', [FinanceiroRelatorioController::class, 'livrocaixaPdf'])->name('relatorio-livrocaixa.pdf')->middleware(['seguranca:menu-relatorios']);
            Route::get('/relatorio/livrograde', [FinanceiroRelatorioController::class, 'livrograde'])->name('relatorio-livrograde')->middleware(['seguranca:financeiro-relatorio-livrograde']);
            Route::post('/relatorio/livrogradepost', [FinanceiroRelatorioController::class, 'livrogradepost'])->name('livrogradepost')->middleware(['seguranca:financeiro-relatorio-livrograde']);
            Route::post('/relatorio/livrograde/store', [FinanceiroRelatorioController::class, 'livrogradestore'])->name('livrograde.store')->middleware(['seguranca:financeiro-relatorio-livrograde']);
            Route::get('/relatorio/balancete', [FinanceiroRelatorioController::class, 'balancete'])->name('relatorio-balancete')->middleware(['seguranca:menu-relatorios']);
            Route::get('/relatorio/balancete-pdf', [FinanceiroRelatorioController::class, 'balancetePdf'])->name('relatorio-balancete-pdf')->middleware(['seguranca:menu-relatorios']);
            Route::get('/relatorio/livrorazao', [FinanceiroRelatorioController::class, 'livroRazao'])->name('relatorio-livrorazao')->middleware(['seguranca:menu-relatorios']);
            Route::get('/relatorio/livrorazao/pdf', [FinanceiroRelatorioController::class, 'livrorazaoPdf'])->name('relatorio-livrorazao.pdf')->middleware(['seguranca:menu-relatorios']);
            Route::get('/relatorio/movimento-bancario', [FinanceiroRelatorioController::class, 'movimentoBancario'])->name('movimento-bancario')->middleware(['seguranca:menu-relatorios']);
        });


        Route::prefix('distrito')->name('distrito.')->group(function () {
            Route::get('/relatorio/lancamentodasigrejas', [DistritoRelatorioController::class, 'lancamentodasigrejas'])->name('relatorio.lancamentodasigrejas')->middleware(['seguranca:distrito-menu-relatorio']);
            Route::post('/relatorio/lancamentodasigrejas/pdf', [DistritoRelatorioController::class, 'lancamentodasigrejasPdf'])->name('relatorio.lancamentodasigrejas-pdf')->middleware(['seguranca:distrito-menu-relatorio']);

            Route::get('/relatorio/saldodasigrejas', [DistritoRelatorioController::class, 'saldodasigrejas'])->name('relatorio.saldodasigrejas')->middleware(['seguranca:distrito-menu-relatorio']);
            Route::post('/relatorio/saldodasigrejas/pdf', [DistritoRelatorioController::class, 'saldodasigrejasPdf'])->name('relatorio.saldodasigrejas-pdf')->middleware(['seguranca:distrito-menu-relatorio']);

            Route::get('/relatorio/livrorazaogeral', [DistritoRelatorioController::class, 'livrorazaogeral'])->name('relatorio.livrorazaogeral')->middleware(['seguranca:distrito-menu-relatorio']);
            Route::post('/relatorio/livrorazaogeral/pdf', [DistritoRelatorioController::class, 'livrorazaogeralPdf'])->name('relatorio.livrorazaogeral-pdf')->middleware(['seguranca:distrito-menu-relatorio']);

            Route::get('/relatorio/orcamento', [DistritoRelatorioController::class, 'orcamento'])->name('relatorio.orcamento')->middleware(['seguranca:distrito-menu-relatorio']);
            Route::post('/relatorio/orcamento/pdf', [DistritoRelatorioController::class, 'orcamentoPdf'])->name('relatorio.orcamento-pdf')->middleware(['seguranca:distrito-menu-relatorio']);

            Route::get('/relatorio/variacaofinanceira', [DistritoRelatorioController::class, 'variacaofinanceira'])->name('relatorio.variacaofinanceira')->middleware(['seguranca:distrito-menu-relatorio']);
            Route::post('/relatorio/variacaofinanceira/pdf', [DistritoRelatorioController::class, 'variacaofinanceiraPdf'])->name('relatorio.variacaofinanceira-pdf')->middleware(['seguranca:distrito-menu-relatorio']);


            //Membresia DEV
            Route::get('/relatorio/membrosministerio', [DistritoRelatorioController::class, 'membrosministerio'])->name('relatorio.membrosministerio')->middleware(['seguranca:distrito-menu-relatorio']);
            Route::post('/relatorio/membrosministerio/pdf', [DistritoRelatorioController::class, 'membrosministerioPdf'])->name('relatorio.membrosministerio-pdf')->middleware(['seguranca:distrito-menu-relatorio']);

            //
            Route::get('/relatorio/quantidademembros', [DistritoRelatorioController::class, 'quantidademembros'])->name('relatorio.quantidademembros')->middleware(['seguranca:distrito-menu-relatorio']);
            Route::post('/relatorio/quantidademembros/pdf', [DistritoRelatorioController::class, 'quantidademembrosPdf'])->name('relatorio.quantidademembros-pdf')->middleware(['seguranca:distrito-menu-relatorio']);

            Route::get('/relatorio/estatisticagenero', [DistritoRelatorioController::class, 'estatisticagenero'])->name('relatorio.estatisticagenero')->middleware(['seguranca:distrito-menu-relatorio']);
            Route::post('/relatorio/estatisticagenero/pdf', [DistritoRelatorioController::class, 'estatisticageneroPdf'])->name('relatorio.estatisticagenero-pdf')->middleware(['seguranca:distrito-menu-relatorio']);

            Route::get('/congregacoes-por-igrejas', [DistritoRelatorioController::class, 'CongregacaoPorIgreja'])->name('relatorio.congregacaoporigreja')->middleware('seguranca:distrito-relatorio-congregacoes-igrejas');

            Route::get('/apirantes-por-igrejas', [DistritoRelatorioController::class, 'AspirantePorIgreja'])->name('relatorio.apirateporigreja')->middleware('seguranca:distrito-relatorio-aspirantes-igrejas');

            //Orcamentos
            Route::get('/cota-orcamentaria', [FinanceiroController::class, 'CotaOrcamentariaDistrito'])->name('cota.orcamentaria')->middleware(['seguranca:distrito-cota-orcamentaria']);

            //Orcamentos
            Route::get('/recursos-humanos', [FinanceiroController::class, 'RecursoHumanoDistrito'])->name('recurso.humano')->middleware(['seguranca:distrito-recurso-humano']);

            //CGEU
            Route::get('/relatorio-gceu', [GceuController::class, 'gceuRelatorioDistritoGceu'])->name('relatorio.gceu')->middleware(['seguranca:gceu-distrito-lista-gceu']);
            Route::get('/relatorio-funcoes-gceu', [GceuController::class, 'gceuRelatorioDistritoFuncoes'])->name('relatorio.funcoes.gceu')->middleware(['seguranca:gceu-distrito-lista-funcoes']);
            Route::get('/relatorio-aniversariantes-gceu', [GceuController::class, 'gceuRelatorioDistritoAniversariantes'])->name('relatorio.aniversariantes.gceu')->middleware(['seguranca:gceu-distrito-lista-aniversariantes']);
            Route::get('/relatorio-carta-pastoral-gceu', [GceuController::class, 'cartaPastoralDistrito'])->name('relatorio.carta-pastoral-distrito')->middleware(['seguranca:gceu-distrito-relatorio-carta-pastoral']);

            Route::get('/carta-pastoral/visualizar-html/{id}', [GceuController::class, 'cartaPastoralVisualizarHtmlDistrito'])->name('carta-pastoral-visualizar-html');
            Route::get('/carta-pastoral/visualizar-pdf/{id}', [GceuController::class, 'cartaPastoralPdfDistrito'])->name('carta-pastoral.pdf');
        });

        Route::prefix('regiao/relatorio')->name('regiao.')->group(function () {
            Route::get('/lancamentodasigrejas', [RegiaoRelatorioController::class, 'lancamentodasigrejas'])->name('relatorio.lancamentodasigrejas')->middleware(['seguranca:regiao-menu-relatorio']);
            Route::post('/lancamentodasigrejas/pdf', [RegiaoRelatorioController::class, 'lancamentodasigrejasPdf'])->name('relatorio.lancamentodasigrejas-pdf')->middleware(['seguranca:regiao-menu-relatorio']);

            Route::get('/saldodasigrejas', [RegiaoRelatorioController::class, 'saldodasigrejas'])->name('relatorio.saldodasigrejas')->middleware(['seguranca:regiao-menu-relatorio']);
            Route::post('/saldodasigrejas/pdf', [RegiaoRelatorioController::class, 'saldodasigrejasPdf'])->name('relatorio.saldodasigrejas-pdf')->middleware(['seguranca:regiao-menu-relatorio']);

            Route::get('/livrorazaogeral', [RegiaoRelatorioController::class, 'livrorazaogeral'])->name('relatorio.livrorazaogeral')->middleware(['seguranca:regiao-menu-relatorio']);
            Route::post('/livrorazaogeral/pdf', [RegiaoRelatorioController::class, 'livrorazaogeralPdf'])->name('relatorio.livrorazaogeral-pdf')->middleware(['seguranca:regiao-menu-relatorio']);

            Route::get('/orcamento', [RegiaoRelatorioController::class, 'orcamento'])->name('relatorio.orcamento')->middleware(['seguranca:regiao-menu-relatorio']);
            Route::post('/orcamento/pdf', [RegiaoRelatorioController::class, 'orcamentoPdf'])->name('relatorio.orcamento-pdf')->middleware(['seguranca:regiao-menu-relatorio']);

            Route::get('/variacaofinanceira', [RegiaoRelatorioController::class, 'variacaofinanceira'])->name('relatorio.variacaofinanceira')->middleware(['seguranca:regiao-menu-relatorio']);
            Route::post('/variacaofinanceira/pdf', [RegiaoRelatorioController::class, 'variacaofinanceiraPdf'])->name('relatorio.variacaofinanceira-pdf')->middleware(['seguranca:regiao-menu-relatorio']);

            Route::get('/financeiro-por-categoria', [RegiaoRelatorioController::class, 'financeiroPorCategoria'])->name('relatorio.financeiroPorCategoria')->middleware(['seguranca:regiao-menu-relatorio']);
            
            //Membresia DEV
            Route::get('/membrosministerio', [RegiaoRelatorioController::class, 'membrosministerio'])->name('relatorio.membrosministerio')->middleware(['seguranca:regiao-menu-relatorio']);
            Route::post('/membrosministerio/pdf', [RegiaoRelatorioController::class, 'membrosministerioPdf'])->name('relatorio.membrosministerio-pdf')->middleware(['seguranca:regiao-menu-relatorio']);

            //
            Route::get('/quantidademembros', [RegiaoRelatorioController::class, 'quantidademembros'])->name('relatorio.quantidademembros')->middleware(['seguranca:regiao-menu-relatorio']);
            Route::post('/quantidademembros/pdf', [RegiaoRelatorioController::class, 'quantidademembrosPdf'])->name('relatorio.quantidademembros-pdf')->middleware(['seguranca:regiao-menu-relatorio']);

            Route::get('/estatisticagenero', [RegiaoRelatorioController::class, 'estatisticagenero'])->name('relatorio.estatisticagenero')->middleware(['seguranca:regiao-menu-relatorio']);
            Route::post('/estatisticagenero/pdf', [RegiaoRelatorioController::class, 'estatisticageneroPdf'])->name('relatorio.estatisticagenero-pdf')->middleware(['seguranca:regiao-menu-relatorio']);
            
            Route::get('/irrf', [ContabilidadeController::class,'irrf'])->name('relatorio.irrf')->middleware('seguranca:regiao-menu-relatorio');

            Route::get('/ano-eclesiastico', [RegiaoRelatorioController::class,'anoEclesiastico'])->name('relatorio.ano.eclesiastico')->middleware('seguranca:regiao-menu-relatorio');
             Route::get('/financeiro/balancete', [FinanceiroRelatorioController::class, 'balanceteRegiao'])->name('relatorio-balancete-regiao')->middleware(['seguranca:regiao-menu-relatorio']);

             //Orcamentos
            Route::get('/cota-orcamentaria', [FinanceiroController::class, 'CotaOrcamentariaRegiao'])->name('cota.orcamentaria')->middleware(['seguranca:regiao-cota-orcamentaria']);

            //CGEU
            Route::get('/gceu', [GceuController::class, 'gceuRelatorioRegiaoGceu'])->name('relatorio.gceu')->middleware(['seguranca:gceu-regiao-lista-gceu']);
            Route::get('/relatorio-funcoes-gceu', [GceuController::class, 'gceuRelatorioRegiaoFuncoes'])->name('relatorio.funcoes.gceu')->middleware(['seguranca:gceu-regiao-lista-funcoes']);
            Route::get('/relatorio-aniversariantes-gceu', [GceuController::class, 'gceuRelatorioRegiaoAniversariantes'])->name('relatorio.aniversariantes.gceu')->middleware(['seguranca:gceu-regiao-lista-aniversariantes']);
            Route::get('/relatorio-carta-pastoral-gceu', [GceuController::class, 'cartaPastoralRegiao'])->name('relatorio.carta-pastoral-regiao')->middleware(['seguranca:gceu-regiao-relatorio-carta-pastoral']);

            Route::get('/carta-pastoral/visualizar-html/{id}', [GceuController::class, 'cartaPastoralVisualizarHtmlRegiao'])->name('carta-pastoral-visualizar-html');
            Route::get('/carta-pastoral/visualizar-pdf/{id}', [GceuController::class, 'cartaPastoralPdfRegiao'])->name('carta-pastoral.pdf');
        });

        // Relatórios Região Clérigos
        Route::prefix('regiao/relatorio')->name('regiao.')->controller(RegiaoRelatorioController::class)->group(function () {
            Route::get('/clerigos-aniversariantes', 'clerigoAniversariante')->name('relatorio.clerigosaniversariantes')->middleware('seguranca:relatorio-clerigos-aniversariantes');
            Route::get('/clerigos-esposas', 'clerigoEsposa')->name('relatorio.clerigosesposas')->middleware('seguranca:relatorio-clerigos-esposas');
            Route::get('/clerigos-dados', 'clerigoDados')->name('relatorio.clerigosdados')->middleware('seguranca:relatorio-clerigos-dados');
            Route::get('/clerigos-categorias', 'clerigoCategoria')->name('relatorio.clerigoscategoria')->middleware('seguranca:relatorio-clerigos-categoria');
            Route::get('/clerigos-status', 'clerigoStatus')->name('relatorio.clerigosstatus')->middleware('seguranca:relatorio-clerigos-status');
            Route::get('/historiconomeacoes', [RegiaoEstatisticasController::class, 'historiconomeacoes'])->name('estatistica.historiconomeacoes.regionais')->middleware(['seguranca:relatorio-clerigos-categoria']);
            Route::post('/historiconomeacoes/pdf', [RegiaoEstatisticasController::class, 'historiconomeacoesPdf'])->name('relatorio.historiconomeacoes-pdf')->middleware(['seguranca:relatorio-clerigos-categoria']);
        });

        // Relatórios Região Igreja
        Route::prefix('regiao/relatorio')->name('regiao.')->controller(RegiaoRelatorioController::class)->group(function () {
            Route::get('/congregacoes-por-igrejas', 'CongregacaoPorIgreja')->name('relatorio.congregacaoporigreja')->middleware('seguranca:regiao-relatorio-congregacoes-igrejas');
            Route::get('/cnpj-igrejas', 'cnpjIgreja')->name('cnpj.igreja')->middleware('seguranca:regiao-relatorio-cnpj-igreja');
            Route::get('/contato-igrejas', 'ContatoIgreja')->name('contato.igreja')->middleware('seguranca:regiao-relatorio-contato-igreja');
            Route::get('/conta-bancaria-igrejas', 'ContaBancariaIgreja')->name('conta.bancaria.igreja')->middleware('seguranca:regiao-relatorio-conta-bancaria-igreja');
        });

        Route::prefix('regiao/estatistica')->name('regiao.')->group(function () {
        //Estatitisca de Membros
            Route::get('/relatorio/estatistica-membros-evolucao', [RegiaoEstatisticasController::class, 'estatisticaEvolucao'])->name('estatistica.evolucao')->middleware(['seguranca:regiao-menu-estatistica']);
            //Estatitisca de Membros
            Route::get('/relatorio/estatistica-total-membresia', [RegiaoEstatisticasController::class, 'totalMembresia'])->name('estatistica.totalMembresia')->middleware(['seguranca:regiao-menu-estatistica']);
            
            Route::get('/relatorio/estatisticaescolaridade', [RegiaoRelatorioController::class, 'estatisticaescolaridade'])->name('relatorio.estatisticaescolaridade')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::post('/relatorio/estatisticaescolaridade/pdf', [RegiaoRelatorioController::class, 'estatisticaescolaridadePdf'])->name('relatorio.estatisticaescolaridade-pdf')->middleware(['seguranca:regiao-menu-estatistica']);

            Route::get('/relatorio/estatisticaestadocivil', [RegiaoRelatorioController::class, 'estatisticaestadocivil'])->name('relatorio.estatisticaestadocivil')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::post('/relatorio/estatisticaestadocivil/pdf', [RegiaoRelatorioController::class, 'estatisticaestadocivilPdf'])->name('relatorio.estatisticaestadocivil-pdf')->middleware(['seguranca:regiao-menu-estatistica']);

            Route::get('/relatorio/estatisticageneroporcentagem', [RegiaoRelatorioController::class, 'estatisticageneroporcentagem'])->name('relatorio.estatisticageneroporcentagem')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::post('/relatorio/estatisticageneroporcentagem/pdf', [RegiaoRelatorioController::class, 'estatisticageneroporcentagemPdf'])->name('relatorio.estatisticageneroporcentagem-pdf')->middleware(['seguranca:regiao-menu-estatistica']);

            Route::get('/relatorio/estatisticatotalmembros', [RegiaoRelatorioController::class, 'estatisticatotalmembros'])->name('relatorio.estatisticatotalmembros')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::post('/relatorio/estatisticatotalmembros/pdf', [RegiaoRelatorioController::class, 'estatisticatotalmembrosPdf'])->name('relatorio.estatisticatotalmembros-pdf')->middleware(['seguranca:regiao-menu-estatistica']);
            //Estatitisca de Membros
            Route::get('/relatorio/estatistica-membros-evolucao', [RegiaoEstatisticasController::class, 'estatisticaEvolucao'])->name('estatistica.evolucao')->middleware(['seguranca:regiao-menu-estatistica']);
            //Estatitisca de Membros
            Route::get('/relatorio/estatistica-total-membresia', [RegiaoEstatisticasController::class, 'totalMembresia'])->name('estatistica.totalMembresia')->middleware(['seguranca:regiao-menu-estatistica']);

            //Membresia
            Route::get('/relatorio/membresia', [RegiaoEstatisticasController::class, 'membresia'])->name('membresia')->middleware('seguranca:regiao-relatorio-membresia');
            //Exportar Excel
            Route::get('/relatorio/membresia/exportar', [RegiaoEstatisticasController::class, 'membresiaExportar'])->name('membresia.exportar')->middleware('seguranca:regiao-relatorio-membresia');
            //Exportar pdf
            Route::get('/relatorio/membresia/exportar/pdf', [RegiaoEstatisticasController::class, 'membresiaExportarPdf'])->name('membresia.exportar.pdf')->middleware('seguranca:regiao-relatorio-membresia');

            Route::get('/relatorio/historiconomeacoes', [RegiaoEstatisticasController::class, 'historiconomeacoes'])->name('estatistica.historiconomeacoes')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::post('/relatorio/historiconomeacoes/pdf', [RegiaoEstatisticasController::class, 'historiconomeacoesPdf'])->name('relatorio.historiconomeacoes-pdf')->middleware(['seguranca:regiao-menu-estatistica']);

            Route::get('/relatorio/ticketmedio', [RegiaoEstatisticasController::class, 'ticketmedio'])->name('estatistica.ticketmedio')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::post('/relatorio/ticketmedio/pdf', [RegiaoEstatisticasController::class, 'ticketmedioPdf'])->name('relatorio.ticketmedio-pdf')->middleware(['seguranca:regiao-menu-estatistica']);


            Route::get('/relatorio/totaldistritoregiao', [TotalizacaoController::class, 'totalDitritoPorRegiao'])->name('totalizacao.totaldistritoregiao')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::get('/relatorio/totaligrejasdistritos', [TotalizacaoController::class, 'totaligrejasdistritos'])->name('totalizacao.totaligrejasdistritos')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::get('/relatorio/totalcongregacoesigrejas', [TotalizacaoController::class, 'totalcongregacoesigrejas'])->name('totalizacao.totalcongregacoesigrejas')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::get('/relatorio/totalcongregacoesdistritos', [TotalizacaoController::class, 'totalcongregacoesdistritos'])->name('totalizacao.totalcongregacoesdistritos')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::get('/relatorio/totalfrentemissionaria', [TotalizacaoController::class, 'totalfrentemissionaria'])->name('totalizacao.totalfrentemissionaria')->middleware(['seguranca:regiao-menu-estatistica']);



            Route::get('/relatorio/distritomaisbatismo', [TotalizacaoController::class, 'distritomaisbatismo'])->name('dezmais.distritomaisbatismo')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::get('/relatorio/distritomaismembros', [TotalizacaoController::class, 'distritomaismembros'])->name('dezmais.distritomaismembros')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::get('/relatorio/distritomaiscrescerammembros', [TotalizacaoController::class, 'distritomaiscrescerammembros'])->name('dezmais.distritomaiscrescerammembros')->middleware(['seguranca:regiao-menu-estatistica']);

            Route::get('/relatorio/igrejamaisbatismo', [TotalizacaoController::class, 'igrejamaisbatismo'])->name('dezmais.igrejamaisbatismo')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::get('/relatorio/igrejamaismembros', [TotalizacaoController::class, 'igrejamaismembros'])->name('dezmais.igrejamaismembros')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::get('/relatorio/igrejamaiscrescerammembros', [TotalizacaoController::class, 'igrejamaiscrescerammembros'])->name('dezmais.igrejamaiscrescerammembros')->middleware(['seguranca:regiao-menu-estatistica']);

            Route::get('/relatorio/totalclerigosnomeacoes', [TotalizacaoController::class, 'totalclerigosnomeacoes'])->name('estatisticaclerigos.totalclerigosnomeacoes')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::get('/relatorio/totalclerigosstatus', [TotalizacaoController::class, 'totalclerigosstatus'])->name('estatisticaclerigos.totalclerigosstatus')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::get('/relatorio/totalclerigosfaxiaetaria', [TotalizacaoController::class, 'totalclerigosfaxiaetaria'])->name('estatisticaclerigos.totalclerigosfaxiaetaria')->middleware(['seguranca:regiao-menu-estatistica']);
            Route::get('/relatorio/totalclerigosporvinculo', [TotalizacaoController::class, 'totalclerigosporvinculo'])->name('estatisticaclerigos.totalclerigosporvinculo')->middleware(['seguranca:regiao-menu-estatistica']);
        });

        // Crud congregações
        Route::prefix('congregacao')->name('congregacao.')->group(function () {
            Route::get('/', [CongregacoesController::class, 'index'])->name('index')->middleware(['seguranca:congregacao-index']);
            Route::get('/list', [CongregacoesController::class, 'list'])->name('list')->middleware(['seguranca:congregacao-index']);
            Route::get('/novo', [CongregacoesController::class, 'novo'])->name('novo')->middleware(['seguranca:congregacao-cadastrar']);
            Route::post('/store', [CongregacoesController::class, 'store'])->name('store')->middleware(['seguranca:congregacao-cadastrar']);
            Route::get('/editar/{congregacao}', [CongregacoesController::class, 'editar'])->name('editar')->middleware(['seguranca:congregacao-editar'])->can('checkSameChurch', 'congregacao');
            Route::put('/update/{congregacao}', [CongregacoesController::class, 'update'])->name('update')->middleware(['seguranca:congregacao-atualizar']);
            Route::delete('/desativar/{congregacao}', [CongregacoesController::class, 'desativar'])->name('desativar')->middleware(['seguranca:congregacao-excluir']);
            Route::put('/restaurar/{id}', [CongregacoesController::class, 'restaurar'])->name('restaurar')->middleware(['seguranca:congregacao-editar']);
        });

        // Crud igrejas
        Route::prefix('igreja')->name('igreja.')->controller(IgrejasController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/list', 'list')->name('list');
            Route::get('estatistica-ano-eclesiastico/{igreja}', 'estatisticaAnoEclesiastico')->name('estatistica-ano-eclesiastico');
            Route::get('balancete/{igreja}', 'balancete')->name('balancete');
            Route::get('balancete-pdf/{igreja}', 'balancetePdf')->name('balancete-pdf');
            Route::get('movimento-diario/{igreja}', 'movimentoDiario')->name('movimento-diario');
            Route::get('movimento-diario-pdf/{igreja}', 'movimentoDiarioPdf')->name('movimento-diario-pdf');
            Route::get('livrorazao/{igreja}', 'livrorazao')->name('livrorazao');
            Route::get('livrorazao-pdf/{igreja}', 'livroRazaoPdf')->name('livrorazao-pdf');
        });

        // Crud GCEU
        Route::prefix('gceu')->name('gceu.')->group(function () {
            Route::get('/lista', [GceuController::class, 'index'])->name('index')->middleware(['seguranca:gceu-lista']);
            Route::get('/lista/teste', [GceuController::class, 'index'])->name('index.teste')->middleware(['seguranca:gceu-lista-teste']);
            Route::get('list', [GceuController::class, 'list'])->name('list')->middleware(['seguranca:gceu-lista']);
            Route::get('/novo', [GceuController::class, 'novo'])->name('novo')->middleware(['seguranca:gceu-cadastrar']);
            Route::post('/store', [GceuController::class, 'store'])->name('store')->middleware(['seguranca:gceu-cadastrar']);
            Route::get('/editar/{id}', [GceuController::class, 'editar'])->name('editar')->middleware(['seguranca:gceu-atualizar']);
            Route::post('/editar/{id}', [GceuController::class, 'update'])->name('update')->middleware(['seguranca:gceu-atualizar']);
            Route::post('/deletar/{id}', [GceuController::class, 'deletar'])->name('deletar')->middleware(['seguranca:gceu-excluir']);
            Route::get('/visualizar-html/{id}', [GceuController::class, 'visualizarHtml'])->name('visualizar-html');
            //membros
            Route::get('/membros', [GceuController::class, 'membros'])->name('membros')->middleware(['seguranca:gceu-membros']);
            Route::post('membro-atualizar/{id}', [GceuController::class, 'updateMembro'])->name('membro.update')->middleware(['seguranca:gceu-membros']);
            Route::post('/diario-presenca-falta', [GceuController::class, 'diarioPresencaFalta'])->name('diario.presenca.falta')->middleware(['seguranca:gceu-diario']);
            //Palavra pastoral
            Route::get('/carta-pastoral', [GceuController::class, 'cartaPastoral'])->name('carta-pastoral')->middleware(['seguranca:gceu-carta-pastoral']);
            Route::get('/carta-pastoral-novo', [GceuController::class, 'cartaPastoralNovo'])->name('carta-pastoral.novo')->middleware(['seguranca:gceu-carta-pastoral-cadastrar']);
            Route::post('/carta-pastoral/store', [GceuController::class, 'cartaPastoralStore'])->name('carta-pastoral.store')->middleware(['seguranca:gceu-carta-pastoral-cadastrar']);
            Route::get('/carta-pastoral/editar/{id}', [GceuController::class, 'cartaPastoralEditar'])->name('carta-pastoral.editar')->middleware(['seguranca:gceu-carta-pastoral-atualizar']);
            Route::post('/carta-pastoral/editar/{id}', [GceuController::class, 'cartaPastoralUpdate'])->name('carta-pastoral.update')->middleware(['seguranca:gceu-carta-pastoral-atualizar']);
            Route::post('/carta-pastoral/deletar/{id}', [GceuController::class, 'cartaPastoralDeletar'])->name('carta-pastoral.deletar')->middleware(['seguranca:gceu-carta-pastoral-excluir']);
            Route::get('/carta-pastoral/visualizar-html/{id}', [GceuController::class, 'cartaPastoralVisualizarHtml'])->name('carta-pastoral-visualizar-html');
            Route::get('/carta-pastoral/visualizar-pdf/{id}', [GceuController::class, 'cartaPastoralPdf'])->name('carta-pastoral.pdf');
            //Diario
            Route::get('/diario', [GceuController::class, 'diario'])->name('diario')->middleware(['seguranca:gceu-diario']);
            Route::post('/diario-presenca-falta', [GceuController::class, 'diarioPresencaFalta'])->name('diario.presenca.falta')->middleware(['seguranca:gceu-diario']);
            //Relatórios Igreja
            Route::get('/relatorio-gceu', [GceuController::class, 'gceuRelatorioGceu'])->name('relatorio.gceu')->middleware(['seguranca:gceu-igreja-lista-gceu']);
            Route::get('/relatorio-funcoes', [GceuController::class, 'gceuRelatorioFuncoes'])->name('relatorio.funcoes')->middleware(['seguranca:gceu-igreja-lista-funcoes']);
            Route::get('/relatorio-aniversariantes', [GceuController::class, 'gceuRelatorioAniversariantes'])->name('relatorio.aniversariantes')->middleware(['seguranca:gceu-igreja-lista-aniversariantes']);
            Route::get('/relatorio-carta-pastoral', [GceuController::class, 'cartaPastoralRelatorio'])->name('carta-pastoral-relatorio')->middleware(['seguranca:gceu-carta-pastoral-relatorio']);
            Route::get('/relatorio-diario', [GceuController::class, 'diarioRelatorio'])->name('diario-relatorio')->middleware(['seguranca:gceu-relatorio-diario']);
            
        });

        /* Por enquanto somente visualiações */
        Route::prefix('financeiro/fornecedor')->name('fornecedor.')->group(function () {
            Route::get('/', [FornecedorController::class, 'index'])->name('index')->middleware(['seguranca:fornecedores-index']);
            Route::get('/novo', [FornecedorController::class, 'novo'])->name('novo')->middleware(['seguranca:fornecedores-cadastrar']);
            Route::delete('/deletar/{id}', [FornecedorController::class, 'deletar'])->name('deletar')->middleware(['seguranca:fornecedores-deletar']);
            Route::get('/editar/{id}', [FornecedorController::class, 'editar'])->name('editar')->middleware(['seguranca:fornecedores-editar']);
            Route::post('/store', [FornecedorController::class, 'store'])->name('store')->middleware(['seguranca:fornecedores-cadastrar']);
            Route::post('/update/{id}', [FornecedorController::class, 'update'])->name('update')->middleware(['seguranca:fornecedores-editar']);
        });

        // Grupo de rotas para 'usuarios'
        Route::get('/auditorias', [AuditoriaController::class, 'index'])->name('auditorias.index')->middleware(['seguranca:auditoria']);
        Route::get('/auditorias/export/xlsx', [AuditoriaController::class, 'exportXlsx'])->name('auditorias.export.xlsx')->middleware(['seguranca:auditoria']);
        Route::get('/auditorias/export/pdf', [AuditoriaController::class, 'exportPdf'])->name('auditorias.export.pdf')->middleware(['seguranca:auditoria']);
        Route::prefix('seguranca/users')->name('usuarios.')->group(function () {
            Route::get('/', [UsuarioController::class, 'index'])->name('index')->middleware(['seguranca:usuarios-index']);
            Route::get('/novo', [UsuarioController::class, 'novo'])->name('novo')->middleware(['seguranca:usuarios-cadastrar']);
            Route::post('/update/{id}', [UsuarioController::class, 'update'])->name('update')->middleware(['seguranca:usuarios-atualizar']);
            Route::post('/store', [UsuarioController::class, 'store'])->name('store')->middleware(['seguranca:usuarios-cadastrar']);
            Route::delete('/deletar/{id}', [UsuarioController::class, 'deletar'])->name('deletar')->middleware(['seguranca:usuarios-excluir']);
            Route::get('/editar/{id}', [UsuarioController::class, 'editar'])->name('editar')->middleware(['seguranca:usuarios-editar']);
            Route::get('/check-email', [UsuarioController::class, 'checkEmail'])->name('checkEmail')->middleware(['seguranca:usuarios-cadastrar']);
        });

        // Relatórios
        Route::prefix('secretaria/relatorio')->name('relatorio.')->controller(RelatorioController::class)->group(function () {
            Route::get('/membresia', 'membresia')->name('membresia')->middleware('seguranca:relatorio-membresia');
            Route::get('/aniversariantes', 'aniversariantes')->name('aniversariantes')->middleware('seguranca:relatorio-aniversariantes');
            Route::get('/historico-eclesiastico', 'historicoEclesiastico')->name('historico-eclesiastico')->middleware('seguranca:relatorio-historico-eclesiastico');
            Route::get('/membros-disciplinados', 'membrosDisciplinados')->name('membros-disciplinados')->middleware('seguranca:relatorio-membro-disciplinado');
            Route::get('/funcao-eclesiastica', 'funcaoEclesiastica')->name('funcao-eclesiastica')->middleware('seguranca:relatorio-funcao-eclesiastica');
        });

        // Segurança
        Route::get('/selecionarPerfil', [HomeController::class, 'selecionarPerfil'])->withoutMiddleware([VerificaPerfil::class])->name('selecionarPerfil');

        Route::prefix('igrejas-regiao')->name('igrejas.regiao.')->controller(IgrejasRegiaoController::class)->middleware(['seguranca:menu-instituicoes'])->group(
            function () {
                Route::get('/', 'index')->name('index');
                Route::get('/list', 'list')->name('list');
                Route::get('estatistica-ano-eclesiastico/{igreja}', 'estatisticaAnoEclesiastico')->name('estatistica-ano-eclesiastico');
                Route::get('balancete/{igreja}', 'balancete')->name('balancete');
                Route::get('balancete-pdf/{igreja}', 'balancetePdf')->name('balancete-pdf');
                Route::get('movimento-diario/{igreja}', 'movimentoDiario')->name('movimento-diario');
                Route::get('movimento-diario-pdf/{igreja}', 'movimentoDiarioPdf')->name('movimento-diario-pdf');
                Route::get('livrorazao/{igreja}', 'livrorazao')->name('livrorazao');
                Route::get('livrorazao-pdf/{igreja}', 'livroRazaoPdf')->name('livrorazao-pdf');
            }
        );

        //Clérigos
        Route::prefix('clerigos')->name('clerigos.')->controller(ClerigosRegiaoController::class)->group(function () {
            Route::middleware(['seguranca:menu-instituicoes'])->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/novo', 'novo')->name('novo');
                Route::delete('/deletar/{id}', 'deletar')->name('deletar');
                Route::get('/editar/{id}', 'editar')->name('editar');
                Route::post('/store', 'store')->name('store');
                Route::post('/update/{id}', 'update')->name('update');
                Route::put('/ativar/{id}', 'ativar')->name('ativar');
                Route::get('/{id}/detalhes', 'detalhes')->name('detalhes');
            });
            Route::get('/buscar-por-cpf/{cpf}', 'findByCpf')->name('findByCpf');
        });

        Route::prefix('clerigos/nomeacoes')->name('clerigos.nomeacoes.')->controller(NomeacoesClerigosController::class)->middleware(['seguranca:menu-instituicoes'])->group(function () {
            Route::get('/{id}', 'index')->name('index');
            Route::get('/{pessoa}/novo', 'novo')->name('novo');
            Route::post('/{id}/novo', 'store')->name('store');
            Route::post('/{clerigoId}/finalizar/{id}', 'finalizar')->name('finalizar');
        });

        Route::prefix('usuario/clerigos/perfil')->name('clerigos.perfil.')->group(function () {
            // dependentes
            Route::prefix('dependentes')->controller(ClerigoPerfilController::class)->middleware(['user-clerigo'])->name('dependentes.')->group(function () {
                Route::get('', 'indexDependentes')->name('index');
                Route::get('novo', 'createDependente')->name('create');
                Route::post('novo', 'storeDependente')->name('store');
                Route::get('/{dependente}/editar', 'editDependente')->name('edit');
                Route::put('/{dependente}/atualizar', 'updateDependente')->name('update');
                Route::delete('/{dependente}', 'deleteDependente')->name('delete');
            });

            // prebendas
            Route::prefix('prebendas')->name('prebendas.')->controller(PrebendaController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::post('/update/{id}', 'update')->name('update');
                Route::get('/create', 'create')->name('create');
                Route::post('/create', 'store')->name('store');
                Route::delete('/{id}', 'delete')->name('delete');
                Route::get('/maxPrebenda/{ano}', 'maxPrebenda')->name('maxPrebenda');
            });

            // imposto de renda
            Route::prefix('imposto-de-renda')->name('impostoDeRenda.')->controller(ImpostoDeRendaController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('load-html/{prebenda}', 'loadHTML')->name('loadHTML');
            });
        });

        Route::prefix('clerigos/prebendas')->name('clerigos.prebendas.')->controller(PrebendasClerigosController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}/editar', 'edit')->name('edit');
            Route::post('/atualizar/{id}', 'update')->name('update');
            Route::delete('/{id}', 'delete')->name('delete');
            Route::get('/nova-prebenda', 'createPrebenda')->name('createPrebenda');
            Route::post('/nova-prebenda', 'storePrebenda')->name('storePrebenda');
            Route::post('/update-prebenda', 'updatePrebenda')->name('updatePrebenda');
            Route::post('/', 'update')->name('update.prebenda');
            Route::get('/valor', 'getValor')->name('valor');
        });

        //Instituicoes
        Route::prefix('instituicoes-regiao')->name('instituicoes-regiao.')->middleware(['seguranca:menu-instituicoes'])->controller(InstituicaoRegiaoDistritosController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/novo', 'novo')->name('novo');
            Route::delete('/deletar/{id}', 'deletar')->name('deletar');
            Route::get('/editar/{id}', 'editar')->name('editar');
            Route::post('/store', 'store')->name('store');
            Route::post('/update/{id}', 'update')->name('update');
            Route::put('/ativar/{id}', 'ativar')->name('ativar');
            Route::get('/{id}/detalhes', 'detalhes')->name('detalhes');
            Route::get('/{id}/nomeacoes', 'nomeacoes')->name('nomeacoes');
            Route::get('/{instituicao}/nomeacoes/novo', 'novaNomeacao')->name('nomeacoes.novo');
            Route::post('/{id}nomeacoes/novo', 'storeNomeacao')->name('nomeacoes.store');
            Route::post('/nomeacoes/{instituicao_id}/finalizar/{id}', 'finalizarNomeacao')->name('finalizar');
        });

        Route::prefix('instituicoes')->name('instituicoes.')->controller(HandleInstituicoesController::class)->group(function () {
            Route::get('igrejasByDistrito/{distritoId}', 'igrejasByDistrito');
        });

        Route::prefix('informe-rendimentos')->name('informe_rendimentos.')->controller(InformeRendimentosController::class)->group(function () {
            Route::get('exibirPdf/{ano}', 'exibirPdf')->name('exibirPdf');
        });

         // Contabilidade
        Route::prefix('contabilidade')->name('contabilidade.')->controller(ContabilidadeController::class)->group(function () {
            Route::get('/irrf', 'irrf')->name('irrf')->middleware('seguranca:contabilidade-irrf');
            Route::get('/financeiro/balancete', [FinanceiroRelatorioController::class, 'balanceteRegiao'])->name('relatorio-balancete')->middleware(['seguranca:contabilidade-irrf']);
        });

    });
});
