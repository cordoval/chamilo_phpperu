<?php
namespace application\weblcms\tool\learning_path;

use application\weblcms\ToolComponent;

class LearningPathToolHidePublicationComponent extends LearningPathTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

}
?>