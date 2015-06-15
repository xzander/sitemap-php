<?php

namespace SitemapPHP;

/**
 * Sitemap
 *
 * This class used for generating Google Sitemap files
 *
 * @package    Sitemap
 * @author     Osman Üngür <osmanungur@gmail.com>
 * @copyright  2009-2011 Osman Üngür
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Sitemap
{

    /**
     *
     * @var \XMLWriter
     */
    private $writer;
    /**
     *
     * @var \XMLWriter
     */
    private $memWriter;
    private $domain;
    private $path;
    private $filename = 'sitemap';
    private $location = '/';
    private $currentItem = 0;
    private $currentSitemap = 0;
    private $sitemaps = array();

    private $itemsPerSitemap = 50000;
    private $bytesPerSitemap = 10000000;

    const EXT = '.xml';
    const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    const DEFAULT_PRIORITY = 0.5;
    const SEPERATOR = '-';
    const INDEX_SUFFIX = 'index';

    /**
     *
     * @param string $domain
     */
    public function __construct($domain)
    {
        $this->setDomain($domain);
    }

    /**
     * Sitemap location, path after domain, default /
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param int $itemsPerSitemap
     */
    public function setItemsPerSitemap($itemsPerSitemap)
    {
        if ($itemsPerSitemap < 1) {
            throw new \LogicException('You must set items per sitemap more than 1');
        }
        $this->itemsPerSitemap = $itemsPerSitemap;
    }

    /**
     * @param int $bytesPerSitemap
     */
    public function setBytesPerSitemap($bytesPerSitemap)
    {
        if ($bytesPerSitemap < 500) {
            throw new \LogicException('You must set bytes per sitemap more than 500');
        }
        $this->bytesPerSitemap = $bytesPerSitemap;
    }

    /**
     * Sets root path of the website, starting with http:// or https://,
     * but without a trailing slash
     *
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Returns root path of the website
     *
     * @return string
     */
    private function getDomain()
    {
        return $this->domain;
    }

    /**
     * Returns XMLWriter object instance
     *
     * @return \XMLWriter
     */
    private function getWriter()
    {
        return $this->writer;
    }

    /**
     * Returns XMLWriter object instance
     *
     * @return \XMLWriter
     */
    private function getMemWriter()
    {
        return $this->memWriter;
    }

    /**
     * Assigns XMLWriter object instance
     *
     * @param \XMLWriter $writer
     */
    private function setWriter(\XMLWriter $writer)
    {
        $this->writer = $writer;
    }

    /**
     * Assigns XMLWriter object instance
     *
     * @param \XMLWriter $writer
     */
    private function setMemWriter(\XMLWriter $writer)
    {
        $this->memWriter = $writer;
    }

    /**
     * Returns path of sitemaps
     *
     * @return string
     */
    private function getPath()
    {
        return $this->path;
    }

    /**
     * Sets paths of sitemaps
     *
     * @param string $path
     * @return Sitemap
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Returns filename of sitemap file
     *
     * @return string
     */
    private function getFilename()
    {
        return $this->filename;
    }

    /**
     * Sets filename of sitemap file
     *
     * @param string $filename
     * @return Sitemap
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Returns current item count
     *
     * @return int
     */
    private function getCurrentItem()
    {
        return $this->currentItem;
    }

    /**
     * Increases item counter
     *
     */
    private function incCurrentItem()
    {
        $this->currentItem = $this->currentItem + 1;
    }

    /**
     * Returns current sitemap file count
     *
     * @return int
     */
    private function getCurrentSitemap()
    {
        return $this->currentSitemap;
    }

    /**
     * Increases sitemap file count
     *
     */
    private function incCurrentSitemap()
    {
        $this->currentSitemap = $this->currentSitemap + 1;
    }

    /**
     * Get list of created sitemaps
     * @return array
     */
    public function getSitemaps()
    {
        return $this->sitemaps;
    }


    /**
     * Get list of created sitemaps
     * @return array
     */
    public function getSitemapsWithAbsolutePath()
    {
        $sitemaps = array();
        foreach ($this->sitemaps as $sitemap) {
            $sitemaps[] = $this->domain . $this->location . $sitemap;
        }
        return $sitemaps;
    }

    /**
     * Prepares sitemap XML document
     */
    private function startSitemap()
    {
        $this->setWriter(new \XMLWriter());
        if ($this->getCurrentSitemap()) {
            $sitemap = $this->getFilename() . self::SEPERATOR . $this->getCurrentSitemap() . self::EXT;
        } else {
            $sitemap = $this->getFilename() . self::EXT;
        }
        $this->sitemaps[] = $sitemap;
        $this->getWriter()->openURI($this->getPath() . $sitemap);
        $this->getWriter()->startDocument('1.0', 'UTF-8');
        $this->getWriter()->setIndent(true);
        $this->getWriter()->startElement('urlset');
        $this->getWriter()->writeAttribute('xmlns', self::SCHEMA);

        $this->setMemWriter(new \XMLWriter());
        $this->getMemWriter()->openMemory();
        $this->getMemWriter()->setIndent(true);

        $this->currentItem = 0;

    }

    private function isNeedToStartNewSitemap()
    {
        $need = ($this->getCurrentItem() % $this->itemsPerSitemap) == 0;
        if (
            !$need &&
            $this->getCurrentItem() % floor($this->itemsPerSitemap / 20) == 0 &&
            $this->getMemWriter() instanceof \XMLWriter &&
            strlen($this->getMemWriter()->outputMemory(false)) > ($this->bytesPerSitemap * 0.9 - 1000)
        ) {
            $need = true;
        }
        return $need;
    }

    /**
     * Adds an item to sitemap
     *
     * @param Url $url
     * @return Sitemap
     */
    public function addItem(Url $url)
    {
        if ($this->isNeedToStartNewSitemap()) {
            if ($this->getWriter() instanceof \XMLWriter) {
                $this->endSitemap();
            }
            $this->startSitemap();
            $this->incCurrentSitemap();
        }
        $this->incCurrentItem();
        $this->getMemWriter()->startElement('url');
        $this->getMemWriter()->writeElement('loc', $this->createAbsoluteUrl($url->getLoc()));
        if ($url->getPriority()) {
            $this->getMemWriter()->writeElement('priority', $url->getPriority());
        }
        if ($url->getChangefreq()) {
            $this->getMemWriter()->writeElement('changefreq', $url->getChangefreq());
        }
        if ($url->getLastmod()) {
            $this->getMemWriter()->writeElement('lastmod',
                Util::getLastModifiedDate($url->getLastmod())
            );
        }
        $this->getMemWriter()->endElement();
        return $this;
    }

    /**
     * @param array $data <code>
     *      array(
     *          array('loc' => '', 'priority' => '', 'changefreq' => '', 'lastmod' => ''),
     *          ...
     *      )
     * </code>
     */
    public function addItemsFromArray($data)
    {
        foreach ($data as $row) {
            if (empty($row['loc'])) {
                continue;
            }

            $url = new Url(
                $row['loc'],
                !empty($row['priority']) ? $row['priority'] : null,
                !empty($row['changefreq']) ? $row['changefreq'] : null,
                !empty($row['lastmod']) ? $row['lastmod'] : null
            );

            $this->addItem($url);
        }
    }

    /**
     * Finalizes tags of sitemap XML document.
     *
     */
    public function endSitemap()
    {
        if ($this->getWriter()) {
            $this->getMemWriter()->endElement();
            $batchXmlString = $this->getMemWriter()->outputMemory(true);
            $this->getWriter()->writeRaw($batchXmlString);
            $this->getMemWriter()->flush();
            unset($this->memWriter);
            $this->getWriter()->endElement();
            $this->getWriter()->endDocument();
            unset($this->writer);
        }
    }

    /**
     * Writes Google sitemap index for generated sitemap files
     *
     * @param string|int $lastmod The date of last modification of sitemap. Unix timestamp or any English textual datetime description.
     * @param null $path
     */
    public function createSitemapIndex($lastmod = 'Today', $path = null)
    {
        $this->endSitemap();
        if (!$path) {
            $path = $this->getPath() . $this->getFilename() . self::SEPERATOR . self::INDEX_SUFFIX . self::EXT;
        }
        $index = new SitemapIndex($path);
        foreach ($this->getSitemapsWithAbsolutePath() as $sitemap) {
            $index->addSitemap($sitemap, $lastmod);
        }
        $index->createIndex();
    }

    /**
     * @param $location
     * @return string
     */
    private function createAbsoluteUrl($location)
    {
        if ($location && $location[0] == '/') {
            $location = $this->getDomain() . $location;
            return $location;
        }
        return $location;
    }
}
