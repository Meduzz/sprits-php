<?php
abstract class Template {
	protected $_model = array();
	protected $_template = null;

	public function __invoke($key, $value='') {
		$this->_model[$key] = $value;
	}

	public function putAll($model) {
		foreach ($model as $key => $value) {
				$this->_model[$key] = $value;
		}
	}

	public function put($key, $value='') {
		$this->_model[$key] = $value;
	}

	/**
	 * Render will eventually return the template with all data.
	 */
	public abstract function render();
}

/**
 * On render this class will attempt to include_once on the file specified in constructor.
 * Into the context that the file will be imcluded, all of the model will be injected.
 *
 * This view allows you to reuse your old pop-views (plain old php, yeah I prolly just invented that expression).
 */
class PHPTemplate extends Template {

	public function __construct($phpFile) {
		$this->_template = $phpFile;
	}

	public function render() {
		foreach ($this->_model as $key => $value) {
			${$key} = $value;
		}

		ob_start();
		include_once($this->_template);
		return ob_get_clean();
	}
}

/**
 * On render this class will replace all placeholders(keys) in the html file with the values found in the model.
 * %key% will be replaced by "Hello world!" in model like 'key'=>'Hello world!'.
 *
 * For complicated keys a viewHelper can be added that handle the situation.
 */
class HTMLTemplate extends Template {

	public function __construct($htmlFile) {
		$this->_template = $htmlFile;
	}

	// TODO learn to handle objects. Use a helper as a workaround for now.
	public function render() {
		$template = file_get_contents($this->_template);

		foreach ($this->_model as $key => $value) {
			$template = str_replace('%'.$key.'%', VievHelper::helpRender($key, $value), $template);
		}

		return $template;
	}
}

class VievHelper {
	private static $_helpers = array();

	public static function helpRender($key, $data) {
		if (!isset(self::$_helpers[$key])) {
			return $data;
		}

		$helpers = self::$_helpers[$key];

		if (is_array($helpers)) {
			$body = $data;
			foreach ($helpers as $helper) {
				if ($helper instanceof Helper) {
					$body = $helper->render($key, $body);
				}
			}
			return $body;
		} else {
			return $helpers->render($key, $data);
		}
	}

	public static function registerHelper($key, $helper) {
		if (isset(self::$_helpers[$key])) {
			$helpers = self::$_helpers[$key];

			if (is_array($helpers)) {
				$helpers[] = $helper;
			} else {
				$helpers = array($helpers, $helper);
			}

			self::$_helpers[$key] = $helpers;
		} else {
			self::$_helpers[$key] = $helper;
		}
	}
}

interface Helper {
	/**
	 *
	 * @param type $key
	 * @param type $data
	 * @return type rendered $data
	 */
	public function render($key, $data);
}

// TODO add helper that takes a template and uses it to loop though an array.
?>
