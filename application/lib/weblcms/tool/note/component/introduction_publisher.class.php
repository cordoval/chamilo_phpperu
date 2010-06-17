<?php
/**
 * Description of introduction_publisherclass
 *
 * @author jevdheyd
 */

require_once dirname(__FILE__) . '/../../component/introduction_publisher.class.php';

class NoteToolIntroductionPublisherComponent extends NoteTool
{
    function run()
    {
        $introduction_publisher =$publisher = ToolComponent :: factory(ToolComponent :: INTRODUCTION_PUBLISHER_COMPONENT, $this);
        $introduction_publisher->run();
    }
}
?>
