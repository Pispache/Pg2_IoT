<?php
use Illuminate\Support\Facades\Artisan;
Artisan::command('iot:info', function () {
    $this->info('Sistema IoT para monitoreo de bodega de medicamentos');
    $this->line('API: POST /api/v1/readings');
})->purpose('Muestra información básica del proyecto IoT.');
