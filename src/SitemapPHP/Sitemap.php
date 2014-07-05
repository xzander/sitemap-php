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
class Sitemap {

	/**
	 *
	 * @var \XMLWriter
	 */
	private $writer;
	private $domain;
	private $path;
	private $filename = 'sitemap';
	private $location = '/';
	private $currentItem = 0;
	private $currentSitemap = 0;
	private $sitemaps = [];

    private $itemsPerSitemap = 50000;

	const EXT = '.xml';
	const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';
	const DEFAULT_PRIORITY = 0.5;
	const SEPERATOR = '-';
	const INDEX_SUFFIX = 'index';

	/**
	 *
	 * @param string $domain
	 */
	public function __construct($domain) {
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
        $this->itemsPerSitemap = $itemsPerSitemap;
    }

    /**
     * Sets root path of the website, starting with http:// or https://,
     * but without a trailing slash
     *
     * @param string $domain
     */
	public function setDomain($domain) {
		$this->domain = $domain;
	}

	/**
	 * Returns root path of the website
	 *
	 * @return string
	 */
	private function getDomain() {
		return $this->domain;
	}

	/**
	 * Returns XMLWriter object instance
	 *
	 * @return \XMLWriter
	 */
	private function getWriter() {
		return $this->writer;
	}

	/**
	 * Assigns XMLWriter object instance
	 *
	 * @param \XMLWriter $writer 
	 */
	private function setWriter(\XMLWriter $writer) {
		$this->writer = $writer;
	}

	/**
	 * Returns path of sitemaps
	 * 
	 * @return string
	 */
	private function getPath() {
		return $this->path;
	}

	/**
	 * Sets paths of sitemaps
	 * 
	 * @param string $path
	 * @return Sitemap
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * Returns filename of sitemap file
	 * 
	 * @return string
	 */
	private function getFilename() {
		return $this->filename;
	}

	/**
	 * Sets filename of sitemap file
	 * 
	 * @param string $filename
	 * @return Sitemap
	 */
	public function setFilename($filename) {
		$this->filename = $filename;
	}

	/**
	 * Returns current item count
	 *
	 * @return int
	 */
	private function getCurrentItem() {
		return $this->currentItem;
	}

	/**
	 * Increases item counter
	 * 
	 */
	private function incCurrentItem() {
		$this->currentItem = $this->currentItem + 1;
	}

	/**
	 * Returns current sitemap file count
	 *
	 * @return int
	 */
	private function getCurrentSitemap() {
		return $this->currentSitemap;
	}

	/**
	 * Increases sitemap file count
	 * 
	 */
	private function incCurrentSitemap() {
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
        $sitemaps = [];
        foreach ($this->sitemaps as $sitemap) {
            $sitemaps[] = $this->domain .$this->location . $sitemap;
        }
        return $sitemaps;
    }

	/**
	 * Prepares sitemap XML document
	 */
	private function startSitemap() {
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
	}

    /**
     * Adds an item to sitemap
     *
     * @param Url $url
     * @return Sitemap
     */
	public function addItem(Url $url) {
		if (($this->getCurrentItem() % $this->itemsPerSitemap) == 0) {
			if ($this->getWriter() instanceof \XMLWriter) {
				$this->endSitemap();
			}
			$this->startSitemap();
			$this->incCurrentSitemap();
		}
		$this->incCurrentItem();
		$this->getWriter()->startElement('url');
        $this->getWriter()->writeElement('loc',  $this->createAbsoluteUrl($url->getLoc()));
        if ($url->getPriority()) {
		    $this->getWriter()->writeElement('priority', $url->getPriority());
        }
        if ($url->getChangefreq()) {
		    $this->getWriter()->writeElement('changefreq', $url->getChangefreq());
        }
        if ($url->getLastmod()) {
		    $this->getWriter()->writeElement('lastmod',
                Util::getLastModifiedDate($url->getLastmod())
            );
        }
		$this->getWriter()->endElement();
		return $this;
	}

	/**
	 * Finalizes tags of sitemap XML document.
	 *
	 */
	public function endSitemap() {
		if ($this->writer) {
			$this->writer->endElement();
			$this->writer->endDocument();
            $this->writer = null;
		}
	}

    /**
	 * Writes Google sitemap index for generated sitemap files
	 *
	 * @param string $loc Accessible URL path of sitemaps
	 * @param string|int $lastmod The date of last modification of sitemap. Unix timestamp or any English textual datetime description.
	 */
	public function createSitemapIndex($lastmod = 'Today', $path = null) {
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
