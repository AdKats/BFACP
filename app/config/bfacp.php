<?php

use BFACP\Option;

$options = [];

$format = function (&$options, $keys, $value) use (&$format) {
    $keys ? $format($options[ array_shift($keys) ], $keys, $value) : $options = $value;
};

try {
    $options = Cache::remember('site.options', 15, function () use ($options, $format) {
        foreach (Option::all() as $option) {
            if ($option->option_value === 1 || $option->option_value === '1' || $option->option_value === 'true') {
                $format($options, explode('.', $option->option_key), true);
            } elseif ($option->option_value === 0 || $option->option_value === '0' || $option->option_value === 'false') {
                $format($options, explode('.', $option->option_key), false);
            } else {
                if ($option->option_key == 'site.languages') {
                    $values = [];
                    foreach (explode(',', $option->option_value) as $value) {
                        $values[ $value ] = MainHelper::languages($value);
                    }
                    $format($options, explode('.', $option->option_key), $values);
                } else {
                    $format($options, explode('.', $option->option_key), $option->option_value);
                }
            }
        }

        return $options;
    });
} catch (\Illuminate\Database\QueryException $e) {
    $options = [];
}

return $options;
