<?php

use Illuminate\Support\Facades\File as File;

$files = File::files(__DIR__ . '/Events');

foreach ($files as $file) {
    File::requireOnce($file);
}
