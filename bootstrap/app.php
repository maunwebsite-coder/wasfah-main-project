<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		web: __DIR__.'/../routes/web.php',
		api: __DIR__.'/../routes/api.php',
		commands: __DIR__.'/../routes/console.php',
		health: '/up',
	)
	->withMiddleware(function (Middleware $middleware): void {
		//
		$middleware->statefulApi();
		
		// تسجيل middleware الإدارة
		$middleware->alias([
			'admin' => \App\Http\Middleware\AdminMiddleware::class,
			'update.last.login' => \App\Http\Middleware\UpdateLastLogin::class,
		]);
		
		// إضافة middleware لتحديث آخر تسجيل دخول لجميع الطلبات المصادق عليها
		$middleware->web(append: [
			\App\Http\Middleware\UpdateLastLogin::class,
		]);
	})
	->withExceptions(function (Exceptions $exceptions): void {
		//
	})->create();
