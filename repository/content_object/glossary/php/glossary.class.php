<?php
namespace repository\content_object\glossary;

use common\libraries\Utilities;
use common\libraries\ComplexContentObjectSupport;

use repository\ContentObject;

/**
 * $Id: glossary.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.glossary
 */
/**
 * This class represents an glossary
 */
class Glossary extends ContentObject implements ComplexContentObjectSupport
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    function get_allowed_types()
    {
        return array(GlossaryItem :: get_type_name());
    }
}
?>