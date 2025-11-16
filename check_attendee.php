<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$service = app(App\Services\GoogleMeetService::class);
var_dump($service->eventHasAttendee('qm4nu7olbdia80hqts4ee956o8','abdalluhadoud@gmail.com','primary'));
