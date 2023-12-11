<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('transpose', function () {
            $new = [];

            for ($i = 0; $i < count($this->first()); $i++) {
                for ($j = 0; $j < $this->count(); $j++) {
                    data_set($new, "$i.$j", data_get($this, "$j.$i"));
                }
            }

            return collect($new)->mapInto($this::class);
        });
    }
}
