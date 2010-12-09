<?php
namespace application\phrases;

use common\libraries\Request;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;
use common\libraries\SubselectCondition;
use repository\ContentObject;
use repository\RepositoryDataManager;
use common\libraries\InCondition;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\BreadcrumbTrail;
use common\libraries\InequalityCondition;
use common\libraries\Utilities;
/**
 * $Id: browser.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component
 */

require_once dirname(__FILE__) . '/../phrases_manager.class.php';
require_once dirname(__FILE__) . '/../../phrases_publication_category_menu.class.php';
require_once dirname(__FILE__) . '/phrases_publication_browser/phrases_publication_browser_table.class.php';

/**
 * phrases component which allows the user to browse his phrases_publications
 * @author Hans De Bisschop
 * @author
 */
class PhrasesManagerBrowserComponent extends PhrasesManager
{
    private $action_bar;

    function run()
    {
        $this->action_bar = $this->get_action_bar();
        $menu = $this->get_menu();
        //$menu->get_breadcrumbs();
        //$trail->merge($menu->get_breadcrumbs());
        $this->display_header();

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
        $table = new PhrasesPublicationBrowserTable($this, $this->get_parameters(), $this->get_condition());
        return $table->as_html();
    }

    function get_menu()
    {
        $current_category = Request :: get(self :: PARAM_CATEGORY);
        $current_category = $current_category ? $current_category : 0;
        $menu = new PhrasesPublicationCategoryMenu($current_category);
        return $menu;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());

        $current_category = Request :: get(self :: PARAM_CATEGORY);
        $current_category = $current_category ? $current_category : 0;

        if ($current_category == 0)
        {
            $RIGHT_PUBLISH = PhrasesRights :: is_allowed_in_phrasess_subtree(PhrasesRights :: PUBLISH_RIGHT, 0, 0);
        }
        else
        {
            $RIGHT_PUBLISH = PhrasesRights :: is_allowed_in_phrasess_subtree(PhrasesRights :: PUBLISH_RIGHT, $current_category, PhrasesRights :: TYPE_CATEGORY);
        }

        if ($RIGHT_PUBLISH)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_publish.png', $this->get_create_phrases_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($this->get_user()->is_platform_admin())
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_category.png', $this->get_manage_phrases_publication_categories_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageRights', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_rights.png', $this->get_rights_editor_url($current_category), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ViewResultsSummary'), Theme :: get_common_image_path() . 'action_view_results.png', $this->get_phrases_results_viewer_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }

    function get_condition()
    {
        $current_category = Request :: get(self :: PARAM_CATEGORY);
        $current_category = $current_category ? $current_category : 0;

        $query = $this->action_bar->get_query();

        $user = $this->get_user();
        $datamanager = PhrasesDataManager :: get_instance();

        if ($user->is_platform_admin())
        {
            $user_id = array();
            $groups = array();
        }
        else
        {
            $user_id = $user->get_id();
            $groups = $user->get_groups();
        }

        $conditions = array();

        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $subselect_condition = new OrCondition($search_conditions);
            $conditions[] = new SubselectCondition(PhrasesPublication :: PROPERTY_CONTENT_OBJECT, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        }

        $access = array();
        $access[] = new EqualityCondition(PhrasesPublication :: PROPERTY_PUBLISHER, $user_id = $user->get_id());
        $access[] = new InCondition(PhrasesPublicationUser :: PROPERTY_USER, $user_id, PhrasesPublicationUser :: get_table_name());
        $access[] = new InCondition(PhrasesPublicationGroup :: PROPERTY_GROUP_ID, $groups, PhrasesPublicationGroup :: get_table_name());
        if (! empty($user_id) || ! empty($groups))
        {
            $access[] = new AndCondition(array(
                    new EqualityCondition(PhrasesPublicationUser :: PROPERTY_USER, null, PhrasesPublicationUser :: get_table_name()),
                    new EqualityCondition(PhrasesPublicationGroup :: PROPERTY_GROUP_ID, null, PhrasesPublicationGroup :: get_table_name())));
        }
        $conditions[] = new OrCondition($access);

        if (! $user->is_platform_admin())
        {
            $visibility = array();
            $visibility[] = new EqualityCondition(PhrasesPublication :: PROPERTY_HIDDEN, false);
            $visibility[] = new EqualityCondition(PhrasesPublication :: PROPERTY_PUBLISHER, $user->get_id());
            $conditions[] = new OrCondition($visibility);

            $dates = array();
            $dates[] = new AndCondition(array(
                    new InequalityCondition(PhrasesPublication :: PROPERTY_FROM_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time()),
                    new InequalityCondition(PhrasesPublication :: PROPERTY_TO_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time())));
            $dates[] = new AndCondition(array(
                    new EqualityCondition(PhrasesPublication :: PROPERTY_FROM_DATE, 0),
                    new EqualityCondition(PhrasesPublication :: PROPERTY_TO_DATE, 0)));
            $dates[] = new EqualityCondition(PhrasesPublication :: PROPERTY_PUBLISHER, $user->get_id());
            $conditions[] = new OrCondition($dates);
        }

        $conditions[] = new EqualityCondition(PhrasesPublication :: PROPERTY_CATEGORY, $current_category);

        return new AndCondition($conditions);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_browser');
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CATEGORY);
    }
}
?>