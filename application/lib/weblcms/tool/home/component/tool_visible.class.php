<?php
require_once dirname(__FILE__) . '/tool_visibility_changer.class.php';

class HomeToolToolVisibleComponent extends HomeToolToolVisibilityChangerComponent
{

    function run()
    {
        Request :: set_get(HomeTool :: PARAM_VISIBILITY, 1);
        parent :: run();
    }
}
?>