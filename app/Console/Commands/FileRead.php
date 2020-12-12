<?php

namespace App\Console\Commands;

use App\Http\Controllers\FileReadController;
use Illuminate\Console\Command;

class FileRead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:read';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start to read the files';

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
        $start = new FileReadController();
        $start->start();
    }
}
