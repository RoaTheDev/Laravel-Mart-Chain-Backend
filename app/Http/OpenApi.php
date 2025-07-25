<?php

namespace App\Http;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Roa API",
 *     description="API documentation for a Mart chain with JWT Authentication"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     in="header",
 *     name="Token based Authentication",
 *     description="Login with email and password to get the authentication token"
 * )
 */
class OpenApi
{
    // This class is only used for OpenAPI annotations.
}
