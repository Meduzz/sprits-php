<?php

include_once('../framework/sprits.php');

Router::GET('/hello/:world', function($world){
	echo 'Hello '.$world.'!';
});

$sprits = new Sprits();

// mount the extermely heavy news logic on requests starting with /news
$sprits->mount('/news', 'news.php');

// override the boring default 404.
$sprits->http404 = function() {
	header('Not Found', true, 404);
	echo '<h1>Not home.</h1>';
	echo '<p>Hello, this is '.$_SERVER['REQUEST_URI'].', I am not in at the moment. <br/>Please leave a message.</p>';
};

$sprits();

?>
