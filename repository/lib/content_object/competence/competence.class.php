<?php
/**
 *  $Id: competence.class.php 200 2009-11-13 12:30:04Z kariboe $
 *  @package repository.lib.content_object.competence
 *  @author Sven Vanpoucke
 */
/**
 * This class represents an competence
 */
class Competence extends ContentObject
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	function get_allowed_types()
    {
        return array('indicator');
    }
}
?>