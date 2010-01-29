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
        
        echo '<div id="select" class="row"><div class="formc formc_no_margin">';
        echo '<b>' . Translation :: get('Step') . ' ' . $page_number . ' ' . Translation :: get('of') . ' ' . $page_number . ' &ndash; ' . Translation :: get('Download') . '</b><br />';
        echo Translation :: get('DownloadInfo');
        echo '</div>';
        echo '</div><br />';
    	
    	$language_ids = array_keys($values['language']);
    	$language_pack_ids = array_keys($values['language_packs']);
    	
    	$languages = $this->parent->retrieve_cda_languages(new InCondition(CdaLanguage :: PROPERTY_ID, $language_ids))->as_array();
    	$language_packs = $this->parent->retrieve_language_packs(new InCondition(LanguagePack :: PROPERTY_ID, $language_pack_ids))->as_array();
    	
    	$exporter = TranslationExporter :: factory($values['branch'], $this->parent->get_user(), $languages, $language_packs);
    	$folder = $exporter->export_translations();
    	
    	$url = '<a href="' . $folder . '">' . Translation :: get('DownloadTranslations') . '</a>';
    	
    	echo '<div class="normal-message">' . $url . '</div>';
    	
    	echo '<div>';
    	
    	$branch_options = LanguagePack :: get_branch_options();
    	
    	echo Display :: form_category(Translation :: get('PackageProperties'));
    	echo Display :: form_row(Translation :: get('Branch'), $branch_options[$values[LanguagePack :: PROPERTY_BRANCH]]);
    	
    	$condition = new InCondition(CdaLanguage :: PROPERTY_ID, array_keys($values['language']));
    	$languages = CdaDataManager :: get_instance()->retrieve_cda_languages($condition, null, null, array(new ObjectTableOrder(CdaLanguage :: PROPERTY_ORIGINAL_NAME)));
    	
    	$html = array();
    	while($language = $languages->next_result())
    	{
    		$html[] = $language->get_original_name();
    	}
    	
    	echo Display :: form_row(Translation :: get('Languages'), implode('<br />', $html));
    	
        $condition = new InCondition(LanguagePack :: PROPERTY_ID, array_keys($values['language_packs']));
    	$language_packs = CdaDataManager :: get_instance()->retrieve_language_packs($condition, null, null, array(new ObjectTableOrder(LanguagePack :: PROPERTY_NAME)));
    	
    	$html = array();
    	while($language_pack = $language_packs->next_result())
    	{
    		$html[] = $language_pack->get_name();
    	}
    	echo Display :: form_row(Translation :: get('LanguagePacks'), implode('<br />', $html));
    	echo Display :: form_category();
    	
    	echo '<div class="clear"></div>';
    	echo '</div>';
    	
    	$this->parent->display_footer();
    }
}
?>