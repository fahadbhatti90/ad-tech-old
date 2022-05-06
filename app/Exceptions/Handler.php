<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof TokenMismatchException){
            session()->regenerateToken();
            return response()->json([
                "isLogged" => false,
                "activeRole" => session()->has("activeRole") ? session("activeRole") : 0,
                "csrf" => \csrf_token(),
                "errorType"=>"TokenMismatch"
            ], 401);
        }
        // if ($this->isHttpException($exception))
        // {
        //     if ($exception->getStatusCode() == 404)
        //         return redirect('/');
    
        //     // if ($e->getStatusCode() == 500)
        //     //    return redirect()->guest('home');
        // }
    
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        if($request->expectsJson()){
            return response()->json([
                "isLogged" => false,
                "activeRole" => session()->has("activeRole") ? session("activeRole") : 0,
                "csrf" => \csrf_token()
            ], 401);
        }

        $guard = array_get($exception->guards(),0);
        switch ($guard) {
            case 'client':
                $login = 'client.login';
                break;
            default:
                $login = 'login';
                break;
        }//end switch
        return redirect()->guest(route($login));
    }//end exception function
}
