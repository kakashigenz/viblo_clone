<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Viblo API",
    version: "0.0.1"
)]
#[OA\Server("http://api.viblo.test/api")]
abstract class Controller
{
    //
}
