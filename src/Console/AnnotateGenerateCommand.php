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

    const START  = '/**';
    const DELIM  = ' * ';
    const BODY   = ' *   ';
    const END    = ' */';
    const HEADER = [
        'scope'    => ' * scope ======================',
        'accessor' => ' * accessor ===================',
        'mutator'  => ' * mutator ====================',
    ];

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
            self::START,
            self::HEADER['scope'],
        ];

        foreach ($this->scopes as $scope) {
            $annotation[] = self::BODY . $scope;
        }

        $annotation[] = self::DELIM;
        $annotation[] = self::HEADER['accessor'];
        
        foreach ($this->accessors as $accessor) {
            $annotation[] = self::BODY . $accessor;
        }

        $annotation[] = self::DELIM;
        $annotation[] = self::HEADER['mutator'];
        
        foreach ($this->mutators as $mutator) {
            $annotation[] =  self::BODY . $mutator;
        }

        $annotation[] = self::END;

        $this->models->each(function ($model, $key) use ($annotation) {
            $file = \File::get($model['path']);

            // annotation がすでにあれば削除する
            $file = preg_replace('/\/\*\*.+========.+?\*\/\n\n/s', '', $file);

            $lines = explode(PHP_EOL, $file);
            array_splice($lines, 1, 0, $annotation);
            \File::put($model['path'], implode(PHP_EOL, $lines));
        });
    }
}
