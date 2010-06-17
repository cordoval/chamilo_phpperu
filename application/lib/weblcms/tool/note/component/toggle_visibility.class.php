<?php
/**
 * Description of toggle_visibilityclass
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/../../component/toggle_visibility.class.php';

class NoteToolToggleVisibilityComponent extends NoteTool
{
    function run()
    {
        $toggle_visibility = ToolComponent :: factory(ToolComponent :: ACTION_TOGGLE_VISIBILITY, $this);
        $toggle_visibility->run();
    }
}
?>
