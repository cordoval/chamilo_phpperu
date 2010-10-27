<?php
namespace application\weblcms\tool\home;

use application\weblcms\Tool;
use application\weblcms\ToolComponent;

class HomeToolUpdaterComponent extends HomeTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }
}
?>