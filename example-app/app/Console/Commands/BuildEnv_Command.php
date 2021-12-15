<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class BuildEnv_Command extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buildEnv:dev
                    {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs commands to help build dev deploy process';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if  (! $this->confirmToProceed()) {
         $this->output->error('ABORT');
            return false;
        }

        $this->comment( 'copy fresh new .env file');
        exec(' cp .env.example .env',$output);
        $this->comment( implode( PHP_EOL, $output ) );

        $this->output->section('collection Database url');
        // COLLECT ROOT APP EXEC
        exec("pwd", $output);

        $dbPath = implode( PHP_EOL, $output ).'/database'.'/database.sqlite';

        $this->output->section('create database');
        $this->comment( 'create path at: '.$dbPath );
        exec("touch $dbPath", $output);
        $this->comment( implode( PHP_EOL, $output ) );


        $this->output->section('generate for env file with collected information ');
        $this->call('buildEnv:generate', [
            'env' => $dbPath
        ]);

        $this->output->section('new key');
        $this->call('key:generate');



        $this->output->section('new key');
        $this->call('migrate:fresh',[
            '--seed'=> true,
            '--step'=> true
        ]);

        $this->output->success('FINISHED');
        return true;
    }
}
