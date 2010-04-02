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
	function get_allowed_types()
    {
        return array('indicator');
    }
}
?>