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
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $data = app(AdminListUsuariosService::class)->execute($request->all(), User::GERAL);
        return view('admin.index', $data);
    }

    public function pesquisarMembro(Request $request)
    {
        $nome = trim((string) $request->input('nome'));
        $cpf = preg_replace('/\D/', '', (string) $request->input('cpf'));
        $searched = $nome !== '' || $cpf !== '';
        $membros = collect();

        if ($searched) {
            $membros = DB::table('membresia_membros as mm')
                ->leftJoin('instituicoes_instituicoes as regiao', 'regiao.id', '=', 'mm.regiao_id')
                ->leftJoin('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'mm.distrito_id')
                ->leftJoin('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'mm.igreja_id')
                ->where('mm.vinculo', 'M')
                ->whereIn('mm.status', ['A', 'I'])
                ->when($nome !== '', fn ($query) =>
                    $query->where('mm.nome', 'like', '%' . $nome . '%'))
                ->when($cpf !== '', fn ($query) =>
                    $query->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(mm.cpf, '.', ''), '-', ''), '/', ''), ' ', '') LIKE ?", ['%' . $cpf . '%']))
                ->select([
                    'mm.id',
                    'mm.nome',
                    'mm.cpf',
                    'mm.status',
                    DB::raw('COALESCE(regiao.nome, regiao_distrito.nome, regiao_igreja.nome) as regiao_nome'),
                    'distrito.nome as distrito_nome',
                    'igreja.nome as igreja_nome',
                    DB::raw("(SELECT CASE
                        WHEN mc.telefone_preferencial IS NOT NULL AND mc.telefone_preferencial <> '' THEN mc.telefone_preferencial
                        WHEN mc.telefone_alternativo IS NOT NULL AND mc.telefone_alternativo <> '' THEN mc.telefone_alternativo
                        ELSE mc.telefone_whatsapp
                    END FROM membresia_contatos mc
                    WHERE mc.membro_id = mm.id AND mc.deleted_at IS NULL
                    ORDER BY mc.id DESC
                    LIMIT 1) as telefone"),
                ])
                ->leftJoin('instituicoes_instituicoes as regiao_distrito', 'regiao_distrito.id', '=', 'distrito.instituicao_pai_id')
                ->leftJoin('instituicoes_instituicoes as regiao_igreja', 'regiao_igreja.id', '=', 'igreja.regiao_id')
                ->orderBy('mm.nome')
                ->limit(100)
                ->get();
        }

        return view('admin.pesquisar-membro', compact('membros', 'searched', 'nome', 'cpf'));
    }

    public function novo()
    {
        try {
            $perfis = app(AdminNovoUsuarioService::class)->execute();
            return view('admin.novo', compact('perfis'));
        } catch (MembroNotFoundException $e) {
            return redirect()->route('admin.index')->with('error', __('Registro não encontrado.'));
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('error', __('Erro ao abrir a página, por favor, tente mais tarde!'));
        }
    }

    public function store(StoreUsuarioRequest $request)
    {
        try {
            DB::beginTransaction();
            app(SalvarUsuarioService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('admin.novo')->with('success', __('Usuário cadastrado com sucesso.'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.novo')->with('error', $e);
        }
    }

    public function editar($id)
    {
        try {
            $user = User::findOrFail($id);
            $perfis = app(AdminNovoUsuarioService::class)->execute();
            return view('admin.editar', compact('user', 'perfis', 'id'));
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('error', __('Erro ao abrir a página, por favor, tente mais tarde!'));
        }
    }

    public function update(UpdateUsuarioRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            app(AdminEditarUsuarioService::class)->execute($request->all(), $id);
            DB::commit();
            return redirect()->route('admin.editar', $id)->with('success', __('Usuário atualizado com sucesso.'));
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.editar', $id)->with('error', $e->getMessage());
        }
    }

    public function deletar($id)
    {
        try {
            DB::beginTransaction();
            app(AdminDeletarUsuarioService::class)->execute($id);
            DB::commit();
            return redirect()->route('admin.index')->with('success', __('Usuário excluído com sucesso.'));
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.index')->with('error', $e->getMessage());
        }
    }
}
