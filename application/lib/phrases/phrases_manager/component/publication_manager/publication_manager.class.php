<?php
/**
 * $Id: publication_manager.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager
 * @author Hans De Bisschop
 */
//require_once dirname(__FILE__) . '/component/registration_browser/registration_browser_table.class.php';

class PhrasesPublicationManager extends SubManager
{
    const PARAM_PUBLICATION_MANAGER_ACTION = 'action';
    const PARAM_PHRASES_PUBLICATION_ID = 'phrase_publication';

    const ACTION_BROWSE = 'browse';
    const ACTION_PUBLISH = 'publish';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_VIEW = 'view';
    const ACTION_BUILD = 'build';

    function PhrasesPublicationManager($phrases_manager)
    {
        parent :: __construct($phrases_manager);

        $publication_action = Request :: get(self :: PARAM_PUBLICATION_MANAGER_ACTION);
        if ($publication_action)
        {
            $this->set_action($publication_action);
        }

//        $this->parse_input_from_table();
    }

    function run()
    {
        $package_action = $this->get_action();

        switch ($package_action)
        {
            case self :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_PUBLISH :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_DELETE :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_UPDATE :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_BUILD :
                $component = $this->create_component('Builder');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }

        $component->run();
    }

    function set_action($action)
    {
        $this->set_parameter(self :: PARAM_PUBLICATION_MANAGER_ACTION, $action);
    }

    function get_action()
    {
        return $this->get_parameter(self :: PARAM_PUBLICATION_MANAGER_ACTION);
    }

//    function parse_input_from_table()
//    {
//        if (isset($_POST['action']))
//        {
//            $selected_ids = Request :: post(RegistrationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX);
//
//            if (empty($selected_ids))
//            {
//                $selected_ids = array();
//            }
//            elseif (! is_array($selected_ids))
//            {
//                $selected_ids = array($selected_ids);
//            }
//            switch ($_POST['action'])
//            {
//                case self :: PARAM_ACTIVATE_SELECTED :
//                    $this->set_action(self :: ACTION_ACTIVATE_PACKAGE);
//                    Request :: set_get(self :: PARAM_REGISTRATION, $selected_ids);
//                    break;
//                case self :: ACTION_DEACTIVATE_PACKAGE :
//                    $this->set_action(self :: ACTION_DEACTIVATE_PACKAGE);
//                    Request :: set_get(self :: PARAM_REGISTRATION, $selected_ids);
//                    break;
//            }
//
//        }
//    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function retrieve_phrases_publications($condition = null, $offset = 0, $max_objects = -1, $order_by = array ())
    {
        return $this->get_parent()->retrieve_phrases_publications($condition, $order_by, $offset, $max_objects);
    }

    function retrieve_phrases_publication($id)
    {
        return $this->get_parent()->retrieve_phrases_publication($id);
    }

    function count_phrases_publications($condition = null)
    {
        return $this->get_parent()->count_phrases_publications($condition);
    }

    function get_delete_phrases_publication_url($phrases_publication)
    {
        return $this->get_parent()->get_url(array(self :: PARAM_PUBLICATION_MANAGER_ACTION => self :: ACTION_DELETE, self :: PARAM_PHRASES_PUBLICATION_ID => $phrases_publication->get_id()));
    }

    function get_update_phrases_publication_url($phrases_publication)
    {
        return $this->get_parent()->get_url(array(self :: PARAM_PUBLICATION_MANAGER_ACTION => self :: ACTION_UPDATE, self :: PARAM_PHRASES_PUBLICATION_ID => $phrases_publication->get_id()));
    }

    function get_build_phrases_publication_url($phrases_publication)
    {
        return $this->get_parent()->get_url(array(self :: PARAM_PUBLICATION_MANAGER_ACTION => self :: ACTION_BUILD, self :: PARAM_PHRASES_PUBLICATION_ID => $phrases_publication->get_id()));
    }
}
?>