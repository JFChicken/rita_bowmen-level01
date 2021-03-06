<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Faker;

class BuildAChicken_Command extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bac:test
    {--eyeColor=010a0b}
    {--bodyColor=0ffa5b}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds a base test Creature from saved values in DB for colors and layers...';

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
        $this->output->title('Build A Chicken: Today!!');

        $this->output->section('get colors for each layer');
        $this->output->section('get each layer from bundle 01');

        $this->output->block(
            Storage::allFiles('bundle01/'));


        $imageBundle = Storage::allFiles('bundle01/');


        foreach ($imageBundle as $layer) {
            $msg = "Loading ... $layer";
            $this->output->section($msg);
            $filePath = Storage::path($layer);
            $layers[] = imagecreatefrompng($filePath);
        }

        // build the final image now

        // TODO: make sure the images are build to the correct specs for each bundle build

        $imagex = imagesx($layers[0]);
        $imagey = imagesy($layers[0]);
        $tc = imagecreatetruecolor($imagex, $imagey);
        imagealphablending($tc, false);
        imagefilledrectangle($tc, 0, 0, $imagex, $imagey, imagecolorallocatealpha($tc, 255, 255, 255, 127));
        imagealphablending($tc, true);
        $imagey = 0;
        $imagex = 0;
        $msg = "Building X:$imagex x Y:$imagey";
        $this->output->section($msg);

        $faker = Faker\Factory::create();

        $bundle = collect([
            [
                $this->input->getOption('bodyColor'), // 'bundle01/af01-base.png'
                $faker->hexColor(),// 'bundle01/af01-effect01.png',
                $faker->hexColor(),//'bundle01/af01-effect02.png',
                $faker->hexColor(),//'bundle01/af01-effect03.png',
                $this->input->getOption('eyeColor'), //'bundle01/af01-eyecolor.png'
                $faker->hexColor(),//'bundle01/af01-eyewhite.png',
                $faker->hexColor(),//'bundle01/af01-lines.png',
            ]
        ]);

        $bundle = $bundle->map(function ($item, $key) {

            return [
                'color'=>$item
            ];
    });

        var_dump($bundle->all());
//        $img = $this->compilepet('0ff00f' , 'a5a5a5');
//
//        imagesavealpha($img,true);
//
//        imagepng($img,'bundle01.png');

        return Command::SUCCESS;
    }

    private function compilepet($skincolor, $eyecolor)
    {

        $img_lines = imagecreatefrompng("af01-lines.png"); // load lines no color change
        $img_eyewhite = imagecreatefrompng("af01-eyewhite.png"); // eye whites no color change

        $img_high1 = imagecreatefrompng("af01-highlights1.png");//highlights change to black
        $img_high2 = imagecreatefrompng("af01-highlights2.png"); // highlights shaddows to black
        $img_high3 = imagecreatefrompng("af01-highlights3.png"); // highlights change to white

        $img_eye = imagecreatefrompng("af01-eyecolor.png"); //eye color change to user slect
        $img_base = imagecreatefrompng("af01-base.png");//base color change to user slect

//
//build the imgage that will be the final output
//
        $tc = imagecreatetruecolor(imagesx($img_lines), imagesy($img_lines));
//fill $tc with transperent white
        imagealphablending($tc, false);
        imagefilledrectangle($tc, 0, 0, imagesx($img_lines), imagesy($img_lines), imagecolorallocatealpha($tc, 255, 255, 255, 127));
        imagealphablending($tc, true);

//
//change the colors to the needed ones
//
//
//compile the img in to $tc
//
        $this->imagecopymerge_alpha($tc, $this->alphacolorswap($img_base, $skincolor), 0, 0, 0, 0, imagesx($img_lines), imagesy($img_lines), 100);// base color leave a full opacity

        $this->imagecopymerge_alpha($tc, $this->alphacolorswap($img_high1, "020112"), 0, 0, 0, 0, imagesx($img_lines), imagesy($img_lines), 50);//left at 50 opacity
        $this->imagecopymerge_alpha($tc, $this->alphacolorswap($img_high2, "04000A"), 0, 0, 0, 0, imagesx($img_lines), imagesy($img_lines), 50);//left at 50
        $this->imagecopymerge_alpha($tc, $this->alphacolorswap($img_high3, "FFFFFF"), 0, 0, 0, 0, imagesx($img_lines), imagesy($img_lines), 25);// left at 25

        $this->imagecopymerge_alpha($tc, $img_eyewhite, 0, 0, 0, 0, imagesx($img_lines), imagesy($img_lines), 100);
        $this->imagecopymerge_alpha($tc, $this->alphacolorswap($img_eye, $eyecolor), 0, 0, 0, 0, imagesx($img_lines), imagesy($img_lines), 100);

        $this->imagecopymerge_alpha($tc, $img_lines, 0, 0, 0, 0, imagesx($img_lines), imagesy($img_lines), 100);

        return $tc;
    }


    private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
    {
        if (!isset($pct)) {
            return false;
        }
        $pct /= 100;
        // Get image width and height
        $w = imagesx($src_im);
        $h = imagesy($src_im);
        // Turn alpha blending off
        imagealphablending($src_im, false);
        // Find the most opaque pixel in the image (the one with the smallest alpha value)
        $minalpha = 127;
        for ($x = 0; $x < $w; $x++)
            for ($y = 0; $y < $h; $y++) {
                $alpha = (imagecolorat($src_im, $x, $y) >> 24) & 0xFF;
                if ($alpha < $minalpha) {
                    $minalpha = $alpha;
                }
            }
        //loop through image pixels and modify alpha for each
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                //get current alpha value (represents the TANSPARENCY!)
                $colorxy = imagecolorat($src_im, $x, $y);
                $alpha = ($colorxy >> 24) & 0xFF;
                //calculate new alpha
                if ($minalpha !== 127) {
                    $alpha = 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha);
                } else {
                    $alpha += 127 * $pct;
                }
                //get the color index with new alpha
                $alphacolorxy = imagecolorallocatealpha($src_im, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);
                //set pixel with the new color + opacity
                if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) {
                    return false;
                }
            }
        }
        // The image copy
        imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
    }

    private function alphacolorswap($image, $color)
    {

        $tc = imagecreatetruecolor(imagesx($image), imagesy($image));
//fill $tc with transperent white
        imagealphablending($tc, false);
        imagefilledrectangle($tc, 0, 0, imagesx($image), imagesy($image), imagecolorallocatealpha($tc, 255, 255, 255, 127));
        imagealphablending($tc, true);
        $newrgb = $this->html2rgb($color);

        for ($x = 0; $x < imagesx($image); $x++) {
            for ($y = 0; $y < imagesy($image); $y++) {
                $rgb = imagecolorat($image, $x, $y);
                $colors = imagecolorsforindex($image, $rgb);
                if ($colors["alpha"] !== 127) {
                    //highlight img
                    $newcolor1 = imagecolorallocatealpha($image, $newrgb[0], $newrgb[1], $newrgb[2], $colors["alpha"]);
                    imagesetpixel($tc, $x, $y, $newcolor1);
                }
            }
        }
        return $tc;
    }

    private function html2rgb($color)
    {
        if ($color[0] == '#')
            $color = substr($color, 1);

        if (strlen($color) == 6)
            list($r, $g, $b) = array($color[0] . $color[1],
                $color[2] . $color[3],
                $color[4] . $color[5]);
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        else
            return false;

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return array($r, $g, $b);
    }
}
