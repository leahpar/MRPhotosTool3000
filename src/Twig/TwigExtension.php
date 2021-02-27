<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{

    function getFilters()
    {
        return [
            new TwigFilter('date',    [$this, 'formatDate']),
        ];
    }
    function formatDate($date = null, ?string $format = null): ?string
    {
        if ($date instanceof \DateTime) {
            $date = $date->format("Y-m-d");
        }
        $str = $date;
        try {
            if ($format) {
                //$str = (new \DateTime($this->value))->format($this->format);
                $timestamp = (new \DateTime($str))->getTimestamp();
                setlocale(LC_ALL, "fr_FR.UTF-8");
                return strftime($format, $timestamp);
            }
        }
        catch (\Exception $e) {
            // Do nothing
        }
        return $str;
    }
}