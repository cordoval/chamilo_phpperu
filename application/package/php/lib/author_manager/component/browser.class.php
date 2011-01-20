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
class AuthorManagerBrowserComponent extends AuthorManager
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
        $table = new AuthorBrowserTable($this, $this->get_parameters());
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(
                AuthorManager :: PARAM_AUTHOR_ACTION => AuthorManager :: ACTION_CREATE))));
        return $action_bar;
    }

    //    function get_condition()
    //    {
    //        $properties[] = new ConditionProperty(Author :: PROPERTY_);
    //        $properties[] = new ConditionProperty(Package :: PROPERTY_SECTION);
    //        
    //        return $this->action_bar->get_conditions($properties);
    //    }
    

    function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('author_browser');
    }
}
?>