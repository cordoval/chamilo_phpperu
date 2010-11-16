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
    const PROPERTY_LICENSE = 'license';

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

    function get_license()
    {
        return $this->get_default_property(self :: PROPERTY_LICENSE);
    }

    function get_license_icon()
    {
        $icon = new ToolbarItem($this->get_license(), Theme :: get_image_path() . 'licenses/' . $this->get_license() . '.png', null, ToolbarItem :: DISPLAY_ICON);
        return $icon->as_html();
    }

    function set_license($license)
    {
        return $this->set_default_property(self :: PROPERTY_LICENSE, $license);
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }
}
?>