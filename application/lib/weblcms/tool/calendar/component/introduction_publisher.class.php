<?php

class CalendarToolIntroductionPublisherComponent extends CalendarTool
{

    function run()
    {
        $component = ToolComponent :: factory(ToolComponent :: INTRODUCTION_PUBLISHER_COMPONENT, $this);
        $component->run();
    }
}
?>