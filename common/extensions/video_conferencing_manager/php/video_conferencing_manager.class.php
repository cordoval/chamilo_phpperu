<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries\SubManager;
use common\libraries\LauncherApplication;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Path;
use common\libraries\Theme;
use common\libraries\Request;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\DynamicVisualTabsRenderer;
use common\libraries\DynamicVisualTab;

use admin\Registration;
use admin\AdminDataManager;

use repository\ExternalSetting;

abstract class VideoConferencingManager extends SubManager
{
    const PARAM_VIDEO_CONFERENCING_MANAGER_ACTION = 'conferencing_action';

    const ACTION_CREATE_MEETING = 'creator';
    const ACTION_CONFIGURE_VIDEO_CONFERENCING = 'configurer';

    const DEFAULT_ACTION = self :: ACTION_CREATE_MEETING;

    const PARAM_VIDEO_CONFERENCING_ID = 'video_conferencing_id';
    const PARAM_VIDEO_CONFERENCING = 'video_conferencing';
    const PARAM_QUERY = 'query';
    const PARAM_RENDERER = 'renderer';

    const NAMESPACE_NAME = __NAMESPACE__;
    const CLASS_NAME = __CLASS__;

    private $video_conferencing;

    /**
     * @param Application $application
     */
    function VideoConferencingManager($application)
    {
        parent :: __construct($application);
        $this->video_conferencing = $application->get_video_conferencing();

        $video_conferencing_manager_action = Request :: get(self :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION);
        if ($video_conferencing_manager_action)
        {
            $this->set_parameter(self :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION, $video_conferencing_manager_action);
        }

        $this->set_optional_parameters();

        if ($this->validate_settings($this->video_conferencing))
        {
            $this->initialize_video_conferencing($this);
        }
    }

    /**
     * @return VideoConferencing
     */
    function get_video_conferencing()
    {
        return $this->video_conferencing;
    }

    /**
     * @param VideoConferencing $video_conferencing
     */
    function set_video_conferencing(VideoConferencing $video_conferencing)
    {
        $this->video_conferencing = $video_conferencing;
    }

    /**
     * @return VideoConferencingConnector
     */
    function get_video_conferencing_connector()
    {
        return VideoConferencingConnector :: get_instance($this->get_video_conferencing());
    }

    function set_optional_parameters()
    {
        $this->set_parameter(self :: PARAM_RENDERER, $this->get_renderer());
    }

    /**
     * @return boolean
     */
    function is_stand_alone()
    {
        return $this->get_parent() instanceof LauncherApplication;
    }

    /**
     * @param ExternalRepository $external_repository
     * @param Application $application
     */
    static function launch($application)
    {
        $video_conferencing = $application->get_video_conferencing();
  
    	$type = $video_conferencing->get_type();

        $file = dirname(__FILE__) . '/../implementation/' . $type . '/php/' . $type . '_video_conferencing_manager.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('VideoConferencingManagerTypeDoesNotExist', array('type' => $type)));
        }

        require_once $file;

        $class = self :: NAMESPACE_NAME . '\implementation\\' . $type . '\\' . Utilities :: underscores_to_camelcase($type) . 'VideoConferencingManager';

        $settings_validated = call_user_func(array($class, 'validate_settings'), $video_conferencing);

        if (! $settings_validated)
        {
            if ($application->get_user()->is_platform_admin())
            {
                Request :: set_get(Application :: PARAM_ERROR_MESSAGE, Translation :: get('PleaseReviewSettings'));
                Request :: set_get(self :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION, self :: ACTION_CONFIGURE_VIDEO_CONFERENCING);            }
            else
            {
                parent :: display_header();
                $application->display_warning_message('TemporarilyUnavailable');
                parent :: display_footer();
                exit();
            }
        }
        parent :: launch($class, $application);
    }

    /**
     * @return string
     */
    function is_ready_to_be_used()
    {
        return false;
    }

    function any_object_selected()
    {
    }

    /* (non-PHPdoc)
     * @see common/SubManager#get_application_component_path()
     */
    //    function get_application_component_path()
    //    {
    //        return Path :: get_common_extensions_path() . 'external_repository_manager/component/';
    //    }


    /**
     * @return string
     */
    function get_action()
    {
        return $this->get_parameter(self :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION);
    }

    function display_header()
    {
        $action = $this->get_action();
        parent :: display_header();

        $html = array();
        $video_conferencing_actions = $this->get_video_conferencing_actions();

        $tabs = new DynamicVisualTabsRenderer(Utilities :: get_classname_from_object($this, true));

        foreach ($video_conferencing_actions as $video_conferencing_action)
        {
            if ($action == $video_conferencing_action)
            {
                $selected = true;
            }
            else
            {
                $selected = false;
            }

            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION] = $video_conferencing_action;

            $label = htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($video_conferencing_action) . 'Title'));
            $link = $this->get_url($parameters, true);

            $tabs->add_tab(new DynamicVisualTab($video_conferencing_action, $label, Theme :: get_common_image_path() . 'place_tab_' . $video_conferencing_action . '.png', $link, $selected));
        }

        $html[] = $tabs->header();
        $html[] = DynamicVisualTabsRenderer :: body_header();

        echo implode("\n", $html);
    }

    /**
     * @return array
     */
    function get_video_conferencing_actions()
    {
        $actions = array();
        $actions[] = self :: ACTION_CREATE_MEETING;

        $is_platform = $this->get_user()->is_platform_admin() && (count(ExternalSetting :: get_all($this->get_video_conferencing()->get_id())) > 0);

        if ($is_platform)
        {
            $actions[] = self :: ACTION_CONFIGURE_VIDEO_CONFERENCING;
        }

        return $actions;
    }

    function display_footer()
    {
        $html = array();
        $html[] = DynamicVisualTabsRenderer :: body_footer();
        $html[] = DynamicVisualTabsRenderer :: footer();
        echo implode("\n", $html);

        parent :: display_footer();
    }

    /**
     * @param mixed $condition
     */
    function count_video_conferencing_objects($condition)
    {
        return $this->get_video_conferencing_connector()->count_video_conferencing_objects($condition);
    }

    /**
     * @param mixed $condition
     * @param ObjectTableOrder $order_property
     * @param int $offset
     * @param int $count
     * @return ArrayResultSet
     */
    function retrieve_video_conferencing_objects($condition, $order_property, $offset, $count)
    {
        return $this->get_video_conferencing_connector()->retrieve_video_conferencing_objects($condition, $order_property, $offset, $count);
    }

    /**
     * @param ExternalRepositoryManager $video_conferencing_manager
     */
    function initialize_video_conferencing(VideoConferencingManager $video_conferencing_manager)
    {
        $this->get_video_conferencing_connector();
    }

    /**
     * @return boolean
     */
    abstract function validate_settings($video_conferencing);

    /**
     * @return string
     */
    function support_sorting_direction()
    {
        return true;
    }

    /**
     * @param mixed $query
     */
    function translate_search_query($query)
    {
        return $this->get_video_conferencing_connector()->translate_search_query($query);
    }

    /**
     * @return array
     */
    abstract function get_menu_items();

    /**
     * @param VideoConferencingObject $object
     * @return string
     */
    //abstract function get_video_conferencing_object_viewing_url(VideoConferencingObject $object);

    /**
     * @param string $id
     * @return VideoConferencingObject
     */
    function retrieve_video_conferencing_object($id)
    {
        return $this->get_video_conferencing_connector()->retrieve_video_conferencing_object($id);
    }

    /**
     * @param string $id
     * @return boolean
     */
    function delete_video_conferencing_object($id)
    {
        return $this->get_video_conferencing_connector()->delete_video_conferencing_object($id);
    }

    //    /**
    //     * @param string $id
    //     * @return boolean
    //     */
    //    function export_external_repository_object($id)
    //    {
    //        return $this->get_external_repository_connector()->export_external_repository_object($id);
    //    }


    /**
     * @param VideoConferencingObject $object
     * @return array
     */
    function get_video_conferencing_object_actions(ExternalRepositoryObject $object)
    {
        $toolbar_items = array();

       
        return $toolbar_items;
    }

    /**
     * @return string
     */
    function get_renderer()
    {
        $renderer = Request :: get(self :: PARAM_RENDERER);

        if ($renderer && in_array($renderer, $this->get_available_renderers()))
        {
            return $renderer;
        }
        else
        {
            $renderers = $this->get_available_renderers();
            return $renderers[0];
        }
    }

    /**
     * @return array
     */
    function get_available_renderers()
    {
        return array(VideoConferencingObjectRenderer :: TYPE_TABLE);
    }

    /**
     * @return Condition
     */
    //abstract function get_content_object_type_conditions();

    /**
     * @param string $type
     * @return boolean
     */
    static public function exists($type)
    {
        $path = Path :: get_common_extensions_path() . 'video_conferencing_manager/implementation';
        $video_conferencing_path = $path . '/' . $type;
        $video_conferencing_manager_path = $video_conferencing_path . '/php/' . $type . '_video_conferencing_manager.class.php';

        if (file_exists($video_conferencing_path) && is_dir($video_conferencing_path) && file_exists($video_conferencing_manager_path))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    function create_component($type, $application = null)
    {
        if ($application == null)
        {
            $application = $this;
        }

        $manager_class = get_class($application);
        $application_component_path = $application->get_application_component_path();

        $file = $application_component_path . Utilities :: camelcase_to_underscores($type) . '.class.php';

        if (! file_exists($file) || ! is_file($file))
        {
            $message = array();
            $message[] = Translation :: get('ComponentFailedToLoad') . '<br /><br />';
            $message[] = '<b>' . Translation :: get('File') . ':</b><br />';
            $message[] = $file . '<br /><br />';
            $message[] = '<b>' . Translation :: get('Stacktrace') . ':</b>';
            $message[] = '<ul>';
            $message[] = '<li>' . Translation :: get($manager_class) . '</li>';
            $message[] = '<li>' . Translation :: get($type) . '</li>';
            $message[] = '</ul>';

            $application_name = Application :: application_to_class($this->get_application_name());

            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb('#', Translation :: get($application_name)));

            Display :: header($trail);
            Display :: error_message(implode("\n", $message));
            Display :: footer();
            exit();
        }

        $class = $manager_class . $type . 'Component';
        require_once $file;

        return new $class($application->get_video_conferencing(), $application->get_parent());
    }
    
	static function get_namespace($type = null)
    {
        if ($type)
        {
            return __NAMESPACE__ . '\implementation\\' . $type;
        }
        else
        {
            return __NAMESPACE__;
        }
    }
    
	static function get_registered_types($status = Registration :: STATUS_ACTIVE)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_VIDEO_CONFERENCING_MANAGER);
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_STATUS, $status);

        return AdminDataManager :: get_instance()->retrieve_registrations(new AndCondition($conditions));
    }
}
?>