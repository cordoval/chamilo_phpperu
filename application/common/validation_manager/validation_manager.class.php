<?php
/**
 * $Id: validation_manager.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.validation_manager
 */

/**
 * Description of validation_manager
 *
 * @author Pieter Hens
 */

require_once dirname(__FILE__) . '/validation_manager_component.class.php';
require_once dirname(__FILE__) . '/validation_form.class.php';

class ValidationManager
{
    
    const PARAM_ACTION = 'validation_action';
    const PARAM_VALIDATION_ID = 'validation_id';
    //const PARAM_REMOVE_VALIDATION = 'remove_validation';
    const ACTION_BROWSE_VALIDATION = 'browse_validation';
    const ACTION_CREATE_VALIDATION = 'create_validation';
    //const ACTION_UPDATE_VALIDATION = 'update_validation';
    const ACTION_DELETE_VALIDATION = 'delete_validation';
    
    private $parent;
    
    private $parameters;
    
    private $application;

    function ValidationManager($parent, $application)
    {
        $this->parent = $parent;
        $this->application = $application;
        //$parent->set_parameter(self :: PARAM_ACTION, $this->get_action());
    //$this->parse_input_from_table();
    }

    function run()
    {
        
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_BROWSE_VALIDATION :
                $component = ValidationManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_CREATE_VALIDATION :
                
                $component = ValidationManagerComponent :: factory('Creator', $this);
                break;
            /*case self :: ACTION_UPDATE_VALIDATION :
                $component = ValidationManagerComponent :: factory('Updater', $this);
                break;*/
            case self :: ACTION_DELETE_VALIDATION :
                $component = ValidationManagerComponent :: factory('Deleter', $this);
                break;
            default :
                $component = ValidationManagerComponent :: factory('Browser', $this);
        }
        $component->run();
    
    }

    /**
     * Returns the tool which created this publisher.
     * @return Tool The tool.
     */
    function get_parent()
    {
        return $this->parent;
    }

    function display_header($breadcrumbtrail)
    {
        return $this->parent->display_header($breadcrumbtrail, false, false);
    }

    function display_footer()
    {
        return $this->parent->display_footer();
    }

    /**
     * @see Tool::get_user_id()
     */
    function get_user_id()
    {
        return $this->parent->get_user_id();
    }

    function get_user()
    {
        return $this->parent->get_user();
    }

    function get_application()
    {
        return $this->application;
    }

    /**
     * Returns the action that the user selected.
     * @return string The action.
     */
    function get_action()
    {
        return $_GET[self :: PARAM_ACTION];
    }

    function get_url($parameters = array(), $encode = false)
    {
        return $this->parent->get_url($parameters, $encode);
    }

    function get_parameters()
    {
        return $this->parent->get_parameters();
    }

    function set_parameter($name, $value)
    {
        $this->parent->set_parameter($name, $value);
    }

    /**
     * Sets a default learning object. When the creator component of this
     * publisher is displayed, the properties of the given learning object will
     * be used as the default form values.
     * @param string $type The learning object type.
     * @param ContentObject $content_object The learning object to use as the
     *                                        default for the given type.
     */
    function set_default_content_object($type, $content_object)
    {
        $this->default_content_objects[$type] = $content_object;
    }

    function get_default_content_object($type)
    {
        if (isset($this->default_content_objects[$type]))
        {
            return $this->default_content_objects[$type];
        }
        return new AbstractContentObject($type, $this->get_user_id());
    }

    function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
    {
        return $this->parent->redirect($action, $message, $error_message, $extra_params);
    }

    function repository_redirect($action = null, $message = null, $cat_id = 0, $error_message = false, $extra_params = array())
    {
        return $this->parent->redirect($action, $message, $cat_id, $error_message, $extra_params);
    }

    function get_extra_parameters()
    {
        return $this->parameters;
    }

    function set_extra_parameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /*  function retrieve_validations($pid,$cid,$application)
        {
            $adm = AdminDataManager :: get_instance();
            return $adm->retrieve_validations($pid,$cid,$application);

        }*/
    
    function retrieve_validation($id)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->retrieve_validation($id);
    }

    function get_validate_button($pid, $cid, $application, $user_id, $action)
    {
        
        $adm = AdminDataManager :: get_instance();
        $conditions = array();
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_PID, $pid);
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_CID, $cid);
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_OWNER, $this->get_user_id());
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_APPLICATION, PortfolioManager :: APPLICATION_NAME);
        $condition = new AndCondition($conditions);
        $aantalval = $adm->count_validations($condition);
        $create_url = $this->get_url(array(ValidationManager :: PARAM_ACTION => ValidationManager :: ACTION_CREATE_VALIDATION, 'pid' => $pid, 'cid' => $cid, 'user_id' => $user_id, 'action' => 'validation'));
        $create_button = $aantalval != 0 ? Theme :: get_common_image_path() . 'buttons/button_confirm.png"' : Theme :: get_common_image_path() . 'action_create.png"';
        $create_link = '<a href="' . $create_url . '"onclick="if (' . $aantalval . '!=0 ) return confirm(\'' . addslashes(htmlentities(Translation :: get('ValidationExists'))) . '\');"><img src="' . $create_button . '  alt=""/></a>';
        return $create_link;
    }

    function retrieve_validations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->retrieve_validations($condition, $order_by, $offset, $max_objects);
    
    }

    function count_validations($condition = null)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->count_validations($condition);
    }

    function get_publication_deleting_url($validation)
    {
        return $this->get_url(array(ValidationManager :: PARAM_ACTION => ValidationManager :: ACTION_DELETE_VALIDATION, 'pid' => $validation->get_pid(), 'cid' => $validation->get_cid(), 'user_id' => Request :: get('user_id'), 'action' => 'validation', 'deleteitem' => $validation->get_id()));
    }

}
?>