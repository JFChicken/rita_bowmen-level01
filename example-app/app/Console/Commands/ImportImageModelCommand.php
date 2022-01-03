<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportImageModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bac:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'imports any image that isnt in the db';

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

        $this->output->title('scanning output dir');


        $this->output->section('output files');
        $this->output->note($imageBundle = Storage::allFiles('output/'));

        $this->output->section('import loop');

        foreach ($imageBundle as $outputImage){
            if (Storage::exists($outputImage)) {
                $this->output->info("File $outputImage Exists");
                $time = Storage::lastModified($outputImage);
                $size = Storage::size($outputImage);
                Carbon::createFromTimestamp($time);
                // TAKE THE SIZE AND MAKE IT IN KB

//                $filename = explode('//',$outputImage);

                $filename = pathinfo($outputImage)['basename'];



                $this->output->note([
                    'create at'=>Carbon::createFromTimestamp($time),
                    'size'=>$this->formatBytes($size),
                    'base Name'=>$filename,
                ]);

                if (Storage::missing('public/'.$filename)) {
                    Storage::move('output/' . $filename, 'public/' . $filename);
                    $this->output->info('moved file');
                }
            }
        }

        return 0;
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        // $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
