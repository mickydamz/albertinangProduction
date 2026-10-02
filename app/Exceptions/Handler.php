<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // Map framework exceptions (ModelNotFoundException → 404, TokenMismatch → 419,
        // etc.) to their proper HTTP status *before* the catch-all below decides.
        // Without this, a missing model surfaces as a generic 500.
        $e = $this->prepareException($e);

        if (!$e instanceof \Illuminate\Validation\ValidationException
            && !$e instanceof \Illuminate\Auth\AuthenticationException
            && !$e instanceof \Illuminate\Auth\Access\AuthorizationException
            && (!$e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface || $e->getStatusCode() >= 500)) {
            $reference = (string) \Illuminate\Support\Str::uuid();
            \Log::error('Request failed', ['reference' => $reference, 'exception' => $e]);
            $message = 'We could not complete this request. Please try again or contact support with reference ' . $reference;
            return $request->expectsJson()
                ? response()->json(['message' => $message, 'reference' => $reference], 500)
                : response()->view('errors.safe', compact('message'), 500);
        }
        return parent::render($request, $e);
    }
}
