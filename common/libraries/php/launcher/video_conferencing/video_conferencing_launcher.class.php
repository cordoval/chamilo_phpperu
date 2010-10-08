<?php
class VideoConferencingLauncher extends LauncherApplication
{
    const APPLICATION_NAME = 'external_repository';

    function VideoConferencingLauncher($user)
    {
        parent :: __construct($user);
    }

    function run()
    {
        $type = $this->get_type();
        $this->external_repository = RepositoryDataManager :: get_instance()->retrieve_video_conferencing($type);
        $this->set_parameter(VideoConferencingManager :: PARAM_VIDEO_CONFERENCING, $type);
        
        VideoConferencingManager :: launch($this);
    }

    function get_type()
    {
        return Request :: get(VideoConferencingManager :: PARAM_VIDEO_CONFERENCING);
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

    function get_video_conferencing()
    {
        return $this->video_conferencing;
    }
}
?>