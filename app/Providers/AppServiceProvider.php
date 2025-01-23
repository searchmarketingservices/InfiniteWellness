<?php

namespace App\Providers;

use Blade;
use Schema;
use App\Models\Module;
use App\Models\Setting;
use App\Rules\ValidRecaptcha;
use App\Observers\ModuleObserver;
use App\Observers\SettingObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $setting = DB::table('settings')->get();
        $applogo = $setting->where('key', 'app_logo')->first()->value;
        $address = $setting->where('key', 'hospital_address')->first()->value;
        $app_name = $setting->where('key', 'app_name')->first()->value;

        View::share('applogo', $applogo);
        View::share('address', $address);
        View::share('app_name', $app_name);
        Paginator::useBootstrap();
        Module::observe(ModuleObserver::class);
        Setting::observe(SettingObserver::class);
        Validator::extend('recaptcha', ValidRecaptcha::class);
        app()->useLangPath(base_path('lang'));
        Schema::defaultStringLength(191);
        Blade::if('module', function ($name, $module = null) {
            $module = $module->where('name', $name)->first();
            //            $module
            if ($module) {
                return $module->is_active;
            }

            return false;
        });
    }
}
