<?php
/**
 * $Id: repository_search_result.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal
 */
class RepositorySearchResult
{
    private $repository_title;
    
    private $repository_url;
    
    private $returned_results;
    
    private $actual_result_count;

    function RepositorySearchResult($repository_title, $repository_url, $returned_results, $actual_result_count)
    {
        $this->repository_title = $repository_title;
        $this->repository_url = $repository_url;
        $this->returned_results = $returned_results;
        $this->actual_result_count = $actual_result_count;
    }

    function get_repository_title()
    {
        return $this->repository_title;
    }

    function get_repository_url()
    {
        return $this->repository_url;
    }

    function get_returned_results()
    {
        return $this->returned_results;
    }

    function get_actual_result_count()
    {
        return $this->actual_result_count;
    }
}
?>