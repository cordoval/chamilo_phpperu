<?php
namespace common\extensions\external_repository_manager\implementation\soundcloud;

use common\extensions\external_repository_manager\ExternalRepositoryObject;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\ToolbarItem;

class SoundcloudExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'soundcloud';

    const PROPERTY_ARTWORK = 'artwork';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ARTWORK));
    }

    function get_artwork()
    {
        return $this->get_default_property(self :: PROPERTY_ARTWORK);
    }

    function set_artwork($artwork)
    {
        return $this->set_default_property(self :: PROPERTY_ARTWORK, $artwork);
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }
}
?>