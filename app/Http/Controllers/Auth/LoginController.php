<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * The user has been authenticated.
     * 
     * Establece el contexto inicial del usuario después del login:
     * - Superusuario: Sin contexto (puede ver todo)
     * - Admin/Propietario: Sin contexto específico (ve todo su grupo)
     * - Usuario operativo: Establece contexto de su empresa principal
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated($request, $user)
    {
        // Simple: solo redirigir según tiene grupo o no
        if ($user->grupo_empresa_id && $user->grupoEmpresa) {
            $this->redirectTo = '/' . $user->grupoEmpresa->slug;
        } else {
            $this->redirectTo = '/home';
        }

        return redirect($this->redirectTo);
    }

}
