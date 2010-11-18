<?php
namespace common\libraries;
/**
 * $Id: object_table_form_action.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.table.object_table
 */
class ObjectTableFormActions
{
    /**
     * @var string
     */
    private $action;
    /**
     * @var array
     */
    private $form_actions;
    /**
     * @var string
     */
    private $namespace;

    /**
     * @param string $action
     * @param array $form_actions
     * @param string $namespace
     */
    function ObjectTableFormActions($namespace, $action = Application :: PARAM_ACTION, $form_actions = array())
    {
        $this->action = $action;
        $this->form_actions = $form_actions;
        $this->namespace = $namespace;
    }

    /**
     * @return the $action
     */
    public function get_action()
    {
        return $this->action;
    }

    /**
     * @param $action the $action to set
     */
    public function set_action($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function get_namespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function set_namespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return the $form_actions
     */
    public function get_form_actions()
    {
        return $this->form_actions;
    }

    /**
     * @param $form_actions the $form_actions to set
     */
    public function set_form_actions($form_actions)
    {
        $this->form_actions = $form_actions;
    }

    function add_form_action(ObjectTableFormAction $form_action)
    {
        $this->form_actions[] = $form_action;
    }

    public function has_form_actions()
    {
        return count($this->form_actions) >= 1;
    }
}
?>