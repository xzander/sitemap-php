What is sitemap-php ?
----------

Fast and lightweight class for generating Google sitemap XML files and index of sitemap files. Written on PHP and uses XMLWriter extension (wrapper for libxml xmlWriter API) for creating XML files. XMLWriter extension is enabled by default in PHP 5 >= 5.1.2. If you having more than 50000 url, it splits items to seperated files. _(In benchmarks, 1.000.000 url was generating in 8 seconds)_

This is a slightly modified version of the original. The Sitemap class is now added to a 'SitemapPHP' namespace, and a composer document has been added.

How to use
----------

Include Sitemap.php file to your PHP document and call Sitemap class with your base domain.

	include 'src/SitemapPHP/Sitemap.php';
    
    use SitemapPHP\Sitemap;

	$sitemap = new Sitemap('http://example.com');	

Now, we need to define path for saving XML files. This can be relative like `xmls` or absolute `/path/to/your/folder` and *must be a writable folder*. In default it uses same folder with your script.

	$sitemap->setPath('xmls/');

Generated XML file names defaulted to `sitemap-*.xml`, you can customize prefix of filenames with `setFilename` method.

	$sitemap->setFilename('customsitemap');

	
We'll add sitemap url's with `addItem` method. In this method, only first parameter (location) is required.

    $sitemap->addItem(new Url('/', '1.0', 'daily', 'Today'));
    $sitemap->addItem(new Url('/movies', '0.5'));
    $sitemap->addItem(new Url('/files'));
    $sitemap->addItem(new Url('http://other.com/files'));//absolute url


If you need to change domain for sitemap instance, you can override it via `setDomain` method.

	$sitemap->setDomain('http://blog.example.com');
	
Finally we create index for sitemap files. This method also closes tags of latest generated xml file.

	$sitemap->createSitemapIndex();
	
When you run your script, it generates and saves XML files to given path.
	
sitemap-0.xml

	<?xml version="1.0" encoding="UTF-8"?>
	<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	 <url>
	  <loc>http://example.com/</loc>
	  <priority>1.0</priority>
	  <changefreq>daily</changefreq>
	  <lastmod>2011-04-07</lastmod>
	 </url>
	 <url>
	  <loc>http://example.com/about</loc>
	  <priority>0.8</priority>
	  <changefreq>monthly</changefreq>
	  <lastmod>2011-06-25</lastmod>
	 </url>
	 <url>
	  <loc>http://example.com/contact</loc>
	  <priority>0.6</priority>
	  <changefreq>yearly</changefreq>
	  <lastmod>2009-12-14</lastmod>
	 </url>
	 <url>
	  <loc>http://example.com/otherpage</loc>
	  <priority>0.5</priority>
	 </url>
	</urlset>
	
sitemap-index.xml

	<?xml version="1.0" encoding="UTF-8"?>
	<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	 <sitemap>
	  <loc>http://example.com/sitemap/sitemap-0.xml</loc>
	  <lastmod>2011-04-07</lastmod>
	 </sitemap>
	</sitemapindex>
	
You need to submit sitemap-index.xml to Google Sitemaps.

Look for more advanced examples at `exmple` folder.
