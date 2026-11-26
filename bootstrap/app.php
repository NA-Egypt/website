<?php

use App\Exceptions\UserFacingException;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RoleRedirect;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLanguageDirection;
use App\Http\Middleware\TrackApiUsage;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            SecurityHeaders::class,
        ]);

        $middleware->api(append: [
            TrackApiUsage::class,
        ]);

        $middleware->alias([
            /**** OTHER MIDDLEWARE ALIASES ****/
            'localize' => LaravelLocalizationRoutes::class,
            'localizationRedirect' => LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => LocaleSessionRedirect::class,
            'localeCookieRedirect' => LocaleCookieRedirect::class,
            'localeViewPath' => LaravelLocalizationViewPath::class,
            'setLanguageDirection' => SetLanguageDirection::class,
            'role.redirect' => RoleRedirect::class,
            'auth' => Authenticate::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => 'validation_failed',
                    'errors' => $e->errors(),
                ], $e->status);
            }

            if ($e instanceof UserFacingException) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->errorCode,
                    'errors' => null,
                ], $e->statusCode);
            }

            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'message' => __('errors.unauthenticated'),
                    'code' => 'unauthenticated',
                    'errors' => null,
                ], 401);
            }

            if ($e instanceof AuthorizationException
                || $e instanceof UnauthorizedException) {
                return response()->json([
                    'message' => __('errors.forbidden'),
                    'code' => 'forbidden',
                    'errors' => null,
                ], 403);
            }

            if ($e instanceof ModelNotFoundException
                || $e instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => __('errors.not_found'),
                    'code' => 'not_found',
                    'errors' => null,
                ], 404);
            }

            if ($e instanceof ThrottleRequestsException) {
                return response()->json([
                    'message' => __('errors.too_many_requests'),
                    'code' => 'too_many_requests',
                    'errors' => null,
                ], 429);
            }

            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                [$code, $message] = match ($status) {
                    401 => ['unauthenticated', __('errors.unauthenticated')],
                    403 => ['forbidden', __('errors.forbidden')],
                    404 => ['not_found', __('errors.not_found')],
                    422 => ['validation_failed', $e->getMessage() ?: __('errors.validation_failed')],
                    429 => ['too_many_requests', __('errors.too_many_requests')],
                    default => [
                        $status >= 500 ? 'server_error' : 'http_error',
                        $status >= 500 ? __('errors.generic') : ($e->getMessage() ?: __('errors.generic')),
                    ],
                };

                return response()->json([
                    'message' => $message,
                    'code' => $code,
                    'errors' => null,
                ], $status);
            }

            return response()->json([
                'message' => __('errors.generic'),
                'code' => 'server_error',
                'errors' => null,
            ], 500);
        });
    })->create();
