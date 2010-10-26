<?php
namespace application\weblcms\tool\note;

use application\weblcms\ToolComponent;

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
