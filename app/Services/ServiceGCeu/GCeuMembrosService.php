<?php

namespace App\Services\ServiceGCeu;

use App\Models\GCeu;
use App\Models\GCeuFuncoes;
use App\Models\GCeuMembros;
use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class GCeuMembrosService
{
    public function getList($igrejaId, $data): array
    {
        $data['instituicao'] = Identifiable::fetchSessionIgrejaLocal()->nome;
        $data['instituicao_id'] = Identifiable::fetchSessionIgrejaLocal()->id;
        $data['gceus'] = GCeu::where(['status' => 'A', 'instituicao_id' => $igrejaId])->orderBy('nome', 'asc')->get();
        $data['gceuFuncoes'] = GCeuFuncoes::orderBy('funcao', 'asc')->get();
        if(isset($data['membro_id'])) {
            $gceuMembros = GCeuMembros::select('gceu_membros.gceu_cadastro_id', 'gceu_membros.membro_id', 'gceu_membros.gceu_funcao_id','gceu_funcoes.funcao')->Join('gceu_funcoes', 'gceu_funcoes.id', 'gceu_membros.gceu_funcao_id' )->where(['membro_id' => $data['membro_id']])->get();
            $data['gceuMembros'] = $gceuMembros;
            $data['totalFuncao'] = DB::select("SELECT COUNT(gceu_cadastro_id) as total, gceu_funcoes.funcao from gceu_membros inner join gceu_funcoes on gceu_funcoes.id = gceu_membros.gceu_funcao_id where membro_id = '{$data['membro_id']}' GROUP BY gceu_funcao_id, gceu_funcoes.funcao");
        } else {
            $data['gceuMembros'] = [];
            $data['totalFuncao'] = [];
        }
        $data['membros'] = MembresiaMembro::where(['igreja_id' => $igrejaId, 'status' => 'A'])
            ->orderBy('nome', 'asc')
            ->get();
        return $data;
    }

    public function getListRelatorio($id, $data): array
    {
        $date = isset($data['dt_gceu']) ? $data['dt_gceu'] :  '';
        $Gceus = GCeu::where(['instituicao_id' => $id])->get();
        $data['Gceus'] = $Gceus;
        $igrejaNome = Identifiable::fetchSessionIgrejaLocal()->nome;
        $data['instituicao'] = $igrejaNome;
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data['instituicao_id'] = $igrejaId;
        $gceuId = isset($data['gceu_id']) ? $data['gceu_id'] : '';
        if($gceuId){
            $data['membros'] = GCeuMembros::select('gceu_cadastros.nome as gceu_nome','gceu_membros.gceu_cadastro_id as gceu_id','gceu_membros.membro_id', 'membresia_membros.nome', DB::raw("(SELECT presenca FROM gceu_diario WHERE gceu_id = $gceuId AND membro_id = gceu_membros.membro_id AND data ='$date') presenca"))
                    ->join('gceu_cadastros', 'gceu_cadastros.id', 'gceu_membros.gceu_cadastro_id')
                    ->join('membresia_membros', 'membresia_membros.id', 'gceu_membros.membro_id')
                    ->when(request()->get('gceu_id'), function ($query) {
                        $query->where('gceu_membros.gceu_cadastro_id', request()->get('gceu_id'));
                    })
                    ->where(['gceu_cadastros.status' => 'A', 'instituicao_id' => $igrejaId])
                    ->groupBy(['gceu_membros.gceu_cadastro_id','gceu_membros.membro_id', 'membresia_membros.nome', 'gceu_cadastros.nome'])->get();
        }else{
            $data['membros'] = GCeuMembros::select('gceu_cadastros.nome as gceu_nome','gceu_membros.gceu_cadastro_id as gceu_id','gceu_membros.membro_id', 'membresia_membros.nome', DB::raw("(SELECT presenca FROM gceu_diario WHERE gceu_id = gceu_cadastros.id AND membro_id = gceu_membros.membro_id AND data ='$date') presenca"))
                    ->join('gceu_cadastros', 'gceu_cadastros.id', 'gceu_membros.gceu_cadastro_id')
                    ->join('membresia_membros', 'membresia_membros.id', 'gceu_membros.membro_id')
                    ->where(['gceu_cadastros.status' => 'A', 'instituicao_id' => $igrejaId])->get();
        }
        $dataFormatada = formatDate($date);
        $data['titulo'] = "Relatório diário de presença/falta do GCEU da igreja: $igrejaNome na data $dataFormatada";
        return $data;
    }
    
}
