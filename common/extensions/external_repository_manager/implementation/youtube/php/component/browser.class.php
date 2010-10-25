<?php
namespace common\extensions\external_repository_manager\implementation\youtube;

use common\libraries\Request;

class YoutubeExternalRepositoryManagerBrowserComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        if (Request :: get(YoutubeExternalRepositoryManager :: PARAM_FEED_TYPE) == YoutubeExternalRepositoryManager :: FEED_STANDARD_TYPE)
        {
            $this->set_parameter(YoutubeExternalRepositoryManager :: PARAM_FEED_IDENTIFIER, Request :: get(YoutubeExternalRepositoryManager :: PARAM_FEED_IDENTIFIER));
        }
        
        ExternalRepositoryComponent :: launch($this);
    }
}
?>