<?php
abstract class VideoConferencingyManager extends SubManager
{
    const PARAM_VIDEO_CONFERENCING_MANAGER_ACTION = 'conferencing_action';
    
    const ACTION_VIEW_EXTERNAL_REPOSITORY = 'viewer';
    const ACTION_EXPORT_EXTERNAL_REPOSITORY = 'exporter';
    const ACTION_IMPORT_EXTERNAL_REPOSITORY = 'importer';
    const ACTION_BROWSE_VIDEO_CONFERENCING = 'browser';
//    const ACTION_DOWNLOAD_EXTERNAL_REPOSITORY = 'downloader';
//    const ACTION_UPLOAD_EXTERNAL_REPOSITORY = 'uploader';
//    const ACTION_SELECT_EXTERNAL_REPOSITORY = 'selecter';
    const ACTION_EDIT_VIDEO_CONFERENCING = 'editor';
    const ACTION_DELETE_VIDEO_CONFERENCING = 'deleter';
    const ACTION_CONFIGURE_VIDEO_CONFERENCING = 'configurer';
//    const ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY = 'external_syncer';
//    const ACTION_SYNCHRONIZE_INTERNAL_REPOSITORY = 'internal_syncer';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE_VIDEO_CONFERENCING;
    
    const PARAM_VIDEO_CONFERENCING_ID = 'video_conferencing_id';
    const PARAM_VIDEO_CONFERENCING = 'video_conferencing';
    const PARAM_QUERY = 'query';
    const PARAM_RENDERER = 'renderer';
    const PARAM_FOLDER = 'folder';
//    const PARAM_USER_QUOTUM = 'default_user_quotum';
    
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
        
        if ($this->validate_settings())
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
        return is_a($this->get_parent(), LauncherApplication :: CLASS_NAME);
    }

    /**
     * @param ExternalRepository $external_repository
     * @param Application $application
     */
    static function launch($application)
    {
        $type = $application->get_video_conferencing()->get_type();
        
        $file = dirname(__FILE__) . '/type/' . $type . '/' . $type . '_video_conferencing_manager.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('VideoConferencingManagerTypeDoesNotExist', array('type' => $type)));
        }
        
        require_once $file;
        
        $class = Utilities :: underscores_to_camelcase($type) . 'VideoConferencingManager';
        
        $settings_validated = call_user_func(array($class, 'validate_settings'));
        
        if (! $settings_validated)
        {
            if ($application->get_user()->is_platform_admin())
            {
                Request :: set_get(Application :: PARAM_ERROR_MESSAGE, Translation :: get('PleaseReviewSettings'));
                Request :: set_get(self :: PARAM_EXTERNAL_VIDEO_CONFERENCING_ACTION, self :: ACTION_CONFIGURE_VIDEO_CONFERENCING);
            }
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
        
        if ($action == self :: ACTION_EDIT_VIDEO_CONFERENCING)
        {
            $video_conferencing_actions[] = self :: ACTION_EDIT_VIDEO_CONFERENCING;
        }
        
        if ($action == self :: ACTION_VIEW_VIDEO_CONFERENCING)
        {
            $video_conferencing_actions[] = self :: ACTION_VIEW_VIDEO_CONFERENCING;
        }
        
        $tabs = new DynamicVisualTabsRenderer(Utilities :: camelcase_to_underscores(get_class($this)));
        
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
            
            if ($video_conferencing_action == self :: ACTION_VIEW_VIDEO_CONFERENCING)
            {
                $parameters[self :: PARAM_VIDEO_CONFERENCING_ID] = Request :: get(self :: PARAM_VIDEO_CONFERENCING_ID);
            }
            
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
        $actions[] = self :: ACTION_BROWSE_VIDEO_CONFERENCING;
        //$actions[] = self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY;
        
        $is_platform = $this->get_user()->is_platform_admin() && (count($this->get_settings()) > 0);
        
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
    abstract function validate_settings();

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
    abstract function get_video_conferencing_object_viewing_url(VideoConferencingObject $object);

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
        
        if ($object->is_editable())
        {
            $toolbar_items[self :: ACTION_EDIT_EXTERNAL_REPOSITORY] = new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_url(array(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => self :: ACTION_EDIT_EXTERNAL_REPOSITORY, self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
        }
        
        if ($object->is_deletable())
        {
            $toolbar_items[self :: ACTION_DELETE_EXTERNAL_REPOSITORY] = new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_url(array(self :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION => self :: ACTION_DELETE_VIDEO_CONFERENCING, self :: PARAM_VIDEO_CONFERENCING_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
        }
        if ($object->is_usable())
        {

//        	if ($this->is_stand_alone())
//            {
//                $toolbar_items[] = new ToolbarItem(Translation :: get('Select'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(self :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION => self :: ACTION_SELECT_VIDEO_CONFERENCING, self :: PARAM_VIDEO_CONFERENCING_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
//            }
//            else
//            {
                if ($object->is_importable())
                {
                	$toolbar_items[self :: ACTION_IMPORT_VIDEO_CONFERENCING] = new ToolbarItem(Translation :: get('Import'), Theme :: get_common_image_path() . 'action_import.png', $this->get_url(array(self :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION => self :: ACTION_IMPORT_VIDEO_CONFERENCING, self :: PARAM_VIDEO_CONFERENCING_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
//                }
//                else
//                {
//                    switch ($object->get_synchronization_status())
//                    {
//                        case ExternalRepositorySync :: SYNC_STATUS_INTERNAL :
//                            $toolbar_items[self :: ACTION_SYNCHRONIZE_INTERNAL_REPOSITORY] = new ToolbarItem(Translation :: get('UpdateContentObject'), Theme :: get_common_image_path() . 'action_synchronize.png', $this->get_url(array(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => self :: ACTION_SYNCHRONIZE_INTERNAL_REPOSITORY, self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
//                            break;
//                        case ExternalRepositorySync :: SYNC_STATUS_EXTERNAL :
//                            if ($object->is_editable())
//                            {
//                                $toolbar_items[self :: ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY] = new ToolbarItem(Translation :: get('UpdateExternalRepositoryObject'), Theme :: get_common_image_path() . 'external_repository/' . $object->get_object_type() . '/logo/16.png', $this->get_url(array(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => self :: ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY, self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
//                            }
//                            break;
//                        case ExternalRepositorySync :: SYNC_STATUS_CONFLICT :
//                            $toolbar_items[self :: ACTION_SYNCHRONIZE_INTERNAL_REPOSITORY] = new ToolbarItem(Translation :: get('UpdateContentObject'), Theme :: get_common_image_path() . 'action_synchronize.png', $this->get_url(array(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => self :: ACTION_SYNCHRONIZE_INTERNAL_REPOSITORY, self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
//                            if ($object->is_editable())
//                            {
//                                $toolbar_items[self :: ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY] = new ToolbarItem(Translation :: get('UpdateExternalRepositoryObject'), Theme :: get_common_image_path() . 'external_repository/' . $object->get_object_type() . '/logo/16.png', $this->get_url(array(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => self :: ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY, self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
//                            }
//                            break;
//                    }
                }
//            }
        }
        
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
    abstract function get_content_object_type_conditions();

    /**
     * @param string $type
     * @return boolean
     */
    static public function exists($type)
    {
        $path = dirname(__FILE__) . '/type';
        $video_conferencing_path = $path . '/' . $type;
        $video_conferencing_manager_path = $video_conferencing_path . '/' . $type . '_video_conferencing_manager.class.php';
        
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
}
?>