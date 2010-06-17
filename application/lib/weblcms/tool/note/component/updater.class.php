<?php
/**
 * Description of updaterclass
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/../../component/updater.class.php';

class NoteToolUpdaterComponent extends NoteTool
{

    function run()
    {
        $update = ToolComponent :: factory(ToolComponent :: ACTION_UPDATE, $this);
        $update->run();
    }
}
?>
