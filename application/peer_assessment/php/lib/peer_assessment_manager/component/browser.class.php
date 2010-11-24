<?php

namespace application\peer_assessment;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;
use repository\ContentObject;
use common\libraries\AndCondition;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/peer_assessment_publication_browser/peer_assessment_publication_browser_table.class.php';
require_once dirname(__FILE__) . '/../../peer_assessment_publication_category_menu.class.php';

/**
 * peer_assessment component which allows the user to browse his peer_assessment_publications
 * @author Nick Van Loocke
 */
class PeerAssessmentManagerBrowserComponent extends PeerAssessmentManager
{

    private $action_bar;

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PeerAssessment')));


        $this->action_bar = $this->get_action_bar();
        $menu = $this->get_menu();
        $trail->merge($menu->get_breadcrumbs());
        $this->display_header($trail, true);

        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo '<div style="float: left; width: 17%; overflow: auto;">';
        echo $menu->render_as_tree();
        echo '</div>';
        echo '<div style="width: 80%; float: right;">';
        echo $this->get_table();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_table()
    {
        $condition = $this->get_condition();
        $table = new PeerAssessmentPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => PeerAssessmentManager :: APPLICATION_NAME, Application :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS), $condition);
        return $table->as_html();
    }

    function get_menu()
    {
        $current_category = Request :: get('category');
        $current_category = $current_category ? $current_category : 0;
        $menu = new PeerAssessmentPublicationCategoryMenu($current_category);
        return $menu;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_CREATE_PEER_ASSESSMENT_PUBLICATION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('category' => Request :: get('category'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_category_manager_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    private function get_condition()
    {
        $category_id = Request :: get('category');
        if ($category_id == null)
        {
            $category_id = 0;
        }
        $conditions[] = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_CATEGORY, $category_id);

        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');

            $conditions[] = new OrCondition($or_conditions);
        }

        $condition = new AndCondition($conditions);
        return $condition;
    }

}

?>