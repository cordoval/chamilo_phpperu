<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/variable_browser/variable_browser_table.class.php';

/**
 * cda component which allows the user to browse his variables
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerVariablesBrowserComponent extends CdaManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseVariables')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_variable_url() . '">' . Translation :: get('CreateVariable') . '</a>';
		echo '<br /><br />';
		echo $this->get_table();
		$this->display_footer();
	}

	function get_table()
	{
		$table = new VariableBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLES), null);
		return $table->as_html();
	}

}
?>