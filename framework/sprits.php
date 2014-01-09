<?php
// TODO add method to visit other parts of the site, like partial renders (list of news etc)
// TODO add redirect method, it looks like every serious framework must have one.
class Sprits {
	private $_mountPoints;
	public $http404;
	public $http500;

	public function __construct() {
		$this->_mountPoints = array();
		$this->http404 = function() {
			header('Not Found', true, 404);
			echo '<h1>404 Not Found</h1>';
		};
		// TODO add params to the 500 callback, so the exception can be sent to the page.
		$this->http500 = function(){
			header('Internal Server Error.', true, 500);
			echo '<h1>500 Internal Server Error.</h1>';
		};
	}

	/**
	 * Instead of including all files in your php project, you tell the framework that pathes matching x should include a file.
	 * @param type $path the path like /news
	 * @param type $file the file, this needs to be available for include_once.
	 */
	public function mount($path, $file) {
		$this->_mountPoints[$path] = $file;
	}

	/**
	 * Calling this method will start the magic.
	 * @param type $verb an optional verb (like GET for get requests)
	 * @param type $path an optional path (like /spam)
	 */
	public function go($verb = null, $path = null) {
		$verb = $verb == null ? $_SERVER['REQUEST_METHOD'] : $verb;
		$path = $path == null ? $_SERVER['REQUEST_URI'] : $path;

		$keys = array_keys($this->_mountPoints);

		foreach ($keys as $key) {
			if (strpos($path, $key) === 0) {
				// mount this mountPoint.
				include_once($this->_mountPoints[$key]);
			}
		}

		$holder = Router::findRouteFor($verb, $path);

		if ($holder === false) {
			call_user_func($this->http404);
		} else {
			try {
				call_user_func_array($holder->callback, $holder->params);
			} catch (Exception $e) {
				call_user_func($this->http500);
			}
		}
	}

	/**
	 * Convinience method for invoking the go method. I.e do ze magic.
	 * @param type $verb an optional verb, in case you'r hacking on your own or testing.
	 * @param type $path an optional path, in case you'r hacking on your own or testing.
	 */
	public function __invoke($verb = null, $path = null) {
		$verb = $verb == null ? $_SERVER['REQUEST_METHOD'] : $verb;
		$path = $path == null ? $_SERVER['REQUEST_URI'] : $path;
		
		$this->go($verb, $path);
	}

	/**
	 * Error handler that throws a proper exception.
	 * @param type $errorno
	 * @param type $errorstr
	 * @param type $errorfile
	 * @param type $errorline
	 * @throws ErrorException <- it's true, always.
	 */
	public function error_handler($errorno, $errorstr, $errorfile, $errorline) {
		throw new ErrorException($errorstr, $errorno, 1, $errorfile, $errorline);
		return true;
	}
}

class Router {
	private static $_routes = array();
	
	public static function GET($path, $action) {
		self::verb('GET', $path, $action);
	}

	public static function POST($path, $action) {
		self::verb('POST', $path, $action);
	}

	public static function PUT($path, $action) {
		self::verb('PUT', $path, $action);
	}

	public static function DELETE($path, $action) {
		self::verb('DELETE', $path, $action);
	}

	public static function HEAD($path, $action) {
		self::verb('HEAD', $path, $action);
	}

	protected static function verb($verb, $path, $action) {
		$verbs = array();

		if (isset(self::$_routes[$verb])) {
			$verbs = self::$_routes[$verb];
		}

		$verbs[$path] = $action;
		self::$_routes[$verb] = $verbs;
	}

	public static function findRouteFor($verb, $path) {
		// TODO there are prolly ways optimize how we're storing the paths and clojures
		$routes = self::$_routes[$verb];
		$keys = array_keys($routes);

		foreach ($keys as $key) {
			$holder = self::holderMatchesPath(self::paramsToRegexp($key), $path);

			if ($holder != false) {
				$holder->callback = $routes[$key];
				return $holder;
			}
		}
		return false;
	}

	/*
	 * Transform the path to a UrlDTO.
	 */
	private static function paramsToRegexp($url) {
		$holder = new UrlDTO();
		$url = '(^'.str_replace('|', '$|^', $url).'$)'; // turn it into a working regexp.
		$matches = array();
		if (preg_match_all('(:[a-z0-9]+)', $url, $matches) > 0) { // TODO should also take !; and posibly something more(|)? Not ? though ;)
			// foreach match, replace :param with a regexp.
			foreach ($matches[0] as $match) {
				$holder->params[] = substr($match, 1); // removes the : in :param
				$url = str_replace($match, '([a-zA-Z0-9]+)', $url);
			}
		}

		$holder->path = $url;
		return $holder;
	}

	/*
	 * Return either the holder or false. False signaling not a match.
	 */
	private static function holderMatchesPath($holder, $path) {
		$matches = array();
		if (preg_match_all($holder->path, $path, $matches) > 0) {
			if (count($matches) == 1) {
				return $holder;
			} else if (count($matches) > 1) {
				$keys = $holder->params;
				$params = array();
				$i = 0;
				while ($i < count($keys)) {
					$params[$keys[$i]] = $matches[$i+1][0];
					$i++;
				}

				$holder->params = $params;
				return $holder;
			}
		}
		return false;
	}
}

class UrlDTO {
	public $path = '';
	public $params = array();
	public $callback = null;
}
?>
