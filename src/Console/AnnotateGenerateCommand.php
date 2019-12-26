<?php

namespace Nojiri1098\Annotate\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AnnotateGenerateCommand extends Command
{
    /**
     * The console command name.
     *
     * @param string
     */
    protected $signature = 'annotate:generate';
    
    /**
     * The console command description.
     *
     * @param string
     */
    protected $description = '';

    protected $methodNames;

    protected $scopes    = [];
    protected $accessors = [];
    protected $mutators  = [];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $models = collect([
            new \Nojiri1098\Annotate\User(),
        ]);
        
        $models->each(function ($model, $key) {
            $methodNames = get_class_methods($model);
            $this->extractMethods($methodNames);
        });
    }

    private function extractMethods(array $methodNames)
    {
        $this->info("Extracting scopes...");

        foreach ($methodNames as $method) {
            preg_match('/^scope(.+)/', $method, $matches);
            if (isset($matches[1])) {
                $this->warn($this->scopes[] = Str::lower($matches[1]));
            };
        }

        $this->info("Extracting accessors...");

        foreach ($methodNames as $method) {
            preg_match('/^get(.+?)Attribute$/', $method, $matches);
            if (isset($matches[1])) {
                $this->warn($this->accessors[] = Str::snake($matches[1]));
            };
        }

        $this->info("Extracting mutators...");

        foreach ($methodNames as $method) {
            preg_match('/^set(.+?)Attribute$/', $method, $matches);
            if (isset($matches[1])) {
                $this->warn($this->mutators[] = Str::snake($matches[1]));
            };
        }

        $this->info("Extracting relations...");
    }
}
