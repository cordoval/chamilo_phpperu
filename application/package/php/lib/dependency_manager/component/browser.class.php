<?php
namespace application\package;

use common\libraries\ActionBarRenderer;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\ToolbarItem;
use common\libraries\ConditionProperty;
use common\libraries\Application;
use common\libraries\Utilities;
/**
 * @package application.package.package.component
 */

/**
 * package component which allows the user to browse his package_languages
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DependencyManagerBrowserComponent extends DependencyManager
{
    
    private $action_bar;

    function run()
    {
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header();
        echo '<a name="top"></a>';
        echo $this->action_bar->as_html() . '';
        echo '<div id="action_bar_browser">';
        echo $this->get_table();
        echo '</div>';
        $this->display_footer();
    }

    function get_table()
    {
        $table = new DependencyBrowserTable($this, $this->get_parameters());
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(
                DependencyManager :: PARAM_DEPENDENCY_ACTION => DependencyManager :: ACTION_CREATE))));
        return $action_bar;
    }

    function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('dependency_browser');
    }
}
?>