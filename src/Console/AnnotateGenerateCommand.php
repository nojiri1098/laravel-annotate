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

    protected $models;
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
        $this->models = collect([
            'user' => [
                'object' => new \Nojiri1098\Annotate\User(),
                'path' => __DIR__ . '/../User.php',
            ],
        ]);
        
        $this->models->each(function ($model, $key) {
            $methodNames = get_class_methods($model['object']);
            $this->extractMethods($methodNames);
            $this->annotateMethods();
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

    private function annotateMethods()
    {
        $annotation = [
            "",
            "/**",
            "* scope ======================",
        ];

        foreach ($this->scopes as $scope) {
            $annotation[] = "*   {$scope}";
        }

        $annotation[] = "* ";
        $annotation[] = "* accessor ===================";
        
        foreach ($this->accessors as $accessor) {
            $annotation[] = "*   {$accessor}";
        }

        $annotation[] = "* ";
        $annotation[] = "* mutator ===================";
        
        foreach ($this->mutators as $mutator) {
            $annotation[] =  "*   {$mutator}";
        }

        $annotation[] = "*/";

        $this->models->each(function ($model, $key) use ($annotation) {
            $file = \File::get($model['path']);
            $lines = explode(PHP_EOL, $file);
            // TODO: annotationがすでに存在する場合は上書きする
            array_splice($lines, 1, 0, $annotation);
            \File::put($model['path'], implode(PHP_EOL, $lines));
        });
    }
}
