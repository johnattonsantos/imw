<?php

namespace App\Services\ServicesCongregados;

use App\Services\Media\MemberPhotoUploadService;
use App\Models\GCeuMembros;
use App\Models\MembresiaContato;
use App\Models\MembresiaFamiliar;
use App\Models\MembresiaFormacaoEclesiastica;
use App\Models\MembresiaFuncaoMinisterial;
use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Carbon\Carbon;

class SalvarCongregadoService
{
    use Identifiable;

    public function execute(array $data)
    {
        $dataMembro = $this->prepareMembroData($data);
        $dataContato = $this->prepareContatoData($data);
        $dataFamiliar = $this->prepareFamiliarData($data);
        $dataFormacoes = $this->prepareFormacoesData($data);
        $dataMinisteriais = $this->prepareMinisteriaisData($data);
        $dataGceu = $this->prepareGceuData($data);

        $membroID = $this->handleCreateMembro($dataMembro);
        $this->handleCreateContato($dataContato, $membroID);
        $this->handleCreateFamiliar($dataFamiliar, $membroID);
        $this->handleCreateFormacoes($dataFormacoes, $membroID);
        $this->handleCreateMinisteriais($dataMinisteriais, $membroID);
        $this->handleUpdateGceu($dataGceu, $membroID);

        if (isset($data['foto'])) {
            $this->handlePhotoUpload($data['foto'], $membroID);
        }

        return $membroID;
    }

    private function handlePhotoUpload($photo, $membroId)
    {
        $filePath = app(MemberPhotoUploadService::class)->upload($photo, 'fotos', true);

        $membro = MembresiaMembro::find($membroId);
        if ($membro) {
            $membro->foto = $filePath;
            $membro->save();
        }
    }

    private function prepareMembroData(array $data): array
    {
        return [
            'nome'            => $data['nome'],
            'sexo'            => $data['sexo'],
            'data_nascimento' => Carbon::createFromFormat('Y-m-d', $data['data_nascimento']),
            'estado_civil'  => $data['estado_civil'],
            'nacionalidade'  => $data['nacionalidade'],
            'naturalidade'  => $data['naturalidade'],
            'novo_convertido' => $data['novo_convertido'],
            'uf'  => $data['uf'] ?? null,
            'escolaridade_id'  => $data['escolaridade_id'],
            'profissao'  => $data['profissao'],
            'funcao_eclesiastica_id'  => $data['funcao_eclesiastica_id'],
            'cpf'  => isset($data['cpf']) && !empty($data['cpf']) ? preg_replace('/[^0-9]/', '', $data['cpf']) : null,
            'tipo_documento'  => $data['tipo_documento'],
            'documento'  => $data['documento'],
            'documento_complemento'  => $data['documento_complemento'],
            'data_conversao'  => $data['data_conversao'],
            'data_batismo'  => $data['data_batismo'],
            'data_batismo_espirito'  => $data['data_batismo_espirito'],
            'vinculo'         => MembresiaMembro::VINCULO_CONGREGADO,
            'status' => MembresiaMembro::STATUS_ATIVO,
            'congregacao_id' => $data['congregacao_id'],
            ...Identifiable::fetchSessionInstituicoesStoreMembresia()
        ];
    }

    private function prepareContatoData(array $data): array
        {
            $contatoData = [
                'telefone_preferencial' => preg_replace('/[^0-9]/', '', $data['telefone_preferencial']),
                'email_preferencial'     => $data['email_preferencial'],
                'cep' => preg_replace('/[^0-9]/', '', $data['cep']),
                'endereco' => $data['endereco'],
                'numero' => $data['numero'],
                'complemento' => $data['complemento'],
                'bairro' => $data['bairro'],
                'cidade' => $data['cidade'],
                'estado' => $data['estado'],
                'observacoes' => $data['observacoes'],
            ];

            // Verifica se as chaves opcionais existem antes de tentar acessá-las
            if (isset($data['telefone_alternativo'])) {
                $contatoData['telefone_alternativo'] = preg_replace('/[^0-9]/', '', $data['telefone_alternativo']);
            }
            if (isset($data['email_alternativo'])) {
                $contatoData['email_alternativo'] = $data['email_alternativo'];
            }

            return $contatoData;
        }

    private function prepareFamiliarData(array $data): array
    {
        return [
            'mae_nome' => $data['mae_nome'],
            'pai_nome' => $data['pai_nome'],
            'conjuge_nome' => $data['conjuge_nome'],
            'data_casamento' => $data['data_casamento'],
            'filhos' => $data['filhos'],
            'historico_familiar' => $data['historico_familiar'],
        ];
    }

    private function prepareFormacoesData(array $data): array
    {
        $dataFormacoes = [];
        if (!empty($data['curso-nome'])) {
            foreach ($data['curso-nome'] as $index => $nome) {
                $dataFormacoes[] = [
                    'curso_id' => $nome,
                    'inicio' => $data['curso-data-inicio'][$index] ?? null,
                    'termino' => $data['curso-data-conclusao'][$index] ?? null,
                    'observacao' => $data['curso-observacao'][$index] ?? '',
                ];
            }
        }
        return $dataFormacoes;
    }

    private function prepareMinisteriaisData(array $data): array
    {
        $dataMinisteriais = [];
        if (!empty($data['ministerial-departamento'])) {
            foreach ($data['ministerial-departamento'] as $index => $departamento) {
                $dataMinisteriais[] = [
                    'setor_id' => $departamento,
                    'tipoatuacao_id' => $data['ministerial-funcao'][$index] ?? null,
                    'data_entrada' => $data['ministerial-nomeacao'][$index] ?? null,
                    'data_saida' => $data['ministerial-exoneracao'][$index] ?? null,
                    'observacoes' => $data['ministerial-observacao'][$index] ?? '',
                ];
            }
        }
        return $dataMinisteriais;
    }

    private function prepareGceuData(array $data): array
    {
        $dataGceuMembro = [];
        if (isset($data['gceu'])) {
            foreach ($data['gceu'] as $index => $gceu) {
                if($gceu != null){                
                    $dataGceuMembro[] = [
                        'gceu_cadastro_id' => $gceu,
                        'gceu_funcao_id' => $data['gceu-funcao'][$index] ?? null,
                    ];
                }
            }
        }
        return $dataGceuMembro;
    }

    private function handleCreateMembro($data)
    {
        $membresia = MembresiaMembro::Create($data);
        return $membresia->id;
    }

    private function handleCreateContato($data, $membroId): void
    {
        MembresiaContato::updateOrCreate(['membro_id' => $membroId], $data);
    }

    private function handleCreateFamiliar($data, $membroId): void
    {
        MembresiaFamiliar::updateOrCreate(['membro_id' => $membroId], $data);
    }

    private function handleCreateFormacoes(array $formacoes, $membroId): void
    {

        $idsExistentes = MembresiaFormacaoEclesiastica::where('membro_id', $membroId)
            ->pluck('curso_id')->toArray();

        $idsAtualizados = [];
        foreach ($formacoes as $formacao) {
            if (!empty($formacao['curso_id'])) {
                $formacao['membro_id'] = $membroId;
                MembresiaFormacaoEclesiastica::updateOrCreate(
                    ['membro_id' => $membroId, 'curso_id' => $formacao['curso_id']],
                    $formacao
                );
                $idsAtualizados[] = $formacao['curso_id'];
            }
        }

        $idsParaRemover = array_diff($idsExistentes, $idsAtualizados);
        if (!empty($idsParaRemover)) {
            MembresiaFormacaoEclesiastica::where('membro_id', $membroId)
                ->whereIn('curso_id', $idsParaRemover)
                ->delete();
        }
    }

    private function handleCreateMinisteriais(array $ministeriais, $membroId): void
    {

        $updatedMinisterialIds = [];

        foreach ($ministeriais as $ministerial) {
            if (!empty($ministerial['setor_id']) && !empty($ministerial['tipoatuacao_id'])) {
                $ministerial['membro_id'] = $membroId;
                $ministerialModel = MembresiaFuncaoMinisterial::updateOrCreate(
                    [
                        'membro_id' => $membroId,
                        'setor_id' => $ministerial['setor_id'],
                        'tipoatuacao_id' => $ministerial['tipoatuacao_id'],
                    ],
                    $ministerial
                );

                $updatedMinisterialIds[] = $ministerialModel->id;
            }
        }

        MembresiaFuncaoMinisterial::where('membro_id', $membroId)
            ->whereNotIn('id', $updatedMinisterialIds)
            ->delete();
    }

    private function handleUpdateGCeu(array $dataGceu, $membroId): void
    {
        $updatedGceuIds = [];

        foreach ($dataGceu as $gceu) {
            if (!empty($gceu['gceu_cadastro_id']) && !empty($gceu['gceu_funcao_id'])) {
                $gceu['membro_id'] = $membroId;
                $gceuMembrosModel = GCeuMembros::updateOrCreate(
                    [
                        'membro_id' => $membroId,
                        'gceu_cadastro_id' => $gceu['gceu_cadastro_id'],
                        'gceu_funcao_id' => $gceu['gceu_funcao_id'],
                    ],
                    $gceu
                );

                $updatedGceuIds[] = $gceuMembrosModel->id;
            }
        }

        GCeuMembros::where('membro_id', $membroId)
            ->whereNotIn('id', $updatedGceuIds)
            ->delete();
    }


}
