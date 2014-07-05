<?php
namespace SitemapPHP;


class Util
{


    /**
     * Prepares given date for sitemap
     *
     * @param string $date Unix timestamp or any English textual datetime description
     * @return string Year-Month-Day formatted date.
     */
    static function getLastModifiedDate($date)
    {
        if (ctype_digit($date)) {
            return date(DATE_W3C, $date);
        } else {
            $date = strtotime($date);
            return date(DATE_W3C, $date);
        }
    }
} 