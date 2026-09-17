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
                    'identidade' => null,
                    'orgao_emissor' => null,
                    'data_emissao' => null,
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
                    'ctps' => null,
                    'ctps_emissao' => null,
                    'habilitacao' => null,
                    'habilitacao_categoria' => null,
                    'habilitacao_emissor' => null,
                    'habilitacao_uf' => null,
                    'identidade_uf' => null,
                    'pispasep' => null,
                    'pispasep_emissao' => null,
                    'residencia_propria' => $request->input('residencia_propria'),
                    'residencia_propria_fgts' => null,
                    'titulo_eleitor' => null,
                    'titulo_eleitor_secao' => null,
                    'titulo_eleitor_zona' => null,
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
                'identidade' => null,
                'orgao_emissor' => null,
                'data_emissao' => null,
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
                'ctps' => null,
                'ctps_emissao' => null,
                'habilitacao' => null,
                'habilitacao_categoria' => null,
                'habilitacao_emissor' => null,
                'habilitacao_uf' => null,
                'identidade_uf' => null,
                'pispasep' => null,
                'pispasep_emissao' => null,
                'residencia_propria' => $request->input('residencia_propria'),
                'residencia_propria_fgts' => null,
                'titulo_eleitor' => null,
                'titulo_eleitor_secao' => null,
                'titulo_eleitor_zona' => null,
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
