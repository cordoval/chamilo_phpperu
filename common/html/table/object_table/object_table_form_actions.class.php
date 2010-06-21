<?php
/**
 * $Id: object_table_form_action.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.table.object_table
 */
class ObjectTableFormActions
{
    private $action;
    private $form_actions;

    /**
     * @param string $action
     * @param array $form_actions
     */
    function ObjectTableFormActions($action = Application :: PARAM_ACTION, $form_actions = array())
    {
        $this->action = $action;
        $this->form_actions = $form_actions;
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