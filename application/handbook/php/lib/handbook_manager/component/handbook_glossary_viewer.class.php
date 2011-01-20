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
use common\libraries\SubManager;
use common\libraries\DynamicTabsRenderer;
use common\libraries\DynamicContentTab;
use repository\RepositoryDataManager;
use application\metadata\MetadataManager;
use common\libraries\EqualityCondition;
use application\metadata\MetadataPropertyValue;
use application\metadata\MetadataDataManager;


require_once dirname(__FILE__) . '/../../../../../../repository/content_object/glossary/php/display/component/viewer.class.php';


/**
 * Component to create a new handbook_publication object
 */
class HandbookManagerHandbookGlossaryViewerComponent extends HandbookManager
{

    private $submanager_array;
    private $languages_glossaries_array;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
       $hid = Request::get(HandbookManager::PARAM_HANDBOOK_ID);
        $this->set_parameter(HandbookManager::PARAM_HANDBOOK_ID, $hid);
        $this->retrieve_glossaries();

        //create tab for every language
        $tabs = new DynamicTabsRenderer('renderer');
        $i=1;
       foreach ($this->languages_glossaries_array as $key => $value)
        {
           $submanager = null;
            $submanager = new GlossaryDisplayViewerComponent($this);
            
            $this->submanager_array[$key] = $submanager;
            $htmlt['tab'.$i][] = '<div>';
            $htmlt['tab'.$i][] = $submanager->to_html();
            $htmlt['tab'.$i][] = '</div>';$tabs->add_tab(new DynamicContentTab($key , $key, null, implode("\n", $htmlt['tab'.$i])));
            $i++;
        }
        $html[] = $tabs->render();
        
        $this->display_header();
        echo implode("\n", $html);
        $this->display_footer();
    }

    static function is_allowed($right)
    {
        //TODO: return correct value for add and remove (check publication rights)
        return false;
    }
    
    function retrieve_glossaries()
    {        
        $handbook_id = Request::get(HandbookManager::PARAM_HANDBOOK_ID);
        $glossary_list = HandbookManager::retrieve_all_glossaries($handbook_id);
        //check the language of the glossaries
        foreach ($glossary_list as $glos)
        {
            $condition = new EqualityCondition(MetadataPropertyValue::PROPERTY_CONTENT_OBJECT_ID, $glos);
            $metadata = MetadataDataManager::get_instance()->retrieve_content_object_metadata_properties_and_values($condition);
            foreach($metadata as $key=>$value)
            {
                if(in_array($key, HandbookManager::get_language_metadata_properties()))
                {
                    $this->languages_glossaries_array[$value][] = $glos;                    
                }
            }
        }        
    }

    function get_root_content_object($sub_manager)
    {
        $key = array_search($sub_manager, $this->submanager_array, true);
        
        $handbook_id = Request::get(HandbookManager::PARAM_HANDBOOK_ID);
    
        $glossary_list = $this->languages_glossaries_array[$key];
       

        $content_object_array = array();
        $rdm = RepositoryDataManager::get_instance();
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