<?php

	// Version: 1.1

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

	foreach ($urls as $url)
	{
		checkurl($url, 0, "/");
	}

	function checkurl($url, $level, $dir)
	{
		global $xml, $text, $levels, $home, $homeurl;
		if ($xml == true)
		{
			$text .= "<url><loc> " . $homeurl . $dir . $url . " </loc></url>\n";
		}
		elseif ($xml == false)
		{
			$text .= str_repeat($levels, $level) . "<a href='" . $homeurl . $dir . $url . "'> " . $url . "</a><br>";
		}

		if (another_is_dir($dir . $url))
		{
			$innerurls[$url] = array_diff(scandir($home . $dir . $url, 1), array('..', '.'));
			foreach ($innerurls[$url] as $urld)
			{
				checkurl($urld, $level + 1, $dir . $url . "/");
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