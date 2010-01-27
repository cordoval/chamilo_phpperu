<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/variable_translation_browser/variable_translation_browser_table.class.php';

/**
 * cda component which allows the user to browse his variable_translations
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerVariableTranslationsBrowserComponent extends CdaManagerComponent
{

	function run()
	{
		$language_id = Request :: get(CdaManager :: PARAM_CDA_LANGUAGE);
		$language_pack_id = Request :: get(CdaManager :: PARAM_LANGUAGE_PACK);
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('BrowseLanguages')));
		$trail->add(new Breadcrumb($this->get_browse_language_packs_url($language_id), Translation :: get('BrowseLanguagePacks')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_CDA_LANGUAGE => $language_id,
														CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id)), 
								   Translation :: get('BrowseVariableTranslations')));

		$this->display_header($trail);
		echo $this->get_table();
		$this->display_footer();
	}

	function get_table()
	{
		$table = new VariableTranslationBrowserTable($this, 
			array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS), 
			$this->get_condition());
			
		return $table->as_html();
	}
	
	function get_condition()
	{
		$language_id = Request :: get(CdaManager :: PARAM_CDA_LANGUAGE);
		$language_pack_id = Request :: get(CdaManager :: PARAM_LANGUAGE_PACK);
		
		$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack_id);
		$conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 'cda_' . Variable :: get_table_name(), $subcondition);
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
		
		return new AndCondition($conditions);
	}

}
?>