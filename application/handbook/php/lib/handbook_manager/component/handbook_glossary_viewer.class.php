<?php
namespace application\handbook;

use repository\content_object\glossary\GlossaryDisplayViewerComponent;
use repository\ContentObjectDisplay;
use repository\ComplexDisplay;
use repository\content_object\glossary\Glossary;
use repository\ComplexDisplayPreview;
use common\libraries\LauncherApplication;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Display;
use repository\content_object\glossary\GlossaryDisplay;
use common\libraries\Request;



/**
 * Component to create a new handbook_publication object
 */
class HandbookManagerHandbookGlossaryViewerComponent extends HandbookManager
{
   
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
       $hid = Request::get(HandbookManager::PARAM_HANDBOOK_ID);
        $this->set_parameter(HandbookManager::PARAM_HANDBOOK_ID, $hid);
        GlossaryDisplay:: launch(Glossary::get_type_name(), $this, false);
    }

    static function is_allowed($right)
    {
        //TODO: return correct value for add and remove (check publication rights)
        return false;
    }

    static function get_root_content_object()
    {

        $handbook_id = Request::get(HandbookManager::PARAM_HANDBOOK_ID);
        $glossary_list = HandbookManager::retrieve_all_glossaries($handbook_id);


        $content_object_array = array();
        $rdm = \repository\RepositoryDataManager::get_instance();
        foreach ($glossary_list as $glos)
        {
            $content_object_array[] = $rdm->retrieve_content_object($glos);
        }

        return $content_object_array;
    }

    function display_header()
    {
        Display :: small_header();
    }

    function display_footer()
    {
        Display::small_footer();
    }

}
?>