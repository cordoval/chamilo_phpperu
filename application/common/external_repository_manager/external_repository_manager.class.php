<?php
abstract class ExternalRepositoryManager extends SubManager
{
    const PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION = 'external_repository_action';

    const ACTION_VIEW_EXTERNAL_REPOSITORY = 'view';
    const ACTION_EXPORT_EXTERNAL_REPOSITORY = 'export';
    const ACTION_IMPORT_EXTERNAL_REPOSITORY = 'import';
    const ACTION_BROWSE_EXTERNAL_REPOSITORY = 'browse';
    const ACTION_DOWNLOAD_EXTERNAL_REPOSITORY = 'download';
    const ACTION_UPLOAD_EXTERNAL_REPOSITORY = 'upload';
    const ACTION_SELECT_EXTERNAL_REPOSITORY = 'select';
    const ACTION_EDIT_EXTERNAL_REPOSITORY = 'edit';
    const ACTION_DELETE_EXTERNAL_REPOSITORY = 'delete';

    const PARAM_EXTERNAL_REPOSITORY_ID = 'external_repository_id';
    const PARAM_TYPE = 'type';
    const PARAM_QUERY = 'query';
    const PARAM_RENDERER = 'renderer';

    const CLASS_NAME = __CLASS__;

    function ExternalRepositoryManager($application)
    {
        parent :: __construct($application);

        $external_repository_manager_action = Request :: get(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION);
        if ($external_repository_manager_action)
        {
            $this->set_parameter(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION, $external_repository_manager_action);
        }

        $this->set_optional_parameters();
        $this->initiliaze_external_repository();
    }

    function set_optional_parameters()
    {
        $this->set_parameter(self :: PARAM_RENDERER, $this->get_renderer());
    }

    function is_stand_alone()
    {
        return is_a($this->get_parent(), LauncherApplication :: CLASS_NAME);
    }

    abstract function is_editable($id);

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
        return array(self :: ACTION_BROWSE_EXTERNAL_REPOSITORY, self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY);
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

    abstract function initiliaze_external_repository();

    function get_external_repository_browser_gallery_table_property_model()
    {
        return null;
    }

    function get_external_repository_browser_gallery_table_cell_renderer()
    {
        return null;
    }

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

    static function retrieve_external_repository_manager()
    {
        $manager = array();
//        $manager[] = 'fedora';
        $manager[] = 'flickr';
        $manager[] = 'google_docs';
//        $manager[] = 'matterhorn';
        $manager[] = 'mediamosa';
        $manager[] = 'youtube';

        return new ArrayResultSet($manager);
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
}
?>