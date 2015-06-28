<?php

$files = File::files(__DIR__ . '/Events');

foreach ($files as $file) {
    File::requireOnce($file);
}
