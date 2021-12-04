<?php


namespace App\Service;


use App\Entity\Photo;
use App\Entity\Shooting;
use Liip\ImagineBundle\Service\FilterService;

class PhotoFilterService
{
    private string $publicDirectory;
    private string $shootingsDirectory;
    private FilterService $imagine;

    /**
     * @param FilterService $imagine
     * @param string $public_directory
     * @param string $shootings_directory
     */
    public function __construct(FilterService $imagine, string $public_directory, string $shootings_directory)
    {
        $this->publicDirectory = $public_directory;
        $this->shootingsDirectory = $shootings_directory;
        $this->imagine = $imagine;
    }

    public function getFilteredPhoto(Photo $photo, string $filter): string
    {
        /** @var Shooting $shooting */
        $shooting = $photo->getShooting();
        
        /** @var string */
        $path = $shooting->getSlug().'/'.$photo->getFile();
        
        if ($filter == "instagram") {
            list($width, $height, $type, $attr) = getimagesize($this->shootingsDirectory . '/' . $path);
            $ratioImg = $height / $width;
            $ratioInsta = 5 / 4;

            $runtimeConfig = [
                'background' => [
                    'size' => [
                        1080,
                        (int)min(1350, 1080 * $ratioImg),
                    ]
                ],
            ];

            if (count($photo->getCensure()??[]) > 0) {
                $runtimeConfig += [
                    'censure_filter' => [
                        'enabled' => true,
                        'positions' => $photo->getCensure()
                    ],
                ];
            }

            dump($runtimeConfig);

            $resourcePath = $this->imagine->getUrlOfFilteredImageWithRuntimeFilters(
                $path,
                'instagram',
                $runtimeConfig
            );
            $filename = parse_url($resourcePath, PHP_URL_PATH);
            $file = $this->publicDirectory . $filename;
        }
        elseif ($filter == "full") {
            $resourcePath = $this->shootingsDirectory . '/' . $path;
            $file = $resourcePath;
        }
        else {
            $resourcePath = $this->imagine->getUrlOfFilteredImage($path, $filter);
            $filename = parse_url($resourcePath, PHP_URL_PATH);
            $file = $this->publicDirectory . $filename;
        }
        
        return $file;

    }

}
