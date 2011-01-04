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

        //TODO: retrieve correct content object (= glossary in handbook)
        //for now this is a dummy glossary to test the other functionality
        $rdm = \repository\RepositoryDataManager::get_instance();
        $content_object = $rdm->retrieve_content_object(51);
        
//        ComplexDisplay:: launch(Glossary::get_type_name(), $this, false);
        GlossaryDisplay:: launch(Glossary::get_type_name(), $this, false);
    }

    static function is_allowed($right)
    {
        //TODO: return correct value for add and remove (check publication rights)

        return false;
    }

    static function get_root_content_object()
    {
        $content_object_array = array();
        $rdm = \repository\RepositoryDataManager::get_instance();
        $content_object_array[] = $rdm->retrieve_content_object(51);
        $content_object_array[] = $rdm->retrieve_content_object(60);

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