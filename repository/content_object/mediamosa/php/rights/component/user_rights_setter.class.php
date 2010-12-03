<?php
namespace repository\content_object\mediamosa;

use common\libraries\Path;

use common\extensions\rights_editor_manager\RightsEditorManagerUserRightsSetterComponent;

require_once __DIR__ . '/../mediamosa_rights_editor_manager.class.php';


class MediamosaRightsEditorManagerUserRightsSetterComponent extends MediamosaRightsEditormanager
{
    function run()
    {
       $component = new RightsEditorManagerUserRightsSetterComponent($this->get_parent(), $this->get_locations());
       $component->run();
       $this->update_mediamosa_rights();
    }


}
