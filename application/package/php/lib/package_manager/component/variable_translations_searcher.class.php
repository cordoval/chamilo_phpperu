<?php

namespace application\package;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\WebApplication;
use common\libraries\EqualityCondition;
use common\libraries\Application;
use common\libraries\Utilities;
/**
 * @package application.package.package.component
 */

require_once WebApplication :: get_application_class_lib_path('package') . 'forms/variable_translation_search_form.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/variable_translation_browser/variable_translation_browser_table.class.php';

/**
 * package component which allows the user to browse his variable_translations
 * @author Sven Vanpoucke
 * @author 
 */
class PackageManagerVariableTranslationsSearcherComponent extends PackageManager
{
	private $action_bar;
	private $form;
	
	function run()
	{
//		$trail = BreadcrumbTrail :: get_instance();
//		$trail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('Package')));
//		$trail->add(new Breadcrumb($this->get_variable_translations_searcher_url(), Translation :: get('SearchVariableTranslations')));

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
		$html[] = '<span class="category">' . Translation :: get('Search', null, Utilities :: COMMON_LIBRARIES) . '</span>';
		$html[] = $this->form->toHtml();
		$html[] = '</div>';

		return implode("\n", $html);
	}
	
	function get_table()
	{
		$table = new VariableTranslationBrowserTable($this, 
			array(Application :: PARAM_APPLICATION => 'package', Application :: PARAM_ACTION => PackageManager :: ACTION_SEARCH_VARIABLE_TRANSLATIONS), 
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

	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('PackageManagerPackageLanguagesBrowserComponent')));
    	$breadcrumbtrail->add_help('variable_translations_searcher');
    }

}
?>