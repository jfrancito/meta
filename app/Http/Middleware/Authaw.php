<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\User;

class Authaw
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(!Session::has('empresas_meta')){
            if(!Session::has('usuario_meta')){
                return Redirect()->to('/login');
            }else{
                return Redirect()->to('/acceso');
            }
        }else{
            if(Session::has('usuario_meta')){

                /********* nuevo **********/
                $usuario                    =   User::where('id','=',Session::get('usuario_meta')->id)
                                                ->where('activo','=',1)->first();                             
                if(count($usuario)<=0){

                    Session::forget('usuario_meta');
                    Session::forget('listamenu_meta');
                    Session::forget('empresas_meta');
                    Session::forget('centros_meta');
                    Session::forget('listaopciones_meta');
                    Session::forget('color_meta');
                    return Redirect()->to('/login');
                }  
                /**************************/

                return $next($request);


            }else{
                return Redirect()->to('/login');
            }

        }

        
    }
}
