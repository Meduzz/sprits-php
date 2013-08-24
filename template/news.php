<?php
include_once('../framework/sprits.php');
include_once('../framework/templating.php');

// Define a view helper to deal with the array of data.
class ListNewsHelper implements Helper {
	public function render($key, $data) {
		$body = '';

		// only trigger when key == body.
		if ($key == 'body') {
			foreach ($data as $row) {
				$body .= '<p>'.$row.'</p>';
			}
		}
		
		return $body;
	}
}

// Register our helper with the tag it should handle.
VievHelper::registerHelper('body', new ListNewsHelper());

Router::GET('/news', function(){
	$newsModel = array('body' => array('Great news.', 'Good news.', 'Some news.', 'Bad news.', 'No news.'));

	if (isset($_GET['embed'])) {
		// skip layout
		// not recomended, but possible. Imagine a XHR scenario here.
		echo VievHelper::helpRender('body', $newsModel['body']);
	} else {
		// render with layout.
		$layout = new HTMLTemplate('templates/layout.html');

		// what the helper returns when it's done will be put at its key and returned by the template.
		$layout->putAll($newsModel);
		$layout->put('title', 'News');

		echo $layout->render();
	}
});
?>
