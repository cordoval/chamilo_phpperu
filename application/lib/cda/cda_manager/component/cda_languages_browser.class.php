<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/cda_language_browser/cda_language_browser_table.class.php';

/**
 * cda component which allows the user to browse his cda_languages
 * @author Sven Vanpoucke
 * @author
 */
class CdaManagerCdaLanguagesBrowserComponent extends CdaManagerComponent
{
	private $action_bar;
	
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseLanguages')));

		$this->action_bar = $this->get_action_bar();
		
		$this->display_header($trail);
        echo '<a name="top"></a>';
        echo $this->action_bar->as_html() . '';
        echo '<div id="action_bar_browser">';
        echo $this->get_table();
        echo '</div>';
		$this->display_footer();
	}

	function get_table()
	{
		$table = new CdaLanguageBrowserTable($this, 
			array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_CDA_LANGUAGES), 
			$this->get_condition());
		return $table->as_html();
	}

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
      	$action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ExportTranslations'), Theme :: get_common_image_path() . 'action_export.png', $this->get_export_translations_url()));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url()));
        return $action_bar;
    }
    
    function get_condition()
    {
    	$query = $this->action_bar->get_query();
    	
    	if($query && $query != '')
    	{
    		$conditions[] = new PatternMatchCondition(CdaLanguage :: PROPERTY_ENGLISH_NAME, '*' . $query . '*');
    		$conditions[] = new PatternMatchCondition(CdaLanguage :: PROPERTY_ORIGINAL_NAME, '*' . $query . '*');
    	
    		return new OrCondition($conditions);
    	}
    }
}
?>