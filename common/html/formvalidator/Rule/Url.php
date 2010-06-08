<?php
/**
 * @package common.html.formvalidator.Rule
 */
// $Id: Url.php 128 2009-11-09 13:13:20Z tristan verheecke $
require_once ('HTML/QuickForm/Rule.php');
/**
 * QuickForm rule to check if a url is of the correct format
 */
class HTML_QuickForm_Rule_Url extends HTML_QuickForm_Rule
{

    /**
     * Function to check if a url is of the correct format
     * @see HTML_QuickForm_Rule
     * @param string $url Wanted url
     * @return boolean True if url is of the correct format
     */
	
    function validate($url)
    {
    	$result = preg_match('/^(http\:\/\/[a-zA-Z0-9_\-]+(?:\.[a-zA-Z0-9_\-]+)*\.[a-zA-Z]{2,4}(?:\/[a-zA-Z0-9_\-]+)*(?:\/[a-zA-Z0-9_]+\.[a-zA-Z]{2,4}(?:\?[a-zA-Z0-9_\-]+=[a-zA-Z0-9_\-]+)?)?(?:\&[a-zA-Z0-9_\-]+\=[a-zA-Z0-9_\-]+)*)$/', $url);
    	return $result;
    }
}
?>