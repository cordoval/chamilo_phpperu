<?php
namespace common\libraries;

use repository\RepositoryManager;
use repository\RepositoryDataManager;
use repository\ComplexDisplayPreview;

use common\libraries\Translation;

class ComplexDisplayPreviewLauncher extends LauncherApplication
{
    const APPLICATION_NAME = 'complex_display_preview';

    function ComplexBuilderLauncher($user)
    {
        parent :: __construct($user);
    }

    function display_header()
    {
        parent :: display_header();

        $html[] = '<div class="warning-banner">';
        $html[] = Translation :: get('PreviewModeWarning');
        $html[] = '</div>';
        echo implode("\n", $html);
    }

    function run()
    {
        $content_object = $this->get_root_content_object();
        $this->set_parameter(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $content_object->get_id());

        if ($content_object)
        {
            ComplexDisplayPreview :: launch($content_object->get_type(), $this);
        }
        else
        {
            $this->display_error_page(Translation :: get('NoObjectSelected'));
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