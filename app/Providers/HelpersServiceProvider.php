<?php

namespace App\Providers;

use App\Helpers\Interfaces\SignInsHelperInterface;
use App\Helpers\SignInsHelper;
use Illuminate\Support\ServiceProvider;

/**
 * Class HelpersServiceProvider.
 */
class HelpersServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            SignInsHelperInterface::class,
            SignInsHelper::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            SignInsHelperInterface::class,
        ];
    }
}
