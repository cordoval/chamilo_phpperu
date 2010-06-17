<?php
/**
 * Description of moverclass
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/../../component/mover.class.php';

class NoteToolMoveUpComponent extends NoteTool
{

    function run()
    {
        xdebug_break();
        $mover = ToolComponent :: factory(ToolComponent :: ACTION_MOVE, $this);
        $mover->run();
    }

    function get_move_direction()
    {
        return 1;
    }
}
?>
