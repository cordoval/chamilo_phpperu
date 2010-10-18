<?php
class LearningPathToolShowPublicationComponent extends LearningPathTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_hidden()
    {
        return 0;
    }
}
?>