<?php
/**
 * $Id: Max_Members_rule.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.formvalidator.Rule
 */
require_once ('HTML/QuickForm/Rule.php');
/**
 * QuickForm rule to check a date
 */
class HTML_QuickForm_Rule_Max_Members extends HTML_QuickForm_Rule
{
	const UNLIMITED_MEMBERS = 'unlimited_members';
	
	function validate($values)
	{
		if($values[0][self :: UNLIMITED_MEMBERS] || (is_numeric($values[1]) && $values[1] > 0))
			return true;
		else
			return false;
	}
}

?>