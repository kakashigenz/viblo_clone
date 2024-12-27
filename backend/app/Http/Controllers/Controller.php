<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Viblo API doc',
    description: "Viblo API documentation for blog Viblo",
    version: '1.0.0',
)]
#[OA\Server(
    url: "http://api.viblo.test/api",
)]
#[OA\SecurityScheme(
    type: "http",
    scheme: "bearer",
    securityScheme: "bearerAuth"
)]
abstract class Controller
{
    //
}
