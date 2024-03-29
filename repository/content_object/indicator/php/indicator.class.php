<?php
namespace repository\content_object\indicator;

use repository\content_object\criteria\Criteria;

use common\libraries\Utilities;
use common\libraries\Versionable;
use common\libraries\ComplexContentObjectSupport;

use repository\ContentObject;

/**
 *  $Id: indicator.class.php 200 2009-11-13 12:30:04Z kariboe $
 *  @package repository.lib.content_object.indicator
 *  @author Sven Vanpoucke
 */
/**
 * This class represents an indicator
 */
class Indicator extends ContentObject implements Versionable, ComplexContentObjectSupport
{
	function get_allowed_types()
    {
        return array(Criteria :: get_type_name());
    }

	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}
}
?>