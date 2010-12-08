<?php
namespace common\extensions\external_repository_manager\implementation\wikipedia;

use common\extensions\external_repository_manager\ExternalRepositoryObject;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\ToolbarItem;

use repository\RepositoryDataManager;

class WikipediaExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'wikipedia';

    const PROPERTY_URLS = 'urls';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_URLS));
    }

    function get_urls()
    {
        return $this->get_default_property(self :: PROPERTY_URLS);
    }

    function set_urls($urls)
    {
        return $this->set_default_property(self :: PROPERTY_URLS, $urls);
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

	function get_content_data()
	{
		return file_get_contents($this->get_urls());
	}

	function get_render_url()
	{
	    return $this->get_urls() . '?action=render';
	}
}
?>