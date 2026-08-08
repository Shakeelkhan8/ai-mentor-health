<?php

namespace App\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Hook for Sentry/Bugsnag: report($e) is enough once the SDK is installed.
        });

        $this->renderable(function (DomainException $e, Request $request): Response|JsonResponse|RedirectResponse|null {
            if ($request->expectsJson()) {
                $status = match ($e->errorCode) {
                    'conversation_not_found', 'habit_not_found' => 404,
                    'ai_not_configured', 'stripe_not_configured' => 503,
                    'ai_provider_error', 'stripe_checkout_failed' => 503,
                    'subscription_already_active' => 409,
                    default => 422,
                };

                return response()->json([
                    'message' => $e->getMessage(),
                    'error_code' => $e->errorCode,
                ], $status);
            }

            return null;
        });
    }
}
