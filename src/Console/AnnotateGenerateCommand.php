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

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // exec("composer install");
        // $this->call('vendor:publish', ['--provider' => 'Maatwebsite\Excel\ExcelServiceProvider']);
        // $this->call('make:exception', ['name' => 'CsvImportException']);
        
        $this->annotateScopes();
        $this->annotateAccessors();
        $this->annotateMutators();
        $this->annotateRelations();
    }

    private function annotateScopes()
    {
        $this->info("Annotating scopes...");
    }

    private function annotateAccessors()
    {
        $this->info("Annotating accessors...");
    }

    private function annotateMutators()
    {
        $this->info("Annotating mutators...");
    }

    private function annotateRelations()
    {
        $this->info("Annotating relations...");
    }
}
