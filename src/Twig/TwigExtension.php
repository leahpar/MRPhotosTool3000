<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('date', $this->formatDate(...)),
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
}
