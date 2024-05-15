<?php

declare(strict_types=1);

namespace App\Config;

use Framework\App;
use App\Controllers\{AboutController, HomeController, AuthController};

function registerRoutes(App $app)
{
    $app->get("/", [HomeController::class, "home"]);
    $app->get("/about", [AboutController::class, "about"]);
    $app->get("/register", [AuthController::class, "registerView"]);
    $app->get("/login", [AuthController::class, "loginView"]);

    $app->post("/register", [AuthController::class, "register"]);
    $app->post("/login", [AuthController::class, "login"]);
}
