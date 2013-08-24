<?php
include_once('../framework/sprits.php');
include_once('../framework/templating.php');

Router::GET('/hello/:world', function($world){
	$template = new PHPTemplate('templates/hello.php');
	$template->put('world', $world);
	echo $template->render();
});

$sprits = new Sprits();

// mount the extermely heavy news logic on requests starting with /news
$sprits->mount('/news', 'news.php');

// override the boring default 404.
$sprits->http404 = function() {
	header('Not Found', true, 404);

	// load a layout template and our content template.
	$layout = new HTMLTemplate('templates/layout.html');
	$template = new HTMLTemplate('templates/partial.404.html');
	$template->put("path", $_SERVER['REQUEST_URI']);

	$layout->put('title', '404 - Not Found');
	// merge our templates onto the layout.
	$layout->put('body', $template->render());

	// echo the result.
	echo $layout->render();
};

$sprits();
?>
