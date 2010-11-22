<?php
/**
 * @package common.html.formvalidator.Rule
 */
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
    $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
    $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
    $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
    $regex .= "(\:[0-9]{2,5})?"; // Port
    $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
    $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
    $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor

    	$result = preg_match("/^$regex$/", $url);
    	return $result;
    }
}
?>