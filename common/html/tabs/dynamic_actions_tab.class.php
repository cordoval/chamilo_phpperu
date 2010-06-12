<?php
class DynamicActionsTab extends DynamicTab
{

    /**
     * @param integer $id
     * @param string $name
     * @param string $image
     * @param Array $actions
     */
    public function DynamicActionsTab($id, $name, $image, $actions = array())
    {
        parent :: __construct($id, $name, $image);
        $this->actions = $actions;
    }

    /**
     * @return the $content
     */
    public function get_actions()
    {
        return $this->actions;
    }

    /**
     * @param $content the $content to set
     */
    public function set_actions($actions)
    {
        $this->actions = $actions;
    }

    public function add_action(DynamicAction $action)
    {
        $this->actions[] = $action;
    }

    /**
     * @param string $tab_name
     * @return string
     */
    public function body($tab_name)
    {
        $html = array();
        
        $html[] = '<h2>';
        if ($this->get_image())
        {
            $html[] = '<img src="' . $this->get_image() . '" border="0" style="vertical-align: middle;" alt="' . $this->get_name() . '" title="' . $this->get_name() . '"/>&nbsp;';
        }
        $html[] = $this->get_name();
        $html[] = '</h2>';
        
        $html[] = '<div class="admin_tab no-padding" id="' . $tab_name . '">';
        $html[] = '<a class="prev"></a>';
        
        $html[] = '<div class="items">';
        
        foreach ($this->actions as $key => $action)
        {
            $html[] = $action->render($key == 0);
        }
        $html[] = '</div>';
        
        $html[] = $this->body_footer($tab_name);
        
        return implode("\n", $html);
    }
}