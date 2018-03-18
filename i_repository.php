<?php

/**
* Interface for classes that want to be used to handle interaction between endu user and repositories
*
* @author    Martin Marek
* @version   1.337
* @copyright CC BY-SA 2.0 https://creativecommons.org/licenses/by-sa/2.0/
*/

interface i_repository{
    /**
     * Returns query (usualy complete URL) sent to repository (product of endpoint)
     * This method should be private (no need to expose the query), but I left it public for demonstration purpose
     * @return string
     * */
    public function get_query();

    /**
     * Output parsed result in desired format (HTML, JSON, etc.)
     * @return mixed
     * */
    public function print_result();
}