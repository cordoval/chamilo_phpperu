<?php
namespace application\weblcms\tool\geolocation;

use common\libraries\Request;
use application\weblcms\ToolComponent;

class GeolocationToolMoverComponent extends GeolocationTool
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