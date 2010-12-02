<?php
namespace repository\content_object\mediamosa;

use common\libraries\Path;
use common\extensions\rights_editor_manager\RightsEditorManagerGroupRightsSetterComponent;

require_once __DIR__ . '/../mediamosa_rights_editor_manager.class.php';

class MediamosaRightsEditorManagerGroupRightsSetterComponent extends MediamosaRightsEditormanager
{
    function run()
    {
       $component = new RightsEditorManagerGroupRightsSetterComponent($this->get_parent(), $this->get_locations());
       $component->run();
       $this->update_mediamosa_rights();
    }
}
