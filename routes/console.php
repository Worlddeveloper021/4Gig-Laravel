<?php

use Illuminate\Foundation\Inspiring;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sniff', function () {
    $process = new Process(['./vendor/bin/php-cs-fixer', 'fix', '-vvv', '--dry-run', '--show-progress=dots']);
    $process->run();
    $this->comment($process->getOutput());
})->purpose('Display files that need to reformat');

Artisan::command('lint', function () {
    $process = new Process(['./vendor/bin/php-cs-fixer', 'fix', '-vvv', '--show-progress=dots']);
    $process->run();
    $this->comment($process->getOutput());
})->purpose('Perform the formatting fo files that need to reformat');
