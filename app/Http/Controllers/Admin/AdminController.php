<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\MembroNotFoundException;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\User;
use App\Services\ServicesUsuarios\AdminDeletarUsuarioService;
use App\Services\ServicesUsuarios\AdminEditarUsuarioService;
use App\Services\ServicesUsuarios\AdminListUsuariosService;
use App\Services\ServicesUsuarios\AdminNovoUsuarioService;
use App\Services\ServicesUsuarios\SalvarUsuarioService;
use App\Models\InstituicoesInstituicao;
use App\Models\Perfil;
use App\Models\PerfilUser;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $data = app(AdminListUsuariosService::class)->execute($request->all(), User::GERAL);
        return view('admin.index', $data);
    }

    public function novo()
    {
        try {
            $perfis = app(AdminNovoUsuarioService::class)->execute();
            return view('admin.novo', compact('perfis'));
        } catch (MembroNotFoundException $e) {
            return redirect()->route('admin.index')->with('error', 'Registro não encontrado.');
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('error', 'Erro ao abrir a página, por favor, tente mais tarde!');
        }
    }

    public function store(StoreUsuarioRequest $request)
    {
        try {
            DB::beginTransaction();
            app(SalvarUsuarioService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('admin.novo')->with('success', 'Usuário cadastrado com sucesso.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.novo')->with('error', $e);
        }
    }

    public function editar($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($this->hasRegionalScopeProfile() && !$this->canManageUserInCurrentRegion($user)) {
                return redirect()->route('admin.index')->with('error', 'Você não pode editar usuários de outra região.');
            }

            if ($this->hasRegionalScopeProfile()) {
                $regiaoId = $this->resolveCurrentRegionId();
                $perfilUsers = PerfilUser::with(['perfil', 'instituicao'])
                    ->where('user_id', $user->id)
                    ->whereHas('instituicao', function ($query) use ($regiaoId) {
                        $query->where('instituicoes_instituicoes.id', $regiaoId)
                            ->orWhere('instituicoes_instituicoes.regiao_id', $regiaoId);
                    })
                    ->get();

                $user->setRelation('perfilUser', $perfilUsers);
            } else {
                $user->load(['perfilUser.perfil', 'perfilUser.instituicao']);
            }

            $perfis = app(AdminNovoUsuarioService::class)->execute();
            return view('admin.editar', compact('user', 'perfis', 'id'));
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('error', 'Erro ao abrir a página, por favor, tente mais tarde!');
        }
    }

    public function update(UpdateUsuarioRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            if ($this->hasRegionalScopeProfile() && !$this->canManageUserInCurrentRegion($user)) {
                return redirect()->route('admin.index')->with('error', 'Você não pode atualizar usuários de outra região.');
            }

            DB::beginTransaction();
            app(AdminEditarUsuarioService::class)->execute($request->all(), $id);
            DB::commit();
            return redirect()->route('admin.editar', $id)->with('success', 'Usuário atualizado com sucesso.');
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.editar', $id)->with('error', $e->getMessage());
        }
    }

    public function deletar($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($this->hasRegionalScopeProfile() && !$this->canManageUserInCurrentRegion($user)) {
                return redirect()->route('admin.index')->with('error', 'Você não pode excluir usuários de outra região.');
            }

            DB::beginTransaction();
            app(AdminDeletarUsuarioService::class)->execute($id);
            DB::commit();
            return redirect()->route('admin.index')->with('success', 'Usuário excluído com sucesso.');
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.index')->with('error', $e->getMessage());
        }
    }

    private function isCrieProfile(): bool
    {
        $perfilNome = (string) optional(session('session_perfil'))->perfil_nome;
        return Perfil::correspondeCodigo($perfilNome, Perfil::CODIGO_CRIE);
    }

    private function hasRegionalScopeProfile(): bool
    {
        return $this->isCrieProfile() || $this->isRegionalAdminProfile();
    }

    private function isRegionalAdminProfile(): bool
    {
        $perfilId = (int) optional(session('session_perfil'))->perfil_id;
        if ($perfilId <= 0) {
            return false;
        }

        $perfil = Perfil::find($perfilId);
        if (!$perfil) {
            return false;
        }

        $nomeNormalizado = Perfil::normalizarNome($perfil->nome);
        return $perfil->nivel === Perfil::NIVEL_REGIAO
            && str_contains($nomeNormalizado, 'administrador')
            && !Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_CRIE)
            && !Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA);
    }

    private function canManageUserInCurrentRegion(User $user): bool
    {
        $regiaoId = $this->resolveCurrentRegionId();
        if ($regiaoId <= 0) {
            return false;
        }

        if ((int) $user->regiao_id === $regiaoId) {
            return true;
        }

        return PerfilUser::where('user_id', $user->id)
            ->whereHas('instituicao', function ($query) use ($regiaoId) {
                $query->where('instituicoes_instituicoes.id', $regiaoId)
                    ->orWhere('instituicoes_instituicoes.regiao_id', $regiaoId);
            })
            ->exists();
    }

    private function resolveCurrentRegionId(): int
    {
        $instituicaoId = (int) optional(session('session_perfil'))->instituicao_id;
        if ($instituicaoId <= 0) {
            return 0;
        }

        $regiaoId = (int) InstituicoesInstituicao::where('id', $instituicaoId)->value('regiao_id');
        return $regiaoId > 0 ? $regiaoId : $instituicaoId;
    }
}
