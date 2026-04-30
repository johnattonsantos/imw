<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEbdVisitanteRapidoRequest;
use App\Models\MembresiaContato;
use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EbdMembroBuscaController extends Controller
{
    use Identifiable;

    public function buscar(Request $request)
    {
        $term = trim((string) $request->query('q', ''));
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $vinculosPermitidos = [
            MembresiaMembro::VINCULO_MEMBRO,
            MembresiaMembro::VINCULO_CONGREGADO,
            MembresiaMembro::VINCULO_VISITANTE,
        ];
        $vinculos = collect(explode(',', (string) $request->query('vinculos', '')))
            ->map(fn ($v) => strtoupper(trim($v)))
            ->filter(fn ($v) => in_array($v, $vinculosPermitidos, true))
            ->values()
            ->all();
        if (empty($vinculos)) {
            $vinculos = $vinculosPermitidos;
        }

        $buildQuery = function () {
            return MembresiaMembro::query()
                ->leftJoin('membresia_contatos as mc', 'mc.membro_id', '=', 'membresia_membros.id')
                ->select(
                    'membresia_membros.*',
                    DB::raw("COALESCE(NULLIF(mc.telefone_preferencial, ''), NULLIF(mc.telefone_alternativo, ''), NULLIF(mc.telefone_whatsapp, '')) as telefone_preferencial"),
                    DB::raw("COALESCE(NULLIF(mc.email_preferencial, ''), NULLIF(mc.email_alternativo, '')) as email_preferencial")
                );
        };

        $applySearchFilter = function ($q) use ($term) {
            $digits = preg_replace('/\D+/', '', $term);
            $termLower = mb_strtolower($term);
            $q->where(function ($inner) use ($term, $digits, $termLower) {
                $inner->where('membresia_membros.nome', 'like', "%{$term}%")
                    ->orWhere('membresia_membros.cpf', 'like', "%{$term}%")
                    ->orWhere('mc.email_preferencial', 'like', "%{$term}%")
                    ->orWhere('mc.email_alternativo', 'like', "%{$term}%")
                    ->orWhere('mc.telefone_preferencial', 'like', "%{$term}%")
                    ->orWhere('mc.telefone_alternativo', 'like', "%{$term}%")
                    ->orWhere('mc.telefone_whatsapp', 'like', "%{$term}%")
                    ->orWhereRaw('LOWER(mc.email_preferencial) like ?', ["%{$termLower}%"])
                    ->orWhereRaw('LOWER(mc.email_alternativo) like ?', ["%{$termLower}%"]);

                if ($digits !== '') {
                    $inner->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(membresia_membros.cpf, '.', ''), '-', ''), '/', ''), ' ', '') like ?",
                        ["%{$digits}%"]
                    )->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(mc.telefone_preferencial, '(', ''), ')', ''), '-', ''), ' ', '') like ?",
                        ["%{$digits}%"]
                    )->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(mc.telefone_alternativo, '(', ''), ')', ''), '-', ''), ' ', '') like ?",
                        ["%{$digits}%"]
                    )->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(mc.telefone_whatsapp, '(', ''), ')', ''), '-', ''), ' ', '') like ?",
                        ["%{$digits}%"]
                    );
                }
            });
        };

        $query = $buildQuery()
            ->where('membresia_membros.status', MembresiaMembro::STATUS_ATIVO)
            ->whereIn('membresia_membros.vinculo', $vinculos)
            ->where(function ($q) use ($igrejaId) {
                $q->where('membresia_membros.igreja_id', $igrejaId)
                    ->orWhereExists(function ($sub) use ($igrejaId) {
                        $sub->select(DB::raw(1))
                            ->from('membresia_rolpermanente as mr')
                            ->whereColumn('mr.membro_id', 'membresia_membros.id')
                            ->where('mr.igreja_id', $igrejaId)
                            ->where('mr.lastrec', 1)
                            ->whereNull('mr.deleted_at');
                    });
            })
            ->when($term !== '', $applySearchFilter)
            ->orderBy('membresia_membros.nome')
            ->limit(20)
            ->get();

        return response()->json([
            'data' => $query,
        ]);
    }

    public function cadastrarVisitante(StoreEbdVisitanteRapidoRequest $request)
    {
        $validated = $request->validated();
        $cpf = isset($validated['cpf']) ? preg_replace('/\D+/', '', $validated['cpf']) : null;
        $telefone = isset($validated['telefone_preferencial']) ? preg_replace('/\D+/', '', $validated['telefone_preferencial']) : null;

        if ($cpf !== null && $cpf !== '') {
            $exists = MembresiaMembro::where('cpf', $cpf)->exists();
            if ($exists) {
                return response()->json([
                    'message' => 'Já existe um membro com esse CPF.',
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $membro = MembresiaMembro::create([
                'status' => MembresiaMembro::STATUS_ATIVO,
                'nome' => $validated['nome'],
                'sexo' => $validated['sexo'],
                'data_nascimento' => $validated['data_nascimento'] ?? null,
                'cpf' => $cpf !== '' ? $cpf : null,
                'vinculo' => MembresiaMembro::VINCULO_VISITANTE,
                ...Identifiable::fetchSessionInstituicoesStoreMembresia(),
            ]);

            if (($telefone ?? '') !== '' || !empty($validated['email_preferencial'])) {
                MembresiaContato::create([
                    'membro_id' => $membro->id,
                    'telefone_preferencial' => $telefone !== '' ? $telefone : null,
                    'email_preferencial' => $validated['email_preferencial'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Visitante cadastrado com sucesso.',
                'membro' => [
                    'id' => $membro->id,
                    'nome' => $membro->nome,
                    'cpf' => $membro->cpf,
                    'vinculo' => $membro->vinculo,
                    'status' => $membro->status,
                    'telefone_preferencial' => $telefone,
                    'email_preferencial' => $validated['email_preferencial'] ?? null,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Não foi possível cadastrar o visitante.',
            ], 500);
        }
    }
}
