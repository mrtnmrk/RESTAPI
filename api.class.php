<?php

/**
* Abstract API REST class for working with HTTP
* @author    Martin Marek
* @version   1.337
* @copyright CC BY-SA 2.0 https://creativecommons.org/licenses/by-sa/2.0/
*/

abstract class http_api{
    /**
     * HTTP method of the request (GET, POST, PUT or DELETE)
     * @var string
     */
    protected $method = '';

    /**
     * Model requested in the URL. e.g. /github
     * @var string
     */
    protected $endpoint = '';

    /**
     * Further specification of the process (e.g. "search" in /github/search/), mandatory
     * @var string
     */
    protected $action = '';

    /**
     * Input of the PUT request
     * @var mixed
     */
     protected $file = null;

    /**
     * Parameters added after endpoint and action: /<endpoint>/<action>/<param0>/<param1>
     * @var array
     */
    protected $params = array();

    /**
     * General function for returning the data
     * @todo parametrization of the output format @see $this->__constructor
     * @param mixed $data to be returned
     * @param integer $code inputs from the outside, that need to be sanitized
     * @return mixed
     */
    private function response($data, $code = 200) {
        header('HTTP/1.1 '.$status.' '.$this->get_status($code));
        // return json_encode($data);
        return $data;
    }

    /**
     * Sanitizing inputs received from the outside.
     * Accepts mixed parameter and returns it in the same structure (e.g. nested arrays)
     * @todo implement better way of sanitization like filter_var()
     * @param mixed $dirty_inputs inputs from the outside, that need to be sanitized
     * @return mixed sanitized inputs
     */
    private function sanitize_inputs($dirty_input) {
        $clean_inputs = array();
        if(is_array($dirty_inputs)){
            foreach ($dirty_inputs as $key => $value) {
                $clean_inputs[$key] = $this->sanitize_inputs($value);
            }
        }
        else{
            $clean_inputs = trim(strip_tags($data)); //or something better
        }
        return $clean_inputs;
    }

    /**
     * Translates status codes into english
     * @param mixed $code of the status
     * @return string
     */
    private function get_status($code) {
        $status = array(
            200 => 'OK',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return $status[$code] ? $status[$code] : $status[500];
    }

    /**
     * Constructor
     * Allows CORS, assemble and pre-process the data
     * @todo header for Content-Type parametrization
     * @param string $request
     * @param mixed $server global variable $server passed as an argument
     * @return void
     */
    public function __construct($request, $server){
        header('Access-Control-Allow-Orgin: *');    //allow requests from any origin to be processed by
        header('Access-Control-Allow-Methods: *');  //allow any HTTP method to be accepted
        // header('Content-Type: application/json');   //output type could be parametrized

        $this->params = explode('/', rtrim($request, '/')); //this could be further enhanced to allow different structure (e.g. classical ?param1=val1&param2=val2)
        $this->endpoint = array_shift($this->params);
        if(array_key_exists(0, $this->params)){
           $this->action = array_shift($this->params);
        }
        else{
            throw new Exception('Missing action parameter (/<endpoint>/<action>/<param0>/<param1>/...)');
        }
        $this->method = $server['REQUEST_METHOD'];
        if($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $server)){    //PUT and DELETE are inside POST
            if($server['HTTP_X_HTTP_METHOD'] == 'DELETE' || $server['HTTP_X_HTTP_METHOD'] == 'PUT'){
                $this->method = $server['HTTP_X_HTTP_METHOD'];
            }
            else{
                throw new Exception('Unexpected Header');
            }
        }
        if($this->method == 'POST' || $this->method == 'DELETE'){
            $this->request = $this->sanitize_inputs($_POST);
        }
        elseif($this->method == 'GET'){
            $this->request = $this->sanitize_inputs($_GET);
        }
        elseif($this->method == 'PUT'){
            $this->request = $this->sanitize_inputs($_GET);
            $this->file = file_get_contents('php://input');
        }
        else{
            $this->response('Invalid Method', 405);
        }
        return;
    }

    public function process_api(){
        if(method_exists($this, $this->endpoint)){
            return $this->response($this->{$this->endpoint}($this->params));
        }
        return $this->response('No Endpoint: '.$this->endpoint, 404);
    }

}