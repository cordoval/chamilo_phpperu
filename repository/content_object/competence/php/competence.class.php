<?php
namespace repository\content_object\competence;

use repository\content_object\indicator\Indicator;

use common\libraries\Utilities;
use common\libraries\Versionable;
use common\libraries\ComplexContentObjectSupport;

use repository\ContentObject;

/**
 *  $Id: competence.class.php 200 2009-11-13 12:30:04Z kariboe $
 *  @package repository.lib.content_object.competence
 *  @author Sven Vanpoucke
 */
/**
 * This class represents an competence
 */
class Competence extends ContentObject implements Versionable, ComplexContentObjectSupport
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}

	function get_allowed_types()
    {
        return array(Indicator :: get_type_name());
    }
}
?>