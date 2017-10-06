<?php

namespace Framework\Twig;

use DateTime;
use Twig_SimpleFilter;

/**
 * Text extensions for twig
 *
 * Class TextExtension
 * @package Framework\Twig
 */
class TimeExtension extends \Twig_Extension
{
    /**
     * @return Twig_SimpleFilter
     */
    public function getFilters(): array
    {
        return [
            new Twig_SimpleFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Excerpt of a long text
     *
     * @param DateTime $date
     * @param string $format
     * @return string
     * @internal param string $content
     * @internal param int $maxLength
     */
    public function ago(DateTime $date, string $format = 'd/m/Y H:i')
    {
        return '<span class="timeago" datetime="' . $date->format(Datetime::ISO8601) . '">' .
            $date->format($format) . '</span>';
    }
}
