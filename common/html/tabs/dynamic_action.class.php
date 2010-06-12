<?php
class DynamicActions
{
    private $name;
    private $description;
    private $action;
    private $url;
    private $confirm;

    public function DynamicAction($name, $description, $action, $url, $confirm = false)
    {
        $this->name = $name;
        $this->description = $description;
        $this->action = $action;
        $this->url = $url;
        $this->confirm = $confirm;
    }

    /**
     * @return the $name
     */
    public function getname()
    {
        return $this->name;
    }

    /**
     * @param $name the $name to set
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * @return the $description
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     * @param $description the $description to set
     */
    public function set_description($description)
    {
        $this->description = $description;
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
     * @return the $url
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     * @param $url the $url to set
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    /**
     * @return the $confirm
     */
    public function get_confirm()
    {
        return $this->confirm;
    }

    /**
     * @param $confirm the $confirm to set
     */
    public function set_confirm($confirm)
    {
        $this->confirm = $confirm;
    }

    /**
     * @return boolean the $confirm
     */
    public function needs_confirmation()
    {
        return $this->get_confirm();
    }

}