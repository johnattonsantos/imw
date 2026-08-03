<?php

namespace App\Http\Controllers;

use App\Exceptions\MembroNotFoundException;
use App\Http\Requests\StoreUsuarioLocalRequest;
use App\Http\Requests\UpdateUsuarioLocalRequest;
use App\Models\User;
use App\Models\PerfilUser;
use App\Services\ServicesUsuarios\DeletarUsuarioService;
use App\Services\ServicesUsuarios\EditarUsuarioLocalService;
use App\Services\ServicesUsuarios\ListUsuariosService;
use App\Services\ServicesUsuarios\NovoUsuarioService;
use App\Services\ServicesUsuarios\SalvarUsuarioLocalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $data = app(ListUsuariosService::class)->execute($request->all(), User::LOCAL);
        return view('usuarios.index', $data);
    }

    public function novo()
    {
        try {
            $perfis = app(NovoUsuarioService::class)->execute();
            return view('usuarios.novo', compact('perfis'));
        } catch (MembroNotFoundException $e) {
            return redirect()->route('usuarios.index')->with('error', __('Registro não encontrado.'));
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', __('Erro ao abrir a página, por favor, tente mais tarde!'));
        }
    }

    public function checkEmail(Request $request) {
        $email = $request->query('email');
        $instituicao_id = session()->get('session_perfil')->instituicao_id;
        $user = User::where('email', $email)->first();

        if ($instituicao_id) {
            if ($user) {
                $userInInstitution =   PerfilUser::where('user_id', $user->id)
                    ->where('instituicao_id', '=', $instituicao_id)->first();

                if ($userInInstitution) {
                    return response()->json(['exists' => true, 'context' => 'institution', 'user' => $userInInstitution]);
                } else {
                    return response()->json(['exists' => true, 'context' => 'general', 'user' => $user]);
                }
            }else {
                return response()->json(['exists' => false]);
            }
        }
    }

    public function store(StoreUsuarioLocalRequest $request)
    {
        try {
            DB::beginTransaction();
            app(SalvarUsuarioLocalService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('usuarios.novo')->with('success', __('Usuário cadastrado com sucesso.'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('usuarios.novo')->with('error', __('Ocorreu um erro ao cadastrar o usuário. Por favor, tente novamente.'));
        }
    }

    public function editar($id)
    {
        try {
            $user = User::findOrFail($id);
            $perfis = app(NovoUsuarioService::class)->execute();
            return view('usuarios.editar', compact('user', 'perfis', 'id'));
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', __('Erro ao abrir a página, por favor, tente mais tarde!'));
        }
    }

    public function update(UpdateUsuarioLocalRequest $request, $id)
    {

        try {

            DB::beginTransaction();
            app(EditarUsuarioLocalService::class)->execute($request->all(), $id);
            DB::commit();
            return redirect()->route('usuarios.editar', $id)->with('success', __('Usuário atualizado com sucesso.'));
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->route('usuarios.editar', $id)->with('error', $e->getMessage());
        }
    }

    public function deletar($id)
    {
        try {
            DB::beginTransaction();
            app(DeletarUsuarioService::class)->execute($id);
            DB::commit();
            return redirect()->route('usuarios.index')->with('success', __('O vínculo deste usuário com a instituição foi removido.'));
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->route('usuarios.index')->with('error', $e->getMessage());
        }
    }
}
