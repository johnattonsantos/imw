<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Models\PessoaNomeacao;
use App\Models\PessoasPessoa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
class StoreClerigosService
{
    public function execute($request)
    {
        if ($request->file('image')) {
            $photo = $request->file('image');  
                try {
                    // Gerar um UUID para o nome do arquivo
                    $filename = Uuid::uuid4()->toString() . '.' . $photo->getClientOriginalExtension();                        
                    // Fazer upload do arquivo para o S3 usando o método storeAs
                    $filePath = $photo->storeAs('fotos', $filename, 's3');
                } catch (\Exception $e) {
                    // Tratamento de erro, caso o upload falhe
                    return response()->json(['error' => $e->getMessage()], 500);
                }
        }else{
            $filePath = '';
        }
        $instituicaoId = session('session_perfil')->instituicoes->regiao->id;
        PessoasPessoa::create([
            'nome' => $request['nome'],
            'identidade' => $request['identidade'],
            'identidade_uf' => $request['identidade_uf'],
            'orgao_emissor' => $request['orgao_emissor'],
            'data_emissao' => $request['data_emissao'],
            'foto' => $filePath,
            'cpf' => $request['cpf'],
            'endereco' => $request['endereco'],
            'numero' => $request['numero'],
            'regiao_id' =>   $data['regiao_id'] = $instituicaoId,
            'complemento' => $request['complemento'],
            'bairro' => $request['bairro'],
            'cidade' => $request['cidade'],
            'uf' => $request['uf'],
            'pais' => $request['pais'],
            'cep' => $request['cep'],
            'residencia_propria' => $request['residencia_propria'],
            'residencia_propria_fgts' => $request['residencia_propria_fgts'],
            'email' => $request['email'],
            'estado_civil' => $request['estado_civil'],
            'sexo' => $request['sexo'],
            'nome_mae' => $request['nome_mae'],
            'nome_pai' => $request['nome_pai'],
            'data_nascimento' => $request['data_nascimento'],
            'telefone_preferencial' => $request['telefone_preferencial'],
            'telefone_alternativo' => $request['telefone_alternativo'],
            'habilitacao' => $request->input('habilitacao'),
            'habilitacao_categoria' => $request->input('habilitacao_categoria'),
            'habilitacao_emissor' => $request->input('habilitacao_emissor'),
            'habilitacao_uf' => $request->input('habilitacao_uf', ''),
            'ctps' => $request->input('ctps', ''),
            'ctps_emissao' => $request->input('ctps_emissao', ''),
            'titulo_eleitor' => $request['titulo_eleitor'],
            'titulo_eleitor_secao' => $request['titulo_eleitor_secao'],
            'titulo_eleitor_zona' => $request['titulo_eleitor_zona'],
            'formacao_id' => $request['formacao_id'],
            'categoria' => $request['categoria'],
            'situacao_id' => $request['situacao'],
            'data_consagracao' => $request['data_consagracao'],
            'data_ordenacao' => $request['data_ordenacao'],
            'data_integralizacao' => $request['data_integralizacao'],
            'rol' => $request['rol'],
        ]);
    }
}
