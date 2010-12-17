<?php
namespace repository\content_object\slideshare;

use common\libraries\Utilities;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * $Id: slideshare.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.slideshare
 */
class Slideshare extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_EMBED = 'embed';    
    
    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function get_embed()
    {
        $default = $this->get_synchronization_data()->get_external_object()->get_default_properties();
    	return $default['embed'];
    }
}
?>