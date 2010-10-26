<?php
namespace application\weblcms\tool\blog;

use application\weblcms\ToolComponent;

class BlogToolEvaluateComponent extends BlogTool
{
    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>