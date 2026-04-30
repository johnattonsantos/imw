<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEbdLiderancaRequest;
use App\Http\Requests\UpdateEbdLiderancaRequest;
use App\Models\EbdLideranca;
use App\Traits\Identifiable;
use Illuminate\Validation\ValidationException;

class EbdLiderancaController extends Controller
{
    use Identifiable;

    public function index()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $liderancas = EbdLideranca::with('membro')
            ->whereHas('membro', fn ($q) => $q->where('igreja_id', $igrejaId))
            ->orderByDesc('ativo')
            ->orderBy('cargo')
            ->paginate(20);

        return view('ebd.liderancas.index', compact('liderancas'));
    }

    public function create()
    {
        return view('ebd.liderancas.create');
    }

    public function store(StoreEbdLiderancaRequest $request)
    {
        $data = $request->validated();

        $this->validateBusinessRules($data);

        EbdLideranca::create($data);

        return redirect()->route('ebd.liderancas.index')->with('success', 'Liderança cadastrada com sucesso.');
    }

    public function edit(EbdLideranca $lideranca)
    {
        $this->authorizeByIgreja($lideranca);

        return view('ebd.liderancas.edit', compact('lideranca'));
    }

    public function update(UpdateEbdLiderancaRequest $request, EbdLideranca $lideranca)
    {
        $this->authorizeByIgreja($lideranca);

        $data = $request->validated();
        $this->validateBusinessRules($data, $lideranca->id);

        $lideranca->update($data);

        return redirect()->route('ebd.liderancas.index')->with('success', 'Liderança atualizada com sucesso.');
    }

    public function destroy(EbdLideranca $lideranca)
    {
        $this->authorizeByIgreja($lideranca);

        $lideranca->delete();

        return redirect()->route('ebd.liderancas.index')->with('success', 'Liderança removida com sucesso.');
    }

    private function validateBusinessRules(array $data, ?int $ignoreId = null): void
    {
        if ((bool) ($data['ativo'] ?? false) && config('ebd.unique_active_leadership_per_role', true)) {
            $exists = EbdLideranca::where('cargo', $data['cargo'])
                ->where('ativo', true)
                ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'cargo' => 'Já existe liderança ativa para este cargo.',
                ]);
            }
        }
    }

    private function authorizeByIgreja(EbdLideranca $lideranca): void
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        if ((int) $lideranca->membro?->igreja_id !== (int) $igrejaId) {
            abort(403);
        }
    }
}
