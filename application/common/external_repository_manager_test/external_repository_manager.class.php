<?php
abstract class ExternalRepositoryManager extends SubManager
{
    const CLASS_NAME = __CLASS__;
    
    const PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION = 'external_repository_action';
    
    const ACTION_VIEW = 'view';
    const ACTION_EXPORT = 'export';
    const ACTION_IMPORT = 'import';
    const ACTION_BROWSE = 'browse';
    const ACTION_DOWNLOAD = 'download';
    const ACTION_UPLOAD = 'upload';
    const ACTION_SELECT = 'select';
    const ACTION_EDIT = 'edit';
    const ACTION_DELETE = 'delete';
    
    const PARAM_EXTERNAL_REPOSITORY_ID = 'external_repository_id';
    const PARAM_QUERY = 'query';
    const PARAM_TYPE = 'type';

    function ExternalRepositoryManager($application)
    {
        parent :: __construct($application);
        
        $external_repository_manager_action = Request :: get(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION);
        if ($external_repository_manager_action)
        {
            $this->set_parameter(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION, $external_repository_manager_action);
        }
    }

    function is_stand_alone()
    {
        return is_a($this->get_parent(), LauncherApplication :: CLASS_NAME);
    }

    static function factory($type, $application)
    {
        $file = dirname(__FILE__) . '/type/' . $type . '/' . $type . '_external_repository_manager.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ExternalRepositoryManagerTypeDoesNotExist', array('type' => $type)));
        }
        
        require_once $file;
        
        $class = Utilities :: underscores_to_camelcase($type) . 'ExternalRepositoryManager';
        return new $class($application);
    }

    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'external_repository_manager/component/';
    }

    function get_action()
    {
        return $this->get_parameter(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION);
    }

    function display_header()
    {
        $action = $this->get_action();
        parent :: display_header();
        
        $html = array();
        $external_repository_actions = $this->get_external_repository_actions();
        
        if ($action == self :: ACTION_EDIT)
        {
            $external_repository_actions[] = self :: ACTION_EDIT;
        }
        
        if ($action == self :: ACTION_VIEW)
        {
            $external_repository_actions[] = self :: ACTION_VIEW;
        }
        
        $tabs = new DynamicVisualTabsRenderer(Utilities :: camelcase_to_underscores(get_class($this)));
        
        foreach ($external_repository_actions as $external_repository_action)
        {
            if ($action == $external_repository_action)
            {
                $selected = true;
            }
            else
            {
                $selected = false;
            }
            
            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = $external_repository_action;
            
            if ($external_repository_action == self :: ACTION_VIEW)
            {
                $parameters[self :: PARAM_EXTERNAL_REPOSITORY_ID] = Request :: get(self :: PARAM_EXTERNAL_REPOSITORY_ID);
            }
            
            $label = htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($external_repository_action) . 'Title'));
            $link = $this->get_url($parameters, true);
            
            $tabs->add_tab(new DynamicVisualTab($external_repository_action, $label, Theme :: get_common_image_path() . 'place_tab_' . $external_repository_action . '.png', $link, $selected));
        }
        
        $html[] = $tabs->header();
        $html[] = DynamicVisualTabsRenderer :: body_header();
        
        echo implode("\n", $html);
    }

    function get_external_repository_actions()
    {
        return array(self :: ACTION_BROWSE, self :: ACTION_UPLOAD);
    }

    function display_footer()
    {
        $html = array();
        $html[] = DynamicVisualTabsRenderer :: body_footer();
        $html[] = DynamicVisualTabsRenderer :: footer();
        echo implode("\n", $html);
        
        parent :: display_footer();
    }

    abstract function count_external_repository_objects($condition);

    abstract function retrieve_external_repository_objects($condition, $order_property, $offset, $count);

    function get_property_model()
    {
        return null;
    }

    function support_sorting_direction()
    {
        return true;
    }

    abstract function translate_search_query($query);

    abstract function get_menu_items();

    static function retrieve_managers()
    {
        $manager = array();
        //$manager[] = Youtube :: get_type_name();
        //$manager[] = 'mediamosa';
        $manager[] = 'fedora';
        $manager[] = 'flickr';
        $manager[] = 'google_docs';
        $manager[] = 'matterhorn';
        $manager[] = 'mediamosa';
        $manager[] = 'youtube';
        return new ArrayResultSet($manager);
    
    }
}
?>