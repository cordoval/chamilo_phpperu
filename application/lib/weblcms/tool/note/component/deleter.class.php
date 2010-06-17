<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of deleterclass
 *
 * @author jevdheyd
 */

require_once dirname(__FILE__) . '/../../component/deleter.class.php';

class NoteToolDeleterComponent extends NoteTool
{
    function run()
    {
        $deleter = ToolComponent :: factory(ToolComponent :: ACTION_DELETE, $this);
        $deleter->run();
    }
}
?>
