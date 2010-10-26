<?php
namespace application\weblcms\tool\blog;

use application\weblcms\ToolComponent;

class BlogToolBrowserComponent extends BlogTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>