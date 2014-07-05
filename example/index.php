<?php

require '../src/SitemapPHP/Sitemap.php';
require '../src/SitemapPHP/SitemapIndex.php';
require '../src/SitemapPHP/Url.php';
require '../src/SitemapPHP/Util.php';

use SitemapPHP\Sitemap;
use SitemapPHP\SitemapIndex;
use SitemapPHP\Url;

$sitemap = new Sitemap('http://example.com');
$sitemap->setPath('./');//current folder
$sitemap->setLocation('/movies/');//path after domain
$sitemap->setFilename('movies');//sitemap name
$sitemap->setItemsPerSitemap(2);
$sitemap->addItem(new Url('/movies/flinstones', '0.5'));
$sitemap->addItem(new Url('/movies/batman', '0.5'));
$sitemap->addItem(new Url('/movies/superman', '0.5'));
$sitemap->addItem(new Url('/movies/spiderman'));
$sitemap->addItem(new Url('/movies/maksvel'));

$sitemap->createSitemapIndex();

$index = new SitemapIndex('./sitemap-index.xml');// full path to sitemap index
$index->readEntries();//read current entries in sitemap
foreach ($sitemap->getSitemapsWithAbsolutePath() as $sitemap) {
    $index->addSitemap($sitemap);
}
$index->createIndex();
