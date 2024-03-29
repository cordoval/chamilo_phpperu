<?php

namespace application\reservations;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\WebApplication;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;
use common\libraries\Utilities;
/**
 * $Id: admin_category_browser.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/category_browser/category_browser_table.class.php';

class ReservationsManagerAdminCategoryBrowserComponent extends ReservationsManager
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->ab = $this->get_action_bar();
        $menu = new ReservationsMenu($_GET[ReservationsManager :: PARAM_CATEGORY_ID], '?application=reservations&go=admin_category_browser&category_id=%s');

        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo '<div style="float: left; overflow: auto; width: 18%;">' . $menu->render_as_tree() . '</div>';
        echo $this->get_user_html();
        $this->display_footer();
    }

    function get_user_html()
    {
        //ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category()
        $parameters = array_merge($this->get_parameters(), array(ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category()));
        $table = new CategoryBrowserTable($this, $parameters, $this->get_condition());

        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_condition()
    {
        $cat_id = $this->get_category();
        $conditions[] = new EqualityCondition(Category :: PROPERTY_PARENT, $cat_id);
        $conditions[] = new EqualityCondition(Category :: PROPERTY_STATUS, Category :: STATUS_NORMAL);
        $condition = new AndCondition($conditions);

        $search = $this->ab->get_query();
        if (isset($search) && ($search != ''))
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(Category :: PROPERTY_NAME, '*' . $search . '*');
            $orcondition = new OrCondition($conditions);

            $conditions = array();
            $conditions[] = $orcondition;
            $conditions[] = $condition;
            $condition = new AndCondition($conditions);
        }
        return $condition;
    }

    function get_category()
    {
        return (isset($_GET[ReservationsManager :: PARAM_CATEGORY_ID]) ? $_GET[ReservationsManager :: PARAM_CATEGORY_ID] : 0);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $this->get_category())));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_category_url($_GET[ReservationsManager :: PARAM_CATEGORY_ID]), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ManageQuota'), Theme :: get_common_image_path() . 'action_statistics.png', $this->get_browse_category_quota_boxes_url(0), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}