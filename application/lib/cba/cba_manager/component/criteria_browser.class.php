<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/criteria_browser/criteria_browser_table.class.php';
/**
 * Criteria component
 *
 * @author Nick Van Loocke
 */
class CbaManagerCriteriaBrowserComponent extends CbaManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('CBA')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseCriteria')));
		$this->display_header($trail, false, true);


		$this->action_bar = $this->get_action_bar();
		echo '<div style="float: right; width: 100%;">';
		echo $this->action_bar->as_html();
		echo '<br />';
		echo $this->get_table();
		echo '</div>';

		$this->display_footer();
	}

	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_url(array(Application :: PARAM_ACTION => CbaManager :: ACTION_CREATOR_CRITERIA)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(Application :: PARAM_ACTION => CbaManager :: ACTION_MANAGE_CATEGORIES_CRITERIA)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('category' => Request :: get('category'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->set_search_url($this->get_url());

        return $action_bar;
    }

	function get_table()
	{
		$condition = $this->get_condition();
		$table = new CriteriaBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cba', Application :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_CRITERIA), $condition);
		return $table->as_html();
	}

	private function get_condition()
    {
        $conditions[] = new EqualityCondition(Criteria :: PROPERTY_STATE, Criteria :: STATE_NORMAL);
        $conditions[] = new EqualityCondition(Criteria :: PROPERTY_OWNER_ID, $this->get_user_id());
        $conditions[] = new EqualityCondition(Criteria :: PROPERTY_PARENT_ID, $this->get_parent_id());

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

	private function get_parent_id()
    {
        return Request :: get(CbaManager :: PARAM_CATEGORY_ID) ? Request :: get(CbaManager :: PARAM_CATEGORY_ID) : 0;
    }

	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}

}
?>