<?php
namespace repository\content_object\mediamosa;

require_once Path :: get_application_path() . 'common/rights_editor_manager/component/user_rights_setter.class.php';


class MediamosaRightsEditorManagerUserRightsSetterComponent extends MediamosaRightsEditormanager
{
    function run()
    {
       $component = new RightsEditorManagerUserRightsSetterComponent($this->get_parent(), $this->get_locations());
       $component->run();
       $this->update_mediamosa_rights();
    }

    
}
?>
