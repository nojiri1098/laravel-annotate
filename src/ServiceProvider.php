<?php

namespace Nojiri1098\Annotate;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Nojiri1098\Annotate\Console\AnnotateGenerateCommand;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAnnotateGenerateCommand();
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerAnnotateGenerateCommand()
    {
        $this->app->singleton('command.annotate.generate', function ($app) {
            return new AnnotateGenerateCommand();
        });

        $this->commands('command.annotate.generate');
    }
}
