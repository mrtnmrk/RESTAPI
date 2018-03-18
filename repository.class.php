<?php

require_once('i_repository.php'); //interface, should be autoloaded

/**
 * Abstract class for working with various code repositories
 * @author  Martin Marek
 * @version 1.337
 * @license CC BY-SA 2.0 https://creativecommons.org/licenses/by-sa/2.0/
 */

abstract class repository implements i_repository{

    /**
     * Keyword to be searched in repository name or description.
     * @var string
     */
    protected $keyword = '';

    /**
     * Index of currently displayed result page. Please note, that indexing may start at 0 or 1.
     * @var integer
     */
    protected $page_number = 0;

    /**
     * Number of hits retrieved per page.
     * @var integer
     */
    protected $results_per_page = 25;

    /**
     * Selector for the result ordering (score, last updated, etc.).
     * Left empty usualy means sorting by repository's default.
     * @var string
     */
    protected $sort_by = '';

    /**
     * Query sent to repository (url + endpoint + action + parameters)
     * @var string
     */
    protected $query;

    /**
     * Returns query (usualy complete URL) sent to repository
     * This method should be private (no need to expose the query), but I left it public for demonstration purpose
     * @return string
     * */
    public function get_query(){
        return $this->query;
    }

    /**
     * Output parsed result in desired format (HTML, JSON, etc.)
     * @return mixed
     * */
    public function print_result(){
        throw new Exception('Do not forget to define your printing method :)');
    }

    /**
     * Constructor that handles the inputs
     * Set to final to force the existence of them
     * @param string $keyword to be searched in repository name or description.
     * @param integer $page_number of currently displayed result page.
     * @param integer $results_per_page to be displayed
     * @param string $sort_by various parameters
     * @return void
     * */
    final public function __construct($keyword, $page_number, $results_per_page, $sort_by){
        $this->keyword = $keyword;
        $this->page_number = $page_number ?: 0;
        $this->results_per_page = $results_per_page ?: 25;
        $this->sort_by = $sort_by ?: null;
        return;
    }
}
