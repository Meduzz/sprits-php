<?php
include_once('../framework/sprits.php');
include_once('../framework/templating.php');

Router::GET('/hello/:world', function($world){
	$template = new PHPTemplate('templates/hello.php');
	$template->put('world', $world);
	echo $template->render();
});

Router::GET('/layout', function(){
	$page = new Layout(new HTMLTemplate('templates/layout.html'));
	$page->addPartial('body', new HTMLTemplate('templates/partial.layout.html'));
	echo $page->render();
});

Router::GET('/|/index.php', function() {
	?>
	<h1>Don't be evil!</h1>
	<p>By that I mean, this route should not steal all attention.</p>
	<?php
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
