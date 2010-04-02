<?php
/**
 *  $Id: indicator.class.php 200 2009-11-13 12:30:04Z kariboe $
 *  @package repository.lib.content_object.indicator
 *  @author Sven Vanpoucke
 */
/**
 * This class represents an indicator
 */
class Indicator extends ContentObject
{
	function get_allowed_types()
    {
        return array('criteria');
    }
}
?>