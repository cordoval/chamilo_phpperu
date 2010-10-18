<?php
/**
 * @package common.html.formvalidator.Rule
 */
// $Id: Filetype.php 128 2009-11-09 13:13:20Z vanpouckesven $
require_once ('HTML/QuickForm/Rule.php');
/**
 * QuickForm rule to check if a filetype
 */
class HTML_QuickForm_Rule_Filetype extends HTML_QuickForm_Rule
{

    /**
     * Function to check if a filetype is allowed
     * @see HTML_QuickForm_Rule
     * @param array $file Uploaded file
     * @param array $extensions Allowed extensions
     * @return boolean True if filetype is allowed
     */
    function validate($file, $extensions = array())
    {
        $parts = explode('.', $file['name']);
        if (count($parts) < 2)
        {
            return false;
        }
        $ext = $parts[count($parts) - 1];
        return in_array($ext, $extensions);
    }
}
?>