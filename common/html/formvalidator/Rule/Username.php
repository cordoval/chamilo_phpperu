<?php
/**
 * @package common.html.formvalidator.Rule
 */
// $Id: Username.php 128 2009-11-09 13:13:20Z vanpouckesven $
require_once ('HTML/QuickForm/Rule.php');
/**
 * QuickForm rule to check if a username is of the correct format
 */
class HTML_QuickForm_Rule_Username extends HTML_QuickForm_Rule
{

    /**
     * Function to check if a username is of the correct format
     * @see HTML_QuickForm_Rule
     * @param string $username Wanted username
     * @return boolean True if username is of the correct format
     */
    function validate($username)
    {
        $filtered_username = eregi_replace('[^a-z0-9_.-@]', '_', strtr($username, 'ְֱֲֳִֵאבגדהוׂ׃װױײ״עףפץצרָֹÊֻטיךכַחּֽ־ֿלםמןÙÚÛÜשתûüÿׁס', 'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn'));
        return $filtered_username == $username;
    }
}
?>