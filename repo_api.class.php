<?php
require_once('api.class.php');  //should be autoloaded

/**
 * Implementation of various repository calls
 * Currently only for GitHub repository searches
 * @author  Martin Marek
 * @version 1.337
 * @license CC BY-SA 2.0 https://creativecommons.org/licenses/by-sa/2.0/
 */

class repo_api extends http_api{

    /**
     * Constructor
     * @todo implement CORS restrictions using $origin if needed
     * @param mixed $request input from the user
     * @param mixed $server global variable $server passed as an argument
     * @param mixed $origin from $_SERVER['HTTP_ORIGIN']
     * @return mixed
     */
    public function __construct($request, $server, $origin){
        parent::__construct($request, $server);
    }

    /**
     * GitHub endpoint
     * @return mixed
     */
     protected function github(){
        if($this->method == 'GET'){
            try{
                $github_repo = new github_repo($this->params[0], $this->params[1], $this->params[2], $this->params[3]);
                var_dump('Query: '.$github_repo->get_query());
                $github_repo->print_result();
            }
            catch(Exception $e){
                var_dump($e->getMessage());
            }
            return;
        }
        else {
            return 'Only accepts GET requests';
        }
     }
 }