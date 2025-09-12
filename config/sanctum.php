    <?php

    return [

        /*
        |--------------------------------------------------------------------------
        | Sanctum Guards
        |--------------------------------------------------------------------------
        |
        | This array contains the authentication guards that will be checked when
        | Sanctum is trying to authenticate a request. If none of these guards
        | are able to authenticate the request, Sanctum will use the bearer
        | token that's present on an incoming request for authentication.
        |
        */

        'guard' => ['web'],

        /*
        |--------------------------------------------------------------------------
        | Expiration Minutes
        |--------------------------------------------------------------------------
        |
        | This value controls the number of minutes until an issued token will be
        | considered expired. This will override any values set in the token's
        | "expires_at" attribute, but first-party sessions are not affected.
        |
        */

        'expiration' => null,

        /*
        |--------------------------------------------------------------------------
        | Token Prefix
        |--------------------------------------------------------------------------
        |
        | Sanctum can prefix new tokens in order to take advantage of numerous
        | security scanning initiatives maintained by open source platforms
        | that notify developers if they commit tokens into repositories.
        |
        */

        'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

        /*
        |--------------------------------------------------------------------------
        | Sanctum Middleware
        |--------------------------------------------------------------------------
        |
        | These middleware are mostly used for cookie-based authentication. You
        | can keep them here, but they are not required when using tokens.
        |
        */

        'middleware' => [
            // 'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
            // 'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
            // 'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ],
    ];
