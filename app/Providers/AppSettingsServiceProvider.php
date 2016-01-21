<?php

namespace BFACP\Providers;

use BFACP\Facades\Main;
use BFACP\Option;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppSettingsServiceProvider
 * @package BFACP\Providers
 */
class AppSettingsServiceProvider extends ServiceProvider
{
    /**
     * Init options array.
     *
     * @var array
     */
    private $options = [];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $format = function (&$options, $keys, $value) use (&$format) {
            $keys ? $format($options[ array_shift($keys) ], $keys, $value) : $options = $value;
        };

        $cache = $this->app['cache'];
        $config = $this->app['config'];

        try {
            $this->options = $cache->remember('site.options', 15, function () use ($format) {
                foreach (Option::all() as $option) {
                    $v = Main::stringToBool($option->option_value);
                    if (is_bool($v) && ! is_null($v)) {
                        $format($this->options, explode('.', $option->option_key), $v);
                    } else {
                        if ($option->option_key == 'site.languages') {
                            $values = [];
                            foreach (explode(',', $option->option_value) as $value) {
                                $values[ $value ] = Main::languages($value);
                            }
                            $format($this->options, explode('.', $option->option_key), $values);
                        } else {
                            $format($this->options, explode('.', $option->option_key), $option->option_value);
                        }
                    }
                }

                return $this->options;
            });
        } catch (QueryException $e) {
            Log::critical('Unable to load application settings.', [
                'exception' => $e->getMessage(),
            ]);
        }

        $config->set('bfacp', $this->options);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
