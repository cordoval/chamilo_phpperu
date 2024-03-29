<?php
namespace common\libraries;

use repository\RepositoryManager;
use repository\RepositoryDataManager;
use repository\ComplexBuilder;

class ComplexBuilderLauncher extends LauncherApplication
{
    const APPLICATION_NAME = 'complex_builder';

    function __construct($user)
    {
        parent :: __construct($user);
    }

    function run()
    {
        $content_object = $this->get_root_content_object();
        $this->set_parameter(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $content_object->get_id());

        if ($content_object)
        {
            ComplexBuilder :: launch($content_object->get_type(), $this);
        }
        else
        {
            $this->display_error_page(Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES));
        }
    }

    function get_root_content_object()
    {
        $content_object_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        return RepositoryDataManager :: get_instance()->retrieve_content_object($content_object_id);
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