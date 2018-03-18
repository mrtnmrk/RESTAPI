<?php
require_once('repo_api.class.php');		//should be done by autoloader
require_once('github_repo.class.php');	//should be done by autoloader

try{
    $server_http_origin = $_SERVER['HTTP_ORIGIN'];
    if(!array_key_exists('HTTP_ORIGIN', $_SERVER)){
        $server_http_origin = $_SERVER['SERVER_NAME'];  // for requests from the same server (they usually don't have this header)
    }
    $repo_api = new repo_api($_REQUEST['request'], $_SERVER, $server_http_origin);
    $repo_api->process_api();
}
catch(Exception $e){
    var_dump('Exception: '.$e->getMessage());
}