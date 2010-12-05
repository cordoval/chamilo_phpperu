<?php
namespace common\libraries;

use common\libraries\Request;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use repository\RepositoryDataManager;
use repository\RepositoryManager;

class ExternalRepositoryLauncher extends LauncherApplication
{
    const APPLICATION_NAME = 'external_repository';

    private $external_instance;

    function __construct($user)
    {
        parent :: __construct($user);
    }

    function run()
    {
        $type = $this->get_type();
        $this->external_instance = RepositoryDataManager :: get_instance()->retrieve_external_instance($type);
        $this->set_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, $type);

        ExternalRepositoryManager :: launch($this);
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

    /**
     * Get a series of links for all external repository instances of one or more types
     *
     * @param array $types An array of external repository manager types
     * @param unknown_type $auto_open if there is only one instance, should it be opened automatically
     * @return string
     */
    function get_links($manager_types = array(), $types = array(), $auto_open = false)
    {
        $instances = RepositoryDataManager :: get_instance()->retrieve_active_external_instances($manager_types, $types);

        if ($instances->size() == 0)
        {
            if (!is_array($types))
            {
                $types = array($types);
            }
            
            $type_names = array();
            foreach ($types as $type)
            {
                $type_names[] = Translation :: get('TypeName', null, ExternalRepositoryManager :: get_namespace($type));
            }
            $type_names = implode(', ', $type_names);
            
            if (count($types) > 1)
            {
                $translation = Translation :: get('NoExternalInstanceTypeManagersAvailable', array('TYPES' => $type_names), RepositoryManager :: APPLICATION_NAME);
            }
            else
            {
                $translation = Translation :: get('NoExternalInstanceTypeManagerAvailable', array('TYPES' => $type_names), RepositoryManager :: APPLICATION_NAME);
            }
            
            return Display :: warning_message($translation, true);
        }
        else
        {
            $html = array();
            $buttons = array();

            while ($instance = $instances->next_result())
            {
                $link = Path :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=' . ExternalRepositoryLauncher :: APPLICATION_NAME . '&' . RepositoryManager :: PARAM_EXTERNAL_INSTANCE . '=' . $instance->get_id();
                $image = Theme :: get_image_path(ExternalRepositoryManager :: get_namespace($instance->get_type())) . 'logo/16.png';
                $title = Translation :: get('BrowseObject', array('OBJECT' => $instance->get_title()), Utilities :: COMMON_LIBRARIES);
                $buttons[] = '<a class="button normal_button upload_button" style="background-image: url(' . $image . ');" onclick="javascript:openPopup(\'' . $link . '\');"> ' . $title . '</a>';
            }

            $html[] = '<div style="margin-bottom: 10px;">' . implode(' ', $buttons) . '</div>';

            if ($instances->size() == 1 && $auto_open)
            {
                $html[] = '<script type="text/javascript">';
                $html[] = '$(document).ready(function ()';
                $html[] = '{';
                $html[] = '	openPopup(\'' . $link . '\');';
                $html[] = '});';
                $html[] = '</script>';
            }

            return implode("\n", $html);
        }
    }
}
?>