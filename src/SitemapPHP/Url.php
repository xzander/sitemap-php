<?php
/**
 * Created by PhpStorm.
 * User: Aidas
 * Date: 14.7.5
 * Time: 10.48
 */

namespace SitemapPHP;


class Url {
    private $loc;
    private $priority;
    private $changefreq;
    private $lastmod;

    function __construct($loc = null, $priority = null, $changefreq = null, $lastmod = null)
    {
        $this->setLoc($loc);
        $this->setPriority($priority);
        $this->setChangefreq($changefreq);
        $this->setLastmod($lastmod);
    }

    /**
     * @return mixed
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * @return mixed
     */
    public function getLastmod()
    {
        return $this->lastmod;
    }

    /**
     * @return mixed
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * How frequently the page is likely to change. Valid values are always, hourly, daily, weekly, monthly, yearly and never.
     * @param mixed $changefreq
     */
    public function setChangefreq($changefreq)
    {
        $this->changefreq = $changefreq;
    }

    /**
     * The date of last modification of url. Unix timestamp or any English textual datetime description.
     * @param mixed $lastmod
     */
    public function setLastmod($lastmod)
    {
        $this->lastmod = $lastmod;
    }

    /**
     * URL of the page. This value must be less than 2,048 characters.
     * @param mixed $loc
     */
    public function setLoc($loc)
    {
        $this->loc = $loc;
    }

    /**
     * The priority of this URL relative to other URLs on your site. Valid values range from 0.0 to 1.0.
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
}