<?php
include_once('../framework/sprits.php');

Router::GET('/news', function(){
	echo 'Have you heard about the new sprits-php webframework? Apparently it can mount pages like this one.';
});

?>
