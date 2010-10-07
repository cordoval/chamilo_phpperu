<?php

require_once dirname(__FILE__) . '/toggle_visibility.class.php';

class ToolComponentHidePublicationComponent extends ToolComponentToggleVisibilityComponent
{
    function get_hidden()
    {
        return 1;
    }
}
?>