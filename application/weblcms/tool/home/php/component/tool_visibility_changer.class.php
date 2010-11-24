<?php
namespace application\weblcms\tool\home;

use application\weblcms\WeblcmsDataManager;
use common\libraries\Request;
use common\libraries\Translation;

class HomeToolToolVisibilityChangerComponent extends HomeTool
{

    function run()
    {
        $tool = Request :: get(HomeTool :: PARAM_TOOL);
        $visibility = Request :: get(HomeTool :: PARAM_VISIBILITY);

        $wdm = WeblcmsDataManager :: get_instance();
        $succes = $wdm->set_module_visible($this->get_course_id(), $tool, $visibility);

        $message = $succes ? 'ToolVisibilityChanged' : 'ToolVisibilityNotChanged';

        $this->redirect(Translation :: get($message), ! $succes, array(HomeTool :: PARAM_ACTION => HomeTool :: ACTION_BROWSE));
    }
}
?>