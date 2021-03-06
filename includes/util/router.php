<?php

class Router {
	function __construct( $routes ) {
		$this->dispatcher = FastRoute\cachedDispatcher( $routes, array(
			'cacheFile' => __DIR__ .'/../../routes.cache'
		));
	}

	function dispatch() {
		global $params;

		$httpMethod = $_SERVER['REQUEST_METHOD'];
		$uri = $_SERVER['REQUEST_URI'];

		$request_body = file_get_contents( 'php://input' );
		$params = json_decode( $request_body );

		// Strip query string (?foo=bar) and decode URI
		if (false !== $pos = strpos($uri, '?')) {
			$uri = substr($uri, 0, $pos);
		}
		$uri = rawurldecode($uri);

		$routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

		switch ($routeInfo[0]) {
			case FastRoute\Dispatcher::NOT_FOUND:
				Controller::not_found();
				break;
			case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
				$allowedMethods = $routeInfo[1];
				// ... 405 Method Not Allowed
				echo 2;
				break;
			case FastRoute\Dispatcher::FOUND:
				$this->callAction( $routeInfo );
				break;
		}
	}

	private function callAction( $routeInfo ) {
		call_user_func( $routeInfo[1], $routeInfo[2] );
		exit;
	}
}
