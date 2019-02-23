<?php

if ( !function_exists('baseUrl') ) {
	
	function baseUrl($url) {
		return sprintf('http://kienpham.com/youtube/index.php/%s', $url);
	}
}

if ( isset($_SERVER['PATH_INFO']) ) {

	$segment = explode('/', trim($_SERVER['PATH_INFO'], '/') );
	$controller = sprintf('%sController', $segment[0]);
	$action = $segment[1] ?? 'index';
	$param = isset($segment[2]) ? $segment : (!empty($_POST) ? $_POST : null);

	require 'controllers/'.$controller.'.php';
	$c = new $controller();

	$c->{$action}($param);
	
}
else require 'views/master.php';