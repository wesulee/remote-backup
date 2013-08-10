<?php

// redirect to index if not logged in
// session_start() needs to be called beforehand
function validateLoggedIn($url='index.php', $perm=false)
{
	if ($_SESSION['auth'] !== 1) {
		redirect($url, $perm);
	}
}

function redirect($url, $perm=false)
{
	header('Location: '.$url, $perm ? 301 : 302);
	exit();
}

function joinPath($path1, $path2)
{
	if (empty($path1))
		return trim($path2, '/');
	if (empty($path2))
		return rtrim($path1, '/');
	$path1 = rtrim($path1, '/');
	$path2 = trim($path2, '/');
	return $path1.'/'.$path2;
}

function joinPaths()
{
	$path = array_reduce(array_slice(func_get_args(), 1), 'joinPath');
	return joinPath(func_get_arg(0), $path);
}


// replaces \ with / (for paths)
function slashes($string)
{
	return str_replace('\\', '/', $string);
}

// basic URL params builder
function urlGET($url, $params)
{
	return $url.'?'.http_build_query($params);
	$url .= '?';
	foreach ($params as $param => $value) {
		$url.= urlencode($param).'='.urlencode($value).'&';
	}
	$url = mb_substr($url, 0, -1);
	return htmlentities($url);
}

// for browse.php
function genLinkPaths($baseURL, $start, $end)
{
	$start = rtrim($start, '/');
	$end = rtrim($end, '/');
	$urls = array(array('folder' => 'Home', 'url' => $baseURL));
	if ($start === $end)
		return $urls;
	$length = mb_strlen($start);
	$paths = explode('/', mb_substr($end, $length+1));
	$current = '';
	foreach ($paths as $path) {
		$current = joinPath($current, $path);
		$urls[] = array(
			'folder' => $path,
			'url'    => urlGET($baseURL, array('path' => $current))
			);
	}
	return $urls;
}
