<?php

require_once Path :: get_application_path() . 'common/rights_editor_manager/component/browser.class.php';

class MediamosaRightsEditorManagerBrowserComponent extends MediamosaRightsEditorManager
{
    function run()
    {
       //$component = new RightsEditorManagerBrowserComponent($this->get_parent(), $this->get_locations());
       $this->display_warning_message(Translation :: get('NotOriginalPublisher') . Translation :: get('CannotEditOriginalRights'));

       $component = new RightsEditorManagerBrowserComponent($this, $this->get_locations());
       $component->run();

       $this->update_mediamosa_rights();
    }
}
?>
