<?php
/**
 * $Id: mastery_level_manager.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager
 * @author Hans De Bisschop
 */
//require_once dirname(__FILE__) . '/component/registration_browser/registration_browser_table.class.php';

class PhrasesMasteryLevelManager extends SubManager
{
    const PARAM_MASTERY_LEVEL_MANAGER_ACTION = 'action';
    const PARAM_PHRASES_MASTERY_LEVEL_ID = 'level';
    const PARAM_MOVE = 'move';

    const ACTION_BROWSE = 'browse';
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_VIEW = 'view';
    const ACTION_MOVE = 'move';
    const ACTION_MOVE_UP = 'move_up';
    const ACTION_MOVE_DOWN = 'move_down';

    function PhrasesMasteryLevelManager($phrases_manager)
    {
        parent :: __construct($phrases_manager);

        $publication_action = Request :: get(self :: PARAM_MASTERY_LEVEL_MANAGER_ACTION);
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
            case self :: ACTION_CREATE :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_DELETE :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_UPDATE :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_MOVE_UP :
                Request :: set_get(self :: PARAM_MOVE, -1);
                $component = $this->create_component('Mover');
                break;
            case self :: ACTION_MOVE_DOWN :
                Request :: set_get(self :: PARAM_MOVE, 1);
                $component = $this->create_component('Mover');
                break;
            case self :: ACTION_MOVE :
                $component = $this->create_component('Mover');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }

        $component->run();
    }

    function set_action($action)
    {
        $this->set_parameter(self :: PARAM_MASTERY_LEVEL_MANAGER_ACTION, $action);
    }

    function get_action()
    {
        return $this->get_parameter(self :: PARAM_MASTERY_LEVEL_MANAGER_ACTION);
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

    function retrieve_phrases_mastery_levels($condition = null, $offset = 0, $max_objects = -1, $order_by = array ())
    {
        return $this->get_parent()->retrieve_phrases_mastery_levels($condition, $order_by, $offset, $max_objects);
    }

    function retrieve_phrases_mastery_level($id)
    {
        return $this->get_parent()->retrieve_phrases_mastery_level($id);
    }

    function count_phrases_mastery_levels($condition = null)
    {
        return $this->get_parent()->count_phrases_mastery_levels($condition);
    }

    function get_delete_phrases_mastery_level_url($mastery_level)
    {
        return $this->get_parent()->get_url(array(self :: PARAM_MASTERY_LEVEL_MANAGER_ACTION => self :: ACTION_DELETE, self :: PARAM_PHRASES_MASTERY_LEVEL_ID => $mastery_level->get_id()));
    }

    function get_update_phrases_mastery_level_url($mastery_level)
    {
        return $this->get_parent()->get_url(array(self :: PARAM_MASTERY_LEVEL_MANAGER_ACTION => self :: ACTION_UPDATE, self :: PARAM_PHRASES_MASTERY_LEVEL_ID => $mastery_level->get_id()));
    }
}
?>