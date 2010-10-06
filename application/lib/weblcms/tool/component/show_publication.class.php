<?php

require_once dirname(__FILE__) . '/toggle_visibility.class.php';

class ToolComponentShowPublicationComponent extends ToolComponentToggleVisibilityComponent
{
    function get_hidden()
    {
        return 0;
    }
}
?>