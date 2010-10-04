<?php
require_once Path :: get_application_path() . 'common/rights_editor_manager/component/group_rights_setter.class.php';

class MediamosaRightsEditorManagerGroupRightsSetterComponent extends MediamosaRightsEditormanager
{
    function run()
    {
       $component = new RightsEditorManagerGroupRightsSetterComponent($this->get_parent(), $this->get_locations());
       $component->run();
       $this->update_mediamosa_rights();
    }
}
?>
