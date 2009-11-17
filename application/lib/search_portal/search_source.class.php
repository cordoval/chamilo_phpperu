<?php
/**
 * $Id: search_source.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal
 */
interface SearchSource
{

    function search($query);

    static function is_supported();
}
?>