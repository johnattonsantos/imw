<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Models\PessoasPessoa;
//use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Config;
use Ramsey\Uuid\Uuid;

class UpdateClerigosService
{
    public function execute($request, $id)
    {
        $clerigo = PessoasPessoa::findOrFail($id);
        $instituicaoId = session('session_perfil')->instituicoes->regiao->id;
        $cpf = $request->filled('cpf') ? $request->input('cpf') : null;

        if ($request->file('image')) {
            $photo = $request->file('image');  
                try {
                    // Gerar um UUID para o nome do arquivo
                    $filename = Uuid::uuid4()->toString() . '.' . $photo->getClientOriginalExtension();                        
                    $filePath = $this->storeFoto($photo, $filename);
                } catch (\Exception $e) {
                    // Tratamento de erro, caso o upload falhe
                    return response()->json(['error' => $e->getMessage()], 500);
                }
                $clerigo->update([
                    'nome' => $request->input('nome'),
                    'identidade' => $request->input('identidade'),
                    'orgao_emissor' => $request->input('orgao_emissor'),
                    'data_emissao' => $request->input('data_emissao'),
                    'foto' => $filePath,
                    'cpf' => $cpf,
                    'endereco' => $request->input('endereco'),
                    'numero' => $request->input('numero'),
                    'complemento' => $request->input('complemento', ''),
                    'bairro' => $request->input('bairro'),
                    'cidade' => $request->input('cidade'),
                    'uf' => $request->input('uf'),
                    'pais' => $request->input('pais'),
                    'cep' => $request->input('cep'),
                    'email' => $request->input('email'),
                    'estado_civil' => $request->input('estado_civil'),
                    'regiao_id' => $instituicaoId,
                    'sexo' => $request->input('sexo'),
                    'formacao_id' => $request->input('formacao_id'),
                    'nome_mae' => $request->input('nome_mae', ''),
                    'nome_pai' => $request->input('nome_pai', ''),
                    'data_nascimento' => $request->input('data_nascimento', ''),
                    'telefone_preferencial' => $request->input('telefone_preferencial'),
                    'telefone_alternativo' => $request->input('telefone_alternativo'),
                    'ctps' => $request->input('ctps', ''),
                    'ctps_emissao' => $request->input('ctps_emissao', ''),
                    'habilitacao' => $request->input('habilitacao'),
                    'habilitacao_categoria' => $request->input('habilitacao_categoria'),
                    'habilitacao_emissor' => $request->input('habilitacao_emissor'),
                    'habilitacao_uf' => $request->input('habilitacao_uf', ''),
                    'identidade_uf' => $request->input('identidade_uf', ''),
                    'pispasep' => $request->input('pispasep', ''),
                    'pispasep_emissao' => $request->input('pispasep_emissao', ''),
                    'residencia_propria' => $request->input('residencia_propria'),
                    'residencia_propria_fgts' => $request->input('residencia_propria_fgts'),
                    'titulo_eleitor' => $request->input('titulo_eleitor', ''),
                    'titulo_eleitor_secao' => $request->input('titulo_eleitor_secao', ''),
                    'titulo_eleitor_zona' => $request->input('titulo_eleitor_zona', ''),
                    'categoria' => $request->input('categoria', ''),
                    'situacao_id' => $request->input('situacao', ''),
                    'data_consagracao' => $request->input('data_consagracao'),
                    'data_ordenacao' => $request->input('data_ordenacao', ''),
                    'data_integralizacao' => $request->input('data_integralizacao', ''),
                    'rol' => $request->input('rol', ''),
                ]);
        }else{
            $clerigo->update([
                'nome' => $request->input('nome'),
                'identidade' => $request->input('identidade'),
                'orgao_emissor' => $request->input('orgao_emissor'),
                'data_emissao' => $request->input('data_emissao'),
                'cpf' => $cpf,
                'endereco' => $request->input('endereco'),
                'numero' => $request->input('numero'),
                'complemento' => $request->input('complemento', ''),
                'bairro' => $request->input('bairro'),
                'cidade' => $request->input('cidade'),
                'uf' => $request->input('uf'),
                'pais' => $request->input('pais'),
                'cep' => $request->input('cep'),
                'email' => $request->input('email'),
                'estado_civil' => $request->input('estado_civil'),
                'regiao_id' => $instituicaoId,
                'sexo' => $request->input('sexo'),
                'formacao_id' => $request->input('formacao_id'),
                'nome_mae' => $request->input('nome_mae', ''),
                'nome_pai' => $request->input('nome_pai', ''),
                'data_nascimento' => $request->input('data_nascimento', ''),
                'telefone_preferencial' => $request->input('telefone_preferencial'),
                'telefone_alternativo' => $request->input('telefone_alternativo'),
                'ctps' => $request->input('ctps', ''),
                'ctps_emissao' => $request->input('ctps_emissao', ''),
                'habilitacao' => $request->input('habilitacao'),
                'habilitacao_categoria' => $request->input('habilitacao_categoria'),
                'habilitacao_emissor' => $request->input('habilitacao_emissor'),
                'habilitacao_uf' => $request->input('habilitacao_uf', ''),
                'identidade_uf' => $request->input('identidade_uf', ''),
                'pispasep' => $request->input('pispasep', ''),
                'pispasep_emissao' => $request->input('pispasep_emissao', ''),
                'residencia_propria' => $request->input('residencia_propria'),
                'residencia_propria_fgts' => $request->input('residencia_propria_fgts'),
                'titulo_eleitor' => $request->input('titulo_eleitor', ''),
                'titulo_eleitor_secao' => $request->input('titulo_eleitor_secao', ''),
                'titulo_eleitor_zona' => $request->input('titulo_eleitor_zona', ''),
                'categoria' => $request->input('categoria', ''),
                'situacao_id' => $request->input('situacao', ''),
                'data_consagracao' => $request->input('data_consagracao'),
                'data_ordenacao' => $request->input('data_ordenacao', ''),
                'data_integralizacao' => $request->input('data_integralizacao', ''),
                'rol' => $request->input('rol', ''),
            ]);
        }        
    }

    private function storeFoto($photo, string $filename): string
    {
        $disk = $this->hasS3Credentials() ? 's3' : 'public';
        $filePath = $photo->storeAs('fotos', $filename, $disk);

        return $disk === 'public' ? 'storage/' . ltrim($filePath, '/') : $filePath;
    }

    private function hasS3Credentials(): bool
    {
        return filled(Config::get('filesystems.disks.s3.key'))
            && filled(Config::get('filesystems.disks.s3.secret'))
            && filled(Config::get('filesystems.disks.s3.region'))
            && filled(Config::get('filesystems.disks.s3.bucket'));
    }
}
