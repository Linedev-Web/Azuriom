<?php

namespace Azuriom\Providers;

use Azuriom\Models\Setting;
use Exception;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            $settings = Setting::all();

            foreach ($settings as $setting) {
                switch ($setting->name) {
                    case 'locale':
                        $this->app->setLocale($setting->value);
                        break;
                    case 'timezone':
                        date_default_timezone_set($setting->value);
                        // no break
                    case 'url':
                        config(['app.'.$setting->name => $setting->value]);
                        break;
                    case 'hash':
                        if ($setting->value === 'argon2id' && ! defined('PASSWORD_ARGON2ID')) {
                            $setting->value = 'argon';
                        }

                        if (config('hashing.driver') !== $setting->value) {
                            config(['hashing.driver' => $setting->value]);
                        }
                        break;
                }
            }

            config($settings->mapWithKeys(function ($setting) {
                return ['setting.'.$setting->name => $setting->value];
            })->toArray());
        } catch (Exception $e) {
            //
        }
    }
}
