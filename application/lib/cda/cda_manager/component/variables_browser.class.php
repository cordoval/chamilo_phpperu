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

        echo '<a name="top"></a>';
        echo $this->get_action_bar_html() . '';
        echo '<div id="action_bar_browser">';
        echo $this->get_table();
        echo '</div>';
		$this->display_footer();
	}

	function get_table()
	{
		$table = new VariableBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLES), null);
		return $table->as_html();
	}

    function get_action_bar_html()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddVariable'), Theme :: get_common_image_path() . 'action_add.png', $this->get_url(array(Application :: PARAM_ACTION => CdaManager :: ACTION_CREATE_LANGUAGE_PACK))));
        return $action_bar->as_html();
    }

}
?>