<?php


namespace App\Twig;


use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('date', $this->formatDate(...)),
            new TwigFilter('qrcode',  [$this, 'qrcode'], ['is_safe' => ['html']]),
        ];
    }

    public function formatDate(\DateTime|string|null $date = null, ?string $format = null): ?string
    {
        if ($date instanceof \DateTime) {
            $date = $date->format("Y-m-d");
        }
        $str = $date;
        try {
            if ($format) {
                if (str_contains($format, '%')) {
                    // advanced format
                    $timestamp = (new \DateTime($str))->getTimestamp();
                    setlocale(LC_ALL, "fr_FR.UTF-8");
                    //return strftime($format, $timestamp);
                    // same code but with intl
                    $formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL);
                    // Convert the format to ICU format
                    $format = str_replace(['%d', '%m', '%B', '%Y'], ['dd', 'MM', 'MMMM', 'yyyy'], $format);
                    $formatter->setPattern($format);
                    return $formatter->format($timestamp);

                }
                else {
                    // Classic format
                    return (new \DateTime($str))->format($format);
                }
            }
        }
        catch (\Exception) {
            // Do nothing
        }
        return $str;
    }

    public function qrCode(?string $value = null): ?string
    {
        $options = [
            'version' => 6, // https://www.qrcode.com/en/about/version.html
            //'versionMin' => 5,
            //'versionMax' => 10,
            'eccLevel' => QRCode::ECC_L,
            //'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'imageTransparent' => true,
        ];

        $qrcode = new QRCode(new QROptions($options));
        return $qrcode->render($value);
    }

}
