<?php
namespace application\weblcms\tool\glossary;

use application\weblcms\ToolComponent;

class GlossaryToolMoverComponent extends GlossaryTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_move_direction()
    {
        return Request :: get(Tool :: PARAM_MOVE_DIRECTION);
    }

}

?>
