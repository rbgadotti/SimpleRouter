<?php

	/**
	 * Simple Router
	 * PHP Router in a single file.
	 *
	 * @author 		Rafael Gadotti Bachovas
	 * @link      https://github.com/rbgadotti/simple-router
	 */

	class SimpleRoute {

		private $ALLOWED_METHODS = array('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS');

    /**
     * Route method (GET, POST, PUT, PATCH, DELETE, OPTIONS).
     * @var string
     */
		private $method;

    /**
     * URL.
     * @var string
     */
		private $url;

    /**
     * Callback function.
     * @var callable
     */
		private $callback;

    /**
     * Create route.
     * @param string 		$method Request method
     * @param string 		$url URL
     * @param callable 	$callback Route callback.
     * @access public
     * @return boolean
     */
		public function __construct($method = 'GET', $url = null, $callback = null){

			if(in_array($method, $this->ALLOWED_METHODS)){
				$this->method = $method;
			}else{
				throw new Exception('Route method not permited');
			}

			if(!is_null($url)){
				$this->url = $url;				
			}else{
				throw new Exception('Route url can\'t be null');
			}

			if(!is_null($callback) && is_callable($callback)){
				$this->callback = $callback;
			}else{
				throw new Exception('Route callback needs to be callable');	
			}

		}

		public function getMethod(){ return $this->method; }
		public function getUrl(){ return $this->url; }
		public function getCallback(){ return $this->callback; }

	}

	class SimpleRouter {

    /**
     * Array of array with routes informations.
     * @var SimpleRoute[]
     */
		private $routes = array();

    /**
     * Simulated request params
     * @var array
     */
    private $SIMULATED_REQUEST = array();

		public function __construct(){}

    /**
     * Set simulated request params. For Debug.
     * @param string 		$method Request method
     * @param string 		$url URL
     * @param string 		$qs Query String.
     * @access public
     * @return boolean
     */
    public function setSimulatedRequest($method = null, $url = null, $qs = null){

    	return $this->SIMULATED_REQUEST = array(
    		'REQUEST_METHOD' => $method,
    		'REQUEST_URI' => $url,
    		'QUERY_STRING' => $qs
    	);

    }

    /**
     * Create SimpleRoute object and add to array of routes.
     * @param string 		$method Request method
     * @param string 		$url URL
     * @param callable 	$callback Callback function.
     * @access public
     * @return void
     */
    public function route($method, $url, $callback){
    	return $this->routes[] = new SimpleRoute($method, $url, $callback);
    }

    /**
     * Create SimpleRoute object with GET method.
     * @param string 		$url URL
     * @param callable 	$callback Callback function.
     * @access public
     * @return void
     */
		public function get($url, $callback){
			return $this->route('GET', $url, $callback);
		}

    /**
     * Create SimpleRoute object with POST method.
     * @param string 		$url URL
     * @param callable 	$callback Callback function.
     * @access public
     * @return void
     */
		public function post($url, $callback){
			return $this->route('POST', $url, $callback);
		}

    /**
     * Create SimpleRoute object with PATCH method.
     * @param string 		$url URL
     * @param callable 	$callback Callback function.
     * @access public
     * @return void
     */
		public function patch($url, $callback){
			return $this->route('PATCH', $url, $callback);
		}

    /**
     * Create SimpleRoute object with PUT method.
     * @param string 		$url URL
     * @param callable 	$callback Callback function.
     * @access public
     * @return void
     */
		public function put($url, $callback){
			return $this->route('PUT', $url, $callback);
		}

    /**
     * Create SimpleRoute object with DELETE method.
     * @param string 		$url URL
     * @param callable 	$callback Callback function.
     * @access public
     * @return void
     */
		public function delete($url, $callback){
			return $this->route('DELETE', $url, $callback);
		}

    /**
     * Create SimpleRoute object with OPTIONS method.
     * @param string 		$url URL
     * @param callable 	$callback Callback function.
     * @access public
     * @return void
     */
		public function options($url, $callback){
			return $this->route('OPTIONS', $url, $callback);
		}

    /**
     * Create SimpleRoute object for all allowed methods.
     * @param string 		$url URL
     * @param callable 	$callback Callback function.
     * @access public
     * @return void
     */
		public function any($url, $callback){
			$this->get($url, $callback);
			$this->post($url, $callback);
			$this->patch($url, $callback);
			$this->put($url, $callback);
			$this->delete($url, $callback);
			$this->options($url, $callback);
		}

    /**
     * Get request (or simulated request) info and call the reference callback.
     * @access public
     * @return void
     */
		public function run(){

			$requestUri 		= !isset($this->SIMULATED_REQUEST['REQUEST_URI']) 		? $_SERVER['REQUEST_URI'] 		: $this->SIMULATED_REQUEST['REQUEST_URI'];
			$requestMethod 	= !isset($this->SIMULATED_REQUEST['REQUEST_METHOD']) 	? $_SERVER['REQUEST_METHOD'] 	: $this->SIMULATED_REQUEST['REQUEST_METHOD'];
			$queryString 		= !isset($this->SIMULATED_REQUEST['QUERY_STRING']) 		? $_SERVER['QUERY_STRING'] 		: $this->SIMULATED_REQUEST['QUERY_STRING'];

			foreach($this->routes as $route){

				$pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($route->getUrl())) . "$@D";
				$requestQueryParams = array();

        if($requestMethod == $route->getMethod() && preg_match($pattern, $requestUri, $requestQueryParams)) {
        	// Remove the first match
					array_shift($requestQueryParams);
					// Call the callback with the matched positions as params
					return call_user_func_array($route->getCallback(), $requestQueryParams);
        }

			}

		}

	}
