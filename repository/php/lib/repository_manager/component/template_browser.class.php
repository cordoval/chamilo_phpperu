<?php
namespace repository;

use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\Session;
use common\libraries\ActionBarRenderer;
use common\libraries\ActionBarSearchForm;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use common\libraries\PatternMatchCondition;
use repository\content_object\learning_path_item\LearningPathItem;
use common\libraries\NotCondition;

/**
 * $Id: template_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

class RepositoryManagerTemplateBrowserComponent extends RepositoryManager
{
    private $action_bar;
    private $form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->action_bar = $this->get_action_bar();
        $this->form = new RepositoryFilterForm($this, $this->get_url());

        $trail = BreadcrumbTrail :: get_instance();

        $output = $this->get_table_html();

        $session_filter = Session :: retrieve('filter');

        if ($session_filter != null && ! $session_filter == 0)
        {
            if (is_numeric($session_filter))
            {
                $condition = new EqualityCondition(UserView :: PROPERTY_ID, $session_filter);
                $user_view = RepositoryDataManager :: get_instance()->retrieve_user_views($condition)->next_result();
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Filter', null, Utilities :: COMMON_LIBRARIES) . ': ' . $user_view->get_name()));
            }
            else
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Filter', null, Utilities :: COMMON_LIBRARIES) . ': ' . Utilities :: underscores_to_camelcase(($session_filter))));
        }

        $this->display_header($trail);
        echo $this->action_bar->as_html();
        echo '<br />' . $this->form->display() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_table_html()
    {
        $condition = $this->get_condition();
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = new TemplateBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, 0);

        $cond = $this->form->get_filter_conditions();
        if ($cond)
        {
            $conditions[] = $cond;
        }

        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');

            $conditions[] = new OrCondition($or_conditions);
        }

        $types = RepositoryDataManager :: get_active_helper_types();

        foreach ($types as $type)
        {
            $conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type));
        }

        return new AndCondition($conditions);

    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_template_browser');
    }

    function is_allowed_to_create($type)
    {
        return true;
    }

}
?>