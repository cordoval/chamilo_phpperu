<?php
namespace common\extensions\external_repository_manager\implementation\slideshare;

use common\extensions\external_repository_manager\StreamingMediaExternalRepositoryObject;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\ToolbarItem;
use common\libraries\Path;

use repository\RepositoryDataManager;

require_once Path :: get_common_extensions_path() . 'external_repository_manager/php/general/streaming/streaming_media_external_repository_object.class.php';

class SlideshareExternalRepositoryObject extends StreamingMediaExternalRepositoryObject
{
    const OBJECT_TYPE = 'slideshare';

    const PROPERTY_URLS = 'urls';
    const PROPERTY_EMBED = 'embed';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_URLS, self :: PROPERTY_TAGS));
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
    
    function is_usable()
    {
        return $this->get_right(self :: RIGHT_USE);
    }
    
	function get_embed()
    {
        return $this->get_default_property(self :: PROPERTY_EMBED);
    }

    function set_embed($embed)
    {
        return $this->set_default_property(self :: PROPERTY_EMBED, $embed);
    }
    
	function get_content_data($external_object)
	{		
		$external_repository = RepositoryDataManager :: get_instance()->retrieve_external_instance($this->get_external_repository_id());		
		return SlideshareExternalRepositoryManagerConnector :: get_instance($external_repository)->download_external_repository_object($external_object);
	}
}
?>