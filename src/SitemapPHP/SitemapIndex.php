<?php

namespace SitemapPHP;


use XMLWriter;

class SitemapIndex {

	const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    private $indexFile;

    private $sitemaps = [];

    function __construct($indexFile)
    {
        $this->indexFile = $indexFile;
    }

    public function readEntries()
    {
        if (!file_exists($this->indexFile)) {
            return false;
        }
        /** @var \SimpleXMLElement $xml */
        $xml = simplexml_load_file($this->indexFile);
        foreach ($xml as $sitemap) {
            $this->sitemaps[(string)$sitemap->loc] = [
                'lastmod' => (string)$sitemap->lastmod
            ];
        }
        return true;
    }

    /**
     * @param array $sitemaps
     */
    public function setSitemaps($sitemaps)
    {
        $this->sitemaps = $sitemaps;
    }

    /**
     * Get sitemaps in current index
     * @return array
     */
    public function getSitemaps()
    {
        return $this->sitemaps;
    }

    public function addSitemap($sitemap, $lastMod = 'Today')
    {
        $this->sitemaps[$sitemap] = [
            'lastmod' => $lastMod
        ];
    }


	/**
	 * Prepares sitemap XML document
	 */
	public function createIndex() {
        $writer = new \XMLWriter();
		$writer->openURI($this->indexFile);
		$writer->startDocument('1.0', 'UTF-8');
		$writer->setIndent(true);
		$writer->startElement('sitemapindex');
		$writer->writeAttribute('xmlns', self::SCHEMA);

        foreach ($this->sitemaps as $loc => $sitemap) {
            $writer->startElement('sitemap');
            $writer->writeElement('loc', $loc);
            $writer->writeElement('lastmod', Util::getLastModifiedDate($sitemap['lastmod']));
            $writer->endElement();
        }

        $writer->endElement();
        $writer->endDocument();
    }
} 