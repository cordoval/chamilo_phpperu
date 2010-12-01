<?php
namespace common\libraries;

use common\libraries\Request;
use repository\RepositoryDataManager;
use repository\RepositoryManager;
use common\extensions\video_conferencing_manager\VideoConferencingManager;

class VideoConferencingLauncher extends LauncherApplication
{
    const APPLICATION_NAME = 'video_conferencing';

    private $external_instance;
    
    function __construct($user)
    {
        parent :: __construct($user);
    }

    function run()
    {
        $type = $this->get_type();
        $this->external_instance = RepositoryDataManager :: get_instance()->retrieve_external_instance($type);
        $this->set_parameter(VideoConferencingManager :: PARAM_VIDEO_CONFERENCING, $type);

        VideoConferencingManager :: launch($this);
    }

    function get_type()
    {
        return Request :: get(RepositoryManager :: PARAM_EXTERNAL_INSTANCE);
    }

    public function get_link($parameters = array (), $filter = array(), $encode_entities = false, $application_type = Redirect :: TYPE_APPLICATION)
    {
        // Use this untill PHP 5.3 is available
    // Then use get_class($this) :: APPLICATION_NAME
    // and remove the get_application_name function();
    //$application = $this->get_application_name();
    //return Redirect :: get_link($application, $parameters, $filter, $encode_entities, $application_type);
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    function get_external_instance()
    {
        return $this->external_instance;
    }
}
?>