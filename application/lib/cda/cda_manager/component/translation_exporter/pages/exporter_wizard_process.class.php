<?php
/**
 * $Id: exporter_wizard_process.class.php 204 2009-11-13 12:51:30Z kariboe $
 */

require_once dirname(__FILE__) . '/../translation_exporter.class.php';

/**
 * This class implements the action to take after the user has completed the export wizard
 */
class ExporterWizardProcess extends HTML_QuickForm_Action
{

    private $parent;


    public function ExporterWizardProcess($parent)
    {
        $this->parent = $parent;
    }

    function perform($page, $actionName)
    {
    	$this->parent->display_header(new BreadcrumbTrail(false));
    	
    	$values = $page->controller->exportValues();
    	
    	$pages = $page->get_parent()->get_pages();
    	$page_number = 1;
    	
        echo '<div id="progressbox">';
        echo '<ul id="progresstrail">';
    	
    	foreach($pages as $page)
    	{
    		 echo '<li class="active"><a href="#">' . $page_number . '.&nbsp;&nbsp;' . $page->get_title() . '</a></li>';
    		 $page_number++;
    	}
    	
    	echo '<li class="active"><a href="#">' . $page_number . '.&nbsp;&nbsp;' . Translation :: get('Download') . '</a></li>';
        echo '</ul>';
        echo '<div class="clear"></div>';
        echo '</div>';
    	
    	$language_ids = array_keys($values['language']);
    	$language_pack_ids = array_keys($values['language_packs']);
    	
    	$languages = $this->parent->retrieve_cda_languages(new InCondition(CdaLanguage :: PROPERTY_ID, $language_ids))->as_array();
    	$language_packs = $this->parent->retrieve_language_packs(new InCondition(LanguagePack :: PROPERTY_ID, $language_pack_ids))->as_array();
    	
    	$exporter = TranslationExporter :: factory($values['branch'], $this->parent->get_user(), $languages, $language_packs);
    	$folder = $exporter->export_translations();
    	
    	$url = '<a href="' . $folder . '">' . Translation :: get('DownloadTranslations') . '</a>';
    	
    	echo '<div class="normal-message">' . $url . '</div>';
    	
    	$this->parent->display_footer();
    }
}
?>