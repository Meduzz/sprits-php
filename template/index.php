<?php

include_once('../framework/sprits.php');

Router::GET('/hello/:world', function($world){
	echo 'Hello '.$world.'!';
});

$sprits = new Sprits();

// mount the extermely heavy news logic on requests starting with /news
$sprits->mount('/news', 'news.php');

$sprits();

?>
