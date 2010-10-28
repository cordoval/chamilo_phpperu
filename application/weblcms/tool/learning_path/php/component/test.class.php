<?php
namespace application\weblcms\tool\learning_path;

use common\libraries\Request;
use application\weblcms\ToolComponent;

class LearningPathToolTestComponent extends LearningPathTool
{

    function run()
    {
        Request :: set_get(self :: PARAM_ACTION, 'complex_display');
        ToolComponent :: launch($this);
    }
}
?>