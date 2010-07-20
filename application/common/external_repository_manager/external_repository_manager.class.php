<?php
abstract class ExternalRepositoryManager extends SubManager
{
    const PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION = 'repository_action';

    const ACTION_VIEW_EXTERNAL_REPOSITORY = 'view';
    const ACTION_EXPORT_EXTERNAL_REPOSITORY = 'export';
    const ACTION_IMPORT_EXTERNAL_REPOSITORY = 'import';
    const ACTION_BROWSE_EXTERNAL_REPOSITORY = 'browse';
    const ACTION_DOWNLOAD_EXTERNAL_REPOSITORY = 'download';
    const ACTION_UPLOAD_EXTERNAL_REPOSITORY = 'upload';
    const ACTION_SELECT_EXTERNAL_REPOSITORY = 'select';
    const ACTION_EDIT_EXTERNAL_REPOSITORY = 'edit';
    const ACTION_DELETE_EXTERNAL_REPOSITORY = 'delete';
    const ACTION_CONFIGURE_EXTERNAL_REPOSITORY = 'configure';
    const ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY = 'synchronize';

    const PARAM_EXTERNAL_REPOSITORY_ID = 'external_repository_id';
    const PARAM_EXTERNAL_REPOSITORY = 'external_repository';
    const PARAM_QUERY = 'query';
    const PARAM_RENDERER = 'renderer';

    const CLASS_NAME = __CLASS__;

    private $settings;
    private $user_settings;

    function ExternalRepositoryManager($application)
    {
        parent :: __construct($application);

        $external_repository_manager_action = Request :: get(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION);
        if ($external_repository_manager_action)
        {
            $this->set_parameter(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION, $external_repository_manager_action);
        }

        $this->set_optional_parameters();

        if (!$this->validate_settings())
        {
            if ($this->get_user()->is_platform_admin())
            {
                Request :: set_get(Application :: PARAM_ERROR_MESSAGE, Translation :: get('PleaseReviewSettings'));
                $this->set_parameter(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION, self :: ACTION_CONFIGURE_EXTERNAL_REPOSITORY);
            }
            else
            {
                parent :: display_header();
                $this->display_warning_message('TemporarilyUnavailable');
                parent :: display_footer();
                exit;
            }
        }
        else
        {
            $this->initialize_external_repository($this);
        }
    }

    function set_optional_parameters()
    {
        $this->set_parameter(self :: PARAM_RENDERER, $this->get_renderer());
    }

    function is_stand_alone()
    {
        return is_a($this->get_parent(), LauncherApplication :: CLASS_NAME);
    }

    function load_settings()
    {
        $this->settings = array();

        $condition = new EqualityCondition(ExternalRepositorySetting :: PROPERTY_EXTERNAL_REPOSITORY_ID, $this->get_parameter(self :: PARAM_EXTERNAL_REPOSITORY));
        $settings = RepositoryDataManager :: get_instance()->retrieve_external_repository_settings($condition);

        while ($setting = $settings->next_result())
        {
            $this->settings[$setting->get_variable()] = $setting->get_value();
        }
    }

    function load_user_settings()
    {
        $this->user_settings = array();

        $condition = new EqualityCondition(ExternalRepositorySetting :: PROPERTY_EXTERNAL_REPOSITORY_ID, $this->get_parameter(self :: PARAM_EXTERNAL_REPOSITORY));
        $settings = RepositoryDataManager :: get_instance()->retrieve_external_repository_settings($condition);

        $setting_ids = array();
        while ($setting = $settings->next_result())
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(ExternalRepositoryUserSetting :: PROPERTY_USER_ID, $this->get_user_id());
            $conditions[] = new EqualityCondition(ExternalRepositoryUserSetting :: PROPERTY_SETTING_ID, $setting->get_id());
            $condition = new AndCondition($conditions);

            $user_settings = RepositoryDataManager :: get_instance()->retrieve_external_repository_user_settings($condition, array(), 0, 1);
            if ($user_settings->size() == 1)
            {
                $user_setting = $user_settings->next_result();
                $this->user_settings[$setting->get_variable()] = $user_setting->get_value();
            }
        }
    }

    function get_setting($variable)
    {
        if (! isset($this->settings))
        {
            $this->load_settings();
        }

        return $this->settings[$variable];
    }

    function get_user_setting($variable)
    {
        if (! isset($this->user_settings))
        {
            $this->load_user_settings();
        }

        return $this->user_settings[$variable];
    }

    function get_settings()
    {
        if (! isset($this->settings))
        {
            $this->load_settings();
        }

        return $this->settings;
    }

    function get_user_settings()
    {
        if (! isset($this->user_settings))
        {
            $this->load_user_settings();
        }

        return $this->user_settings;
    }

    static function factory($external_repository, $application)
    {
        $type = $external_repository->get_type();

        $file = dirname(__FILE__) . '/type/' . $type . '/' . $type . '_external_repository_manager.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ExternalRepositoryManagerTypeDoesNotExist', array('type' => $type)));
        }

        require_once $file;

        $class = Utilities :: underscores_to_camelcase($type) . 'ExternalRepositoryManager';
        return new $class($application);
    }

    function is_ready_to_be_used()
    {
        //        $action = $this->get_parameter(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION);
        //
        //        return self :: any_object_selected() && ($action == self :: ACTION_PUBLISHER);
        return false;
    }

    function any_object_selected()
    {
        //$object = Request :: get(self :: PARAM_ID);
    //return isset($object);
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

        if ($action == self :: ACTION_EDIT_EXTERNAL_REPOSITORY)
        {
            $external_repository_actions[] = self :: ACTION_EDIT_EXTERNAL_REPOSITORY;
        }

        if ($action == self :: ACTION_VIEW_EXTERNAL_REPOSITORY)
        {
            $external_repository_actions[] = self :: ACTION_VIEW_EXTERNAL_REPOSITORY;
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

            if ($external_repository_action == self :: ACTION_VIEW_EXTERNAL_REPOSITORY)
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
        $actions = array();
        $actions[] = self :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
        $actions[] = self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY;

        $is_platform = $this->get_user()->is_platform_admin() && (count($this->get_settings()) > 0);

        if ($is_platform)
        {
            $actions[] = self :: ACTION_CONFIGURE_EXTERNAL_REPOSITORY;
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

    abstract function count_external_repository_objects($condition);

    abstract function retrieve_external_repository_objects($condition, $order_property, $offset, $count);

    abstract function initialize_external_repository(ExternalRepositoryManager $external_repository_manager);

    abstract function validate_settings();

    function support_sorting_direction()
    {
        return true;
    }

    abstract function translate_search_query($query);

    abstract function get_menu_items();

    abstract function get_external_repository_object_viewing_url($object);

    abstract function retrieve_external_repository_object($id);

    abstract function delete_external_repository_object($id);

    abstract function export_external_repository_object($id);

    /**
     * @param ExternalRepositoryObject $object
     */
    function get_external_repository_object_actions(ExternalRepositoryObject $object)
    {
        $toolbar_items = array();

        if ($object->is_editable())
        {
            $toolbar_items[] = new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_url(array(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => self :: ACTION_EDIT_EXTERNAL_REPOSITORY, self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
        }

        if ($object->is_deletable())
        {
            $toolbar_items[] = new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_url(array(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => self :: ACTION_DELETE_EXTERNAL_REPOSITORY, self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
        }

        if ($object->is_usable())
        {
            if ($this->is_stand_alone())
            {
                $toolbar_items[] = new ToolbarItem(Translation :: get('Select'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => self :: ACTION_SELECT_EXTERNAL_REPOSITORY, self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
            }
            else
            {
                if ($object->is_importable())
                {
                    $toolbar_items[] = new ToolbarItem(Translation :: get('Import'), Theme :: get_common_image_path() . 'action_import.png', $this->get_url(array(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => self :: ACTION_IMPORT_EXTERNAL_REPOSITORY, self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
                }
                else
                {
                    $toolbar_items[] = new ToolbarItem(Translation :: get('Synchronize'), Theme :: get_common_image_path() . 'action_synchronize.png', $this->get_url(array(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => self :: ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY, self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id())), ToolbarItem :: DISPLAY_ICON);
                }
            }
        }

        return $toolbar_items;
    }

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

    function get_available_renderers()
    {
        return array(ExternalRepositoryObjectRenderer :: TYPE_TABLE);
    }

    abstract function get_content_object_type_conditions();

    static public function exists($type)
    {
        $path = dirname(__FILE__) . '/type';
        $external_repository_path = $path . '/' . $type;
        $external_repository_manager_path = $external_repository_path . '/' . $type . '_external_repository_manager.class.php';

        if (file_exists($external_repository_path) && is_dir($external_repository_path) && file_exists($external_repository_manager_path))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>