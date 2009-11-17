<?php
/**
 * @package common.html.formvalidator.Rule
 */
// $Id: UsernameAvailable.php 128 2009-11-09 13:13:20Z vanpouckesven $
require_once ('HTML/QuickForm/Rule.php');
/**
 * QuickForm rule to check if a username is available
 */
class HTML_QuickForm_Rule_UsernameAvailable extends HTML_QuickForm_Rule
{

    /**
     * Function to check if a username is available
     * @see HTML_QuickForm_Rule
     * @param string $username Wanted username
     * @param string $current_username 
     * @return boolean True if username is available
     */
    function validate($username, $current_username = null)
    {
        // TODO: Contact UserDataManager for this ...
        //		$user_table = Database::get_main_table(MAIN_USER_TABLE);
        //		$sql = "SELECT * FROM $user_table WHERE username = '$username'";
        //		if(!is_null($current_username))
        //		{
        //			$sql .= " AND username != '$current_username'";
        //		}
        //		$res = api_sql_query($sql,__FILE__,__LINE__);
        //		$number = mysql_num_rows($res);
        //		return $number == 0;
        return true;
    }
}
?>