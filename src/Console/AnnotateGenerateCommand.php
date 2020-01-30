<?php

namespace Nojiri1098\Annotate\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        $this->models = collect(glob(app_path('*.php')));
        
        $this->models->each(function ($model, $key) {
            $this->extractMethods($model);
            $this->annotateMethods($model);
        });
    }

    private function extractMethods($model)
    {
        $file = \File::get($model);

        $this->info("Extracting scopes...");

        preg_match_all('/public function scope(.+)\(/', $file, $matches);
        foreach ($matches[1] as $scope) {
            $this->scopes[] = Str::lower($scope);
        }

        $this->info("Extracting accessors...");

        preg_match_all('/public function get(.+)Attribute\(/', $file, $matches);
        foreach ($matches[1] as $accessor) {
            $this->accessors[] = Str::snake($accessor);
        }

        $this->info("Extracting mutators...");

        preg_match_all('/public function set(.+)Attribute\(/', $file, $matches);
        foreach ($matches[1] as $mutator) {
            $this->mutators[] = Str::snake($mutator);
        }

        $this->info("Extracting relations...");
    }

    private function annotateMethods($model)
    {
        $annotation = [
            "",
            self::START,
        ];

        if (! empty($this->scopes)) {
            $annotation[] = self::HEADER['scope'];
            foreach ($this->scopes as $scope) {
                $annotation[] = self::BODY . $scope;
            }
            $annotation[] = self::DELIM;
        }

        if (! empty($this->accessors)) {
            $annotation[] = self::HEADER['accessor'];
            foreach ($this->accessors as $accessor) {
                $annotation[] = self::BODY . $accessor;
            }
            $annotation[] = self::DELIM;
        }

        if (! empty($this->mutators)) {
            $annotation[] = self::HEADER['mutator'];
            foreach ($this->mutators as $mutator) {
                $annotation[] =  self::BODY . $mutator;
            }
        }
        
        $annotation[] = self::END;
        
        $file = \File::get($model);

        // annotation がすでにあれば削除する
        $file = preg_replace('/\/\*\*.+========.+?\*\/\n\n/s', '', $file);

        $lines = explode(PHP_EOL, $file);
        array_splice($lines, 1, 0, $annotation);
        \File::put($model, implode(PHP_EOL, $lines));
    }
}
