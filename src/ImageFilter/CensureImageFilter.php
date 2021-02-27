<?php


namespace App\ImageFilter;


use Imagine\Filter\Basic\Fill;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class CensureImageFilter implements LoaderInterface
{

    /**
     * @var ImagineInterface
     */
    private ImagineInterface $imagine;

    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    /**
     * Si besoin, voir exemple
     * https://github.com/neok/LiipImagineAdditionalFiltersBundle
     *
     * @param ImageInterface $image
     * @param array          $options
     *
     * @return ImageInterface
     */
    public function load(ImageInterface $image, array $options = []): ImageInterface
    {
        if ($options["enabled"]) {

            foreach ($options['positions'] as $position) {
                $pos = explode(',', $position);
                //dump($pos);
                $w = 50;
                $h = 50;
                $x = $pos[0] - $w/2;
                $y = $pos[1] - $h/2;

                $point = new Point($x, $y);
                $box = new Box($w, $h);

                // Application plusieurs fois du filtre car il est très faible
                // https://www.php.net/manual/fr/function.imagefilter.php#114750
                for ($i = 0; $i < 50; $i++) {
                    $patch = $image->copy()->crop($point, $box);
                    //$patch = $this->imagine->create($box, new RGB(new RGBPalette(), [255, 0, 0], 100));
                    $patch->effects()->blur(1);
                    //$patch->effects()->negative();
                    $image->paste($patch, $point);
                }
            }
        }

        return $image;
    }


}

