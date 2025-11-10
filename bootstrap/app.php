<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		web: __DIR__.'/../routes/web.php',
		api: __DIR__.'/../routes/api.php',
		commands: __DIR__.'/../routes/console.php',
		channels: __DIR__.'/../routes/channels.php',
		health: '/up',
	)
	->withMiddleware(function (Middleware $middleware): void {
		//
		$middleware->statefulApi();
		
		// تسجيل middleware الإدارة
		$middleware->alias([
			'admin' => \App\Http\Middleware\AdminMiddleware::class,
			'update.last.login' => \App\Http\Middleware\UpdateLastLogin::class,
			'chef' => \App\Http\Middleware\EnsureUserIsChef::class,
			'moderate.content' => \App\Http\Middleware\EnforceContentModeration::class,
			'capture.referral' => \App\Http\Middleware\CaptureReferralFromRequest::class,
			'referral.partner' => \App\Http\Middleware\EnsureReferralPartner::class,
			'set.locale' => \App\Http\Middleware\SetLocale::class,
			'policy.consent' => \App\Http\Middleware\EnsurePoliciesAccepted::class,
		]);
		
		// إضافة middleware لتحديث آخر تسجيل دخول لجميع الطلبات المصادق عليها
		$middleware->web(
			prepend: [
				\App\Http\Middleware\EnforceContentModeration::class,
			],
			append: [
				\App\Http\Middleware\SetLocale::class,
				\App\Http\Middleware\CaptureReferralFromRequest::class,
				\App\Http\Middleware\UpdateLastLogin::class,
				\App\Http\Middleware\EnsurePoliciesAccepted::class,
				\App\Http\Middleware\ApplyXssProtection::class,
			]
		);

		$middleware->api(
			prepend: [
				\App\Http\Middleware\EnforceContentModeration::class,
			],
			append: [
				\App\Http\Middleware\ApplyXssProtection::class,
			]
		);
	})
	->withExceptions(function (Exceptions $exceptions): void {
		//
	})->create();
