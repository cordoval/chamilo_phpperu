<?php
class HomeTool extends Tool
{
    const ACTION_VIEW_HOME = 'view';

    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();

        switch ($action)
        {
            case self :: ACTION_VIEW_HOME :
                $component = $this->create_component('Viewer');
                break;
            default :
                $component = $this->create_component('Viewer');
        }
        $component->run();
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }
}
?>