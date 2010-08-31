<?php
/**
 * Description of browserclass
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/../../component/browser.class.php';

class NoteToolBrowserComponent extends NoteTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>
