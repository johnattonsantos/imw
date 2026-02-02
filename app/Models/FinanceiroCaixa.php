<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class FinanceiroCaixa extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'financeiro_caixas';

    protected $fillable = ['descricao', 'instituicao_id', 'tipo'];

    const IDS_TRANFERENCIA = [100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137];

    public function lancamentos()
    {
        return $this->hasMany(FinanceiroLancamento::class, 'caixa_id');
    }

    public function saldoAtualNaoConciliado()
    {
        $totalEntrada = $this->totalLancamentosNaoConciliadosEntrada() + $this->totalLancamentosNaoConciliadosTransferenciaEntrada();
        $totalSaida = $this->totalLancamentosNaoConciliadosSaida() + $this->totalLancamentosNaoConciliadosTransferenciaSaida();
        $ultimoConciliado = $this->totalLancamentosUltimosConciliados();

        return $totalEntrada - $totalSaida + $ultimoConciliado;
    }


    public function saldoAtualNaoConciliadoPorData($ano, $mes) 
    {
        $totalEntrada = $this->totalLancamentosNaoConciliadosEntradaPorData($ano, $mes) + $this->totalLancamentosNaoConciliadosTransferenciaEntradaPorData($ano, $mes);
        $totalSaida = $this->totalLancamentosNaoConciliadosSaidaPorData($ano, $mes) + $this->totalLancamentosNaoConciliadosTransferenciaSaidaPorData($ano, $mes);
        $ultimoConciliado = $this->totalLancamentosUltimosConciliados();

        return $totalEntrada - $totalSaida + $ultimoConciliado;
    }

    public function saldoAtual($mesAno = null)
    {
        $totalEntrada = $this->totalLancamentosEntrada($mesAno) + $this->totalLancamentosTransferenciaEntrada($mesAno);
        $totalSaida = $this->totalLancamentosSaida($mesAno) + $this->totalLancamentosTransferenciaSaida($mesAno);
        $ultimoConciliado = $this->totalLancamentosUltimosConciliados($mesAno);
    
        return $totalEntrada - $totalSaida + $ultimoConciliado;
    }
    

    public function totalLancamentosNaoConciliados()
    {
        return $this->lancamentos()
            ->where('conciliado', 0)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id)
            ->sum('valor');
    }


    public function totalLancamentosNaoConciliadosTransferenciaEntrada()
    {
        return $this->lancamentos()
            ->whereIn('plano_conta_id', self::IDS_TRANFERENCIA)
            ->where('conciliado', 0)
            ->where('tipo_lancamento', FinanceiroLancamento::TP_LANCAMENTO_ENTRADA)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id)
            ->sum('valor');
    }

    public function totalLancamentosNaoConciliadosTransferenciaEntradaPorData($ano, $mes)
    {
        return $this->lancamentos()
            ->whereIn('plano_conta_id', self::IDS_TRANFERENCIA)
            ->where('conciliado', 0)
            ->where('tipo_lancamento', FinanceiroLancamento::TP_LANCAMENTO_ENTRADA)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id)    
            ->whereYear('data_movimento', $ano)
            ->whereMonth('data_movimento', $mes)
            ->sum('valor');
    }

    public function totalLancamentosTransferenciaEntrada($mesAno = null)
    {
        $query = $this->lancamentos()
            ->whereIn('plano_conta_id', self::IDS_TRANFERENCIA)
            ->where('conciliado', 0)
            ->where('tipo_lancamento', FinanceiroLancamento::TP_LANCAMENTO_ENTRADA)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id);

        if ($mesAno) {
            // Extrair o mês e o ano do formato "mm/yyyy"
            $mes = intval(substr($mesAno, 0, 2));
            $ano = intval(substr($mesAno, 3, 4));

            // Adicionar a condição para buscar lançamentos do mês/ano fornecido
            $query->whereYear('data_movimento', $ano)->whereMonth('data_movimento', $mes);
        }

        return $query->sum('valor');
    }


    public function totalLancamentosNaoConciliadosTransferenciaSaida()
    {
        return $this->lancamentos()
            ->whereIn('plano_conta_id', self::IDS_TRANFERENCIA)
            ->where('conciliado', 0)
            ->where('tipo_lancamento', FinanceiroLancamento::TP_LANCAMENTO_SAIDA)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id)
            ->sum('valor');
    }

    public function totalLancamentosNaoConciliadosTransferenciaSaidaPorData($ano, $mes)
    {
        return $this->lancamentos()
            ->whereIn('plano_conta_id', self::IDS_TRANFERENCIA)
            ->where('conciliado', 0)
            ->where('tipo_lancamento', FinanceiroLancamento::TP_LANCAMENTO_SAIDA)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id)
            ->whereYear('data_movimento', $ano)
            ->whereMonth('data_movimento', $mes)
            ->sum('valor');
    }


    public function totalLancamentosTransferenciaSaida($mesAno = null)
    {
        $query = $this->lancamentos()
            ->whereIn('plano_conta_id', self::IDS_TRANFERENCIA)
            ->where('conciliado', 0)
            ->where('tipo_lancamento', FinanceiroLancamento::TP_LANCAMENTO_SAIDA)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id);

        if ($mesAno) {
            // Extrair o mês e o ano do formato "mm/yyyy"
            $mes = intval(substr($mesAno, 0, 2));
            $ano = intval(substr($mesAno, 3, 4));

            // Adicionar a condição para buscar lançamentos do mês/ano fornecido
            $query->whereYear('data_movimento', $ano)->whereMonth('data_movimento', $mes);
        }

        return $query->sum('valor');
    }


    public function totalLancamentosNaoConciliadosEntrada()
    {
        return $this->lancamentos()
            ->whereNotIn('plano_conta_id', self::IDS_TRANFERENCIA)
            ->where('conciliado', 0)
            ->where('tipo_lancamento', FinanceiroLancamento::TP_LANCAMENTO_ENTRADA)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id)
            ->sum('valor');
    }

    public function totalLancamentosNaoConciliadosEntradaPorData($ano, $mes)
    {
        return $this->lancamentos()
            ->whereNotIn('plano_conta_id', self::IDS_TRANFERENCIA)
            ->where('conciliado', 0)
            ->where('tipo_lancamento', FinanceiroLancamento::TP_LANCAMENTO_ENTRADA)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id)
            ->whereYear('data_movimento', $ano)
            ->whereMonth('data_movimento', $mes)
            ->sum('valor');
    }

    public function totalLancamentosEntrada($mesAno = null)
    {
        $query = $this->lancamentos()
            ->whereNotIn('plano_conta_id', self::IDS_TRANFERENCIA)
            ->where('tipo_lancamento', FinanceiroLancamento::TP_LANCAMENTO_ENTRADA)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id);

        if ($mesAno) {
            // Extrair o mês e o ano do formato "mm/yyyy"
            $mes = intval(substr($mesAno, 0, 2));
            $ano = intval(substr($mesAno, 3, 4));

            // Adicionar a condição para buscar lançamentos do mês/ano fornecido
            $query->whereYear('data_movimento', $ano)->whereMonth('data_movimento', $mes);
        }

        return $query->sum('valor');
    }



    public function totalLancamentosNaoConciliadosSaida()
    {
        return $this->lancamentos()
            ->whereNotIn('plano_conta_id', self::IDS_TRANFERENCIA)
            ->where('conciliado', 0)
            ->where('tipo_lancamento', FinanceiroLancamento::TP_LANCAMENTO_SAIDA)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id)
            ->sum('valor');
    }

    public function totalLancamentosNaoConciliadosSaidaPorData($ano, $mes)
    {
        return $this->lancamentos()
            ->whereNotIn('plano_conta_id', self::IDS_TRANFERENCIA)
            ->where('conciliado', 0)
            ->where('tipo_lancamento', FinanceiroLancamento::TP_LANCAMENTO_SAIDA)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id)
            ->whereYear('data_movimento', $ano)
            ->whereMonth('data_movimento', $mes)
            ->sum('valor');
    }

    public function totalLancamentosSaida($mesAno = null)
    {
        $query = $this->lancamentos()
            ->whereNotIn('plano_conta_id', self::IDS_TRANFERENCIA)
            ->where('tipo_lancamento', FinanceiroLancamento::TP_LANCAMENTO_SAIDA)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id);

        if ($mesAno) {
            // Extrair o mês e o ano do formato "mm/yyyy"
            $mes = intval(substr($mesAno, 0, 2));
            $ano = intval(substr($mesAno, 3, 4));

            // Adicionar a condição para buscar lançamentos do mês/ano fornecido
            $query->whereYear('data_movimento', $ano)->whereMonth('data_movimento', $mes);
        }

        return $query->sum('valor');
    }


    public function totalLancamentosUltimosConciliados($mesAno = null)
    {
        $query = FinanceiroSaldoConsolidadoMensal::where('caixa_id', $this->id)
            ->where('instituicao_id', session()->get('session_perfil')->instituicao_id);

        if ($mesAno) {
            // Extrair o mês e o ano do formato "mm/yyyy"
            $mes = intval(substr($mesAno, 0, 2));
            $ano = intval(substr($mesAno, 3, 4));

            // Montar a data do primeiro dia do mês seguinte ao mês/ano fornecido
            $dataInicio = Carbon::createFromFormat('Y-m-d', "$ano-$mes-01")->addMonth()->startOfMonth();

            // Adicionar a condição para buscar registros anteriores à data de início do mês fornecido
            $query->where('data_hora', '<', $dataInicio);
        }

        $saldo = $query->orderBy('data_hora', 'desc')->first();

        if (!$saldo) {
            return 0;
        }

        return $saldo->saldo_final;
    }
}
