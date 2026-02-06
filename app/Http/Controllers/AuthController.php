<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OwenIt\Auditing\Models\Audit;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user) {
                try {
                    Audit::create([
                        'user_type' => get_class($user),
                        'user_id' => $user->id,
                        'event' => 'login',
                        'auditable_type' => get_class($user),
                        'auditable_id' => $user->id,
                        'old_values' => [],
                        'new_values' => [
                            'message' => 'Login realizado com sucesso',
                        ],
                        'url' => $request->fullUrl(),
                        'ip_address' => $request->ip(),
                        'user_agent' => substr((string) $request->userAgent(), 0, 1023),
                        'tags' => 'auth',
                    ]);
                } catch (\Throwable $e) {
                    // Nunca bloquear o login por falha de auditoria.
                }
            }

            return redirect()->route('dashboard');
        }

        try {
            Audit::create([
                'user_type' => null,
                'user_id' => null,
                'event' => 'login_failed',
                'auditable_type' => \App\Models\User::class,
                'auditable_id' => 0,
                'old_values' => [],
                'new_values' => [
                    'email' => $request->input('email'),
                    'message' => 'Tentativa de login sem sucesso',
                ],
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1023),
                'tags' => 'auth',
            ]);
        } catch (\Throwable $e) {
            // Nunca bloquear o fluxo por falha de auditoria.
        }

        session()->flash('error', 'E-mail ou senha inválida!');
        return redirect()->back();
    }

    public function showResetRequestForm() {
        return view('auth.esqueciSenha');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            try {
                Audit::create([
                    'user_type' => get_class($user),
                    'user_id' => $user->id,
                    'event' => 'logout',
                    'auditable_type' => get_class($user),
                    'auditable_id' => $user->id,
                    'old_values' => [],
                    'new_values' => [
                        'message' => 'Logout realizado com sucesso',
                    ],
                    'url' => $request->fullUrl(),
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 1023),
                    'tags' => 'auth',
                ]);
            } catch (\Throwable $e) {
                // Nunca bloquear o logout por falha de auditoria.
            }
        }

        Auth::logout();
        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function submitResetRequest(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Verifica o status da solicitação de redefinição de senha
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(['status' => __($status)]);
        } else {
            // Se não for bem-sucedido, passe uma mensagem de erro para a visão
            return back()->withErrors(['email' => __($status)]);
        }
    }



    public function reset(Request $request)
    {
        // Validação dos dados do formulário
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required|string',
        ]);

        // Lógica para redefinir a senha do usuário
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        // Verificar o status da redefinição de senha
        if ($status === Password::PASSWORD_RESET) {
            return back()->with('status', 'Senha redefinida com sucesso!');
        } else {
            return back()->withErrors(['email' => 'Não foi possível redefinir a senha. Token expirado!']);
        }
    }

    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        return view('auth.reset')->with(compact('token', 'email'));
    }

}
