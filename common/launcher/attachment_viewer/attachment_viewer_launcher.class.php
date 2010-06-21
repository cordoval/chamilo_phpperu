<?php
class AttachmentViewerLauncher extends LauncherApplication
{
    const APPLICATION_NAME = 'attachment_viewer';

    function AttachmentViewerLauncher($user)
    {
        parent :: __construct($user);
    }

    function run()
    {
        $this->display_header();
        
        $object_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        if ($object_id)
        {
        	$object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
            $display = ContentObjectDisplay :: factory($object);
            echo $display->get_full_html();
        }
        else
        {
        	$this->display_error_message('NoObjectSelected');
        }
        
        $this->display_footer();
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
}
?>