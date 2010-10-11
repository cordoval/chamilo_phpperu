<?php
/**
 *  $Id: complex_indicator.class.php 200 2009-11-13 12:30:04Z kariboe $
 *  @package repository.lib.content_object.indicator
 *  @author Sven Vanpoucke
 */

class ComplexIndicator extends ComplexContentObjectItem
{
	function get_allowed_types()
    {
        return array(Criteria :: get_type_name());
    }
}
?>