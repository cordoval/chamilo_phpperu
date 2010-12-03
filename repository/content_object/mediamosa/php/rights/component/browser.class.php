<?php
namespace repository\content_object\mediamosa;

use common\libraries\Translation;
use common\libraries\Path;
use common\extensions\rights_editor_manager\RightsEditorManagerBrowserComponent;
require_once __DIR__ . '/../mediamosa_rights_editor_manager.class.php';

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