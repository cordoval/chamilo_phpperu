<?php

require_once Path :: get_application_path() . 'common/rights_editor_manager/component/browser.class.php';

class StreamingVideoClipRightsEditorManagerBrowserComponent extends StreamingVideoClipRightsEditorManager
{
    function run()
    {
       $component = new RightsEditorManagerBrowserComponent($this->get_parent(), $this->get_locations());
       $component->run();
       $this->update_mediamosa_rights();
    }
}
?>
