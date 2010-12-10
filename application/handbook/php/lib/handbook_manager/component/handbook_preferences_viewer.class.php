<?php
namespace application\handbook;

use common\libraries\Request;

require_once dirname(__FILE__).'/../handbook_manager.class.php';

/**
 * Description of handbook_preferences_viewer
 *
 * @author nblocry
 */
class handbook_preferences_viewer extends HandbookManager
{
    private $handbook_publication_id;

    /**
    * Runs this component and displays its output.
    */
    function run()
    {
        $this->handbook_publication_id = Request :: get(HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID);
        $preferences = HandbookManager::get_preferences($this->handbook_publication_id);
        echo $preferences;
    }
}
