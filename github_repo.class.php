<?php

require_once('repository.class.php');   //should be autoloaded

/**
 * Class for working with GitHub repository through it's API
 * Currently used only for searching
 * @author  Martin Marek
 * @version 1.337
 * @license CC BY-SA 2.0 https://creativecommons.org/licenses/by-sa/2.0/
 */

class github_repo extends repository{

    /**
     * GitHub API URL
     * Currently only for repository search
     * @var string
     */
    private $url = 'https://api.github.com/search/repositories';

    /**
     * Available sorting parameters
     * @var string
     */
    private $allowed_sorting = array('stars', 'fork', 'updated');

    /**
     * Prepare complete query string
     * Takes keyword, page_number, result_per_page and sort_by from parent class
     * @return object
     */
    private function build_query(){
        if(!isset($this->keyword)){
            throw new Exception('Missing keyword search: http://.../github/search/<keyword>');
        }
        $this->query = $this->url.'?q='.$this->keyword;
        $this->query .= '&page='.intval($this->page_number);
        $this->query .= '&per_page='.intval($this->results_per_page);
        if($this->sort_by[0]) $this->query .= '&sort='.$this->sort_by;  //TODO: sanitize a mozna lepsi podminka
        return $this;
    }

    /**
     * Returns raw result from the GitHub
     * Stores it in result from parent class
     * @return object
     */
    private function get_result(){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->get_query());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');  //old, but still valid
        //curl_setopt($curl, CONNECTTIMEOUT, 1);
        $content = curl_exec($curl);
        //echo $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);  //this could be use for exception throwing
        curl_close($curl);
        $this->result = $content;   //results comes from the parent abstract class
        return $this;
    }

    /**
     * Parses raw result from GitHub (which is in JSON)
     * @return object
     */
    private function parse_result(){
        return json_decode($this->result);
    }

    /**
     * Returns URL query string sent to repository
     * This method should be private (no need to expose the query), but I left it public for demonstration purpose
     * @return string
     * */
    public function get_query(){
        return $this->build_query()->query;
    }

    /**
     * Output parsed result in HTML format
     * @return string
     * */
    public function print_result(){
        $parsed_result = (array) $this->get_result()->parse_result();
        foreach($parsed_result['items'] as $repository_detail){
            $out .= $repository_detail->full_name.': '.$repository_detail->description.'<br />';
        }
        echo $out;
    }
}

