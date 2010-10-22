<?php

namespace application\reservations;

use common\libraries\WebApplication;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
/**
 * $Id: quota_browser.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/quota_browser/quota_browser_table.class.php';

class ReservationsManagerQuotaBrowserComponent extends ReservationsManager
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewQuota')));

        $this->ab = $this->get_action_bar();

        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo $this->get_user_html();
        $this->display_footer();
    }

    function get_user_html()
    {
        $table = new QuotaBrowserTable($this, $this->get_parameters(), $this->get_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_condition()
    {
        $search = $this->ab->get_query();
        if (isset($search) && ($search != ''))
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(Quota :: PROPERTY_CREDITS, '*' . $search . '*');
            $conditions[] = new PatternMatchCondition(Quota :: PROPERTY_TIME_UNIT, '*' . $search . '*');
            return new OrCondition($conditions);
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_quota_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
?>
