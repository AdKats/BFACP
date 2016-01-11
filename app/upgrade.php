<?php

use Illuminate\Support\Facades\Artisan;

Artisan::call('migrate', ['--force' => true]);
