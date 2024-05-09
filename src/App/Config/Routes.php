<?php

declare(strict_types=1);

namespace App\Config;

use Framework\App;
use App\Controllers\{AboutController, HomeController};

function registerRoutes(App $app)
{
    $app->get("/", [HomeController::class, "home"]);
    $app->get("/about", [AboutController::class, "about"]);
}
