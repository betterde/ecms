<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('money', 'App\Rules\Money@passes');
        Relation::morphMap([
            'user' => User::class,
            'customer' => Customer::class
        ]);
        Carbon::setLocale('zh');
    }
}
