<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Mail\ErrorMail;
use Illuminate\Support\Facades\Mail;
use Sentinel;

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
        if (Sentinel::check()) {
            $email = 'jelena.juras@duplico.hr';
            $url = $_SERVER['REQUEST_URI'];
            try {
                //Mail::to($email)->send(new ErrorMail( $exception, $url)); 
            } catch (\Throwable $th) {
                session()->flash('error', __('ctrl.email_error'));
				return redirect()->back();
            }
        }
        return parent::render($request, $exception);
    }
}