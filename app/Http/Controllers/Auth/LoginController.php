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

    protected function authenticated($request, $user)
    {

        Log::info('ARRAY : Usuario autenticado: ' . $user->toJson());

        if($user->esSuperusuario()){
            $this->redirectTo = '/admin';
        } elseif($user->esAdministradorGeneral() || $user->esPropietario()){
            $this->redirectTo = '/'.$user->grupoEmpresa->slug;
        } else {
            $empresa = $user->getEmpresaPrincipal();
            if ($empresa) {
                $this->redirectTo = '/'.$user->grupoEmpresa->slug.'/erp/'.$empresa->slug;
            } else {
                // Si no tiene empresa asignada, redirigir al dashboard del grupo
                $this->redirectTo = '/'.$user->grupoEmpresa->slug;
            }
        }

        // enviar a controlador de dashboardController
        return redirect($this->redirectTo);
    }


}
