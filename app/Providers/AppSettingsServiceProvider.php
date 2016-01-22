<?php

namespace BFACP\Providers;

use BFACP\Facades\Main;
use BFACP\Option;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

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
            /*
             * Fetches application settings from database and caches them for 15 minutes.
             * @var array
             */
            $this->options = $cache->remember('site.options', 15, function () use ($format) {
                Log::info('Fetching application settings from database.');

                $settings = Option::all();

                $settings->each(function ($option) use (&$format) {
                    $v = Main::stringToBool($option->option_value);
                    $optionKey = explode('.', $option->option_key);

                    if (env('APP_DEBUG')) {
                        Log::debug(sprintf('Setting "%s" with "%s".', $option->option_key, $option->option_value));
                    }

                    if (is_bool($v) && ! is_null($v)) {
                        $format($this->options, $optionKey, $v);
                    } else {
                        if ($option->option_key == 'site.languages') {
                            $values = [];
                            foreach (explode(',', $option->option_value) as $value) {
                                $lang = main::languages($value);
                                $values[$value] = $lang;
                                if (env('APP_DEBUG')) {
                                    Log::debug(sprintf('Adding %s language to list.', $lang));
                                }
                            }
                            $format($this->options, $optionKey, $values);
                        } else {
                            $format($this->options, $optionKey, $option->option_value);
                        }
                    }
                });

                return $this->options;
            });

            if (empty($this->options)) {
                throw new Exception('Application settings array is empty.');
            }
        } catch (QueryException $e) {
            Log::critical('Unable to load application settings from database.', [
                'exception' => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            Log::critical('Application settings were not set.', [
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
