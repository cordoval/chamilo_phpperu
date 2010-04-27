<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../../forms/variable_translation_search_form.class.php';
require_once dirname(__FILE__).'/variable_translation_browser/variable_translation_browser_table.class.php';

/**
 * cda component which allows the user to browse his variable_translations
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerVariableTranslationsSearcherComponent extends CdaManager
{
	private $action_bar;
	private $form;
	
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('Cda')));
		$trail->add(new Breadcrumb($this->get_variable_translations_searcher_url(), Translation :: get('SearchVariableTranslations')));

		$this->display_header($trail);
		echo $this->display_form();
		echo '<br />';
        echo $this->get_table();
		$this->display_footer();
	}

	function display_form()
	{
		$this->form = new VariableTranslationSearchForm($this, $this->get_variable_translations_searcher_url());
		
		$html[] = '<div class="configuration_form">';
		$html[] = '<span class="category">' . Translation :: get('Search') . '</span>';
		$html[] = $this->form->toHtml();
		$html[] = '</div>';

		return implode("\n", $html);
	}
	
	function get_table()
	{
		$table = new VariableTranslationBrowserTable($this, 
			array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_SEARCH_VARIABLE_TRANSLATIONS), 
			$this->get_condition());
			
		return $table->as_html();
	}
	
	function get_condition()
	{
		$condition = $this->form->get_search_conditions();
		if($condition)
			return $condition;
			
		return new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, 0);
	}


}
?>