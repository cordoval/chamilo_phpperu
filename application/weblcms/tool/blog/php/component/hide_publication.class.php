<?php
namespace application\weblcms\tool\blog;

use application\weblcms\ToolComponent;

class BlogToolHidePublicationComponent extends BlogTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>