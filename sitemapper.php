<?php

	// Version: 1.2

	$home = $_SERVER['DOCUMENT_ROOT'];
	$homeurl = "//" . $_SERVER['HTTP_HOST'];
	$innerurls = array();
	$text = "";

	if (isset($_GET['dir']))
	{
		$home .= $_GET['dir'];
		$homeurl .= $_GET['dir'];
	}

	$urls = array_diff(scandir($home . "/", 1), array('..', '.'));

	if (isset($_GET['xml']))
	{
		$xml = true;
	}
	else
	{
		$xml = false;
		$levels = "<span style='margin-left: 40px;'></span>";
	}

	if ($xml == true)
	{
		$text .= "<?xml version='1.0' encoding='UTF-8'?>\n<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";
	}
	else
	{
		$text .= "<html> <head>	<link rel='stylesheet' type='text/css' href='sitemapperresources/style.css'> </head> <body> ";
	}

	foreach ($urls as $url)
	{
		checkurl($url, 0, "/");
	}

	function checkurl($url, $level, $dir)
	{
		global $xml, $text, $levels, $home, $homeurl;
		$isdir = another_is_dir($dir . $url);

		if ($isdir && !$xml)
		{
			$text .= "<div class='dirholder'>";
		}

		if ($xml)
		{
			$text .= "<url><loc> " . $homeurl . $dir . $url . " </loc></url>\n";
		}
		elseif (!$xml && !$isdir)
		{
			$text .= str_repeat($levels, $level) . "<a href='" . $homeurl . $dir . $url . "' class='url'> " . $url . "</a><br>";
		}
		elseif (!$xml && $isdir)
		{
			$text .= str_repeat($levels, $level) . "<input type='checkbox' id='" . $dir . $url . "' class='extender'> <label class='dir' for='" . $dir . $url . "'> " . $url . "</label><br>";
		}

		if ($isdir)
		{
			$innerurls[$url] = array_diff(scandir($home . $dir . $url, 1), array('..', '.'));
			foreach ($innerurls[$url] as $urld)
			{
				checkurl($urld, $level + 1, $dir . $url . "/");
			}
			if (!$xml)
			{
				$text .= "</div>";
			}
		}
	}

	if ($xml == true)
	{
		$text .= "</urlset>";
		echo "<textarea rows='100' cols='100'>";
		echo $text;
		echo "</textarea>";
		if (isset($_GET['update']))
		{
			updatexml();
		}
	}
	else
	{
		
		$text .= " </body> </html>";
		echo $text;
	}

	function updatexml()
	{
		global $text;
		$sitemap = fopen("sitemap.xml", "w") or die ("Cannot find sitemap.xml");
		fwrite($sitemap, $text);
		fclose($sitemap);
	}

	// Modified from PHP Manual - is_dir, User comment: sly at noiretblanc dot org
	function another_is_dir ($file)
	{ 
		global $home;
		return ((fileperms($home . $file) & 0x4000) == 0x4000);
	}

?>