<?php

require '../src/SitemapPHP/Sitemap.php';
require '../src/SitemapPHP/SitemapIndex.php';
require '../src/SitemapPHP/Url.php';
require '../src/SitemapPHP/Util.php';

use SitemapPHP\Sitemap;
use SitemapPHP\Url;

$sitemap = new Sitemap('http://example.com');
$sitemap->setPath('./');//current folder
$sitemap->addItem(new Url('/', '1.0', 'daily', 'Today'));
$sitemap->addItem(new Url('/movies', '0.5'));
$sitemap->addItem(new Url('/files'));
$sitemap->addItem(new Url('http://other.com/files'));//absolute url
$sitemap->endSitemap();