<?php
namespace repository;
use common\libraries\ActionBarRenderer;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\EqualityCondition;
use admin\Registration;
use common\libraries\AndCondition;
use common\libraries\ActionBarSearchForm;
use common\libraries\BreadcrumbTrail;
use common\libraries\PatternMatchCondition;
use common\libraries\Utilities;
/**
 * $Id: template_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

class RepositoryManagerContentObjectRegistrationBrowserComponent extends RepositoryManager
{
    private $action_bar;
    private $form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->action_bar = $this->get_action_bar();

        $output = $this->get_table_html();
        $this->display_header();
        echo $this->action_bar->as_html();
        echo $output;
        $this->display_footer();
    }

    function get_table_html()
    {
        $condition = $this->get_condition();
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = new ContentObjectRegistrationBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_CONTENT_OBJECT);

        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(Registration :: PROPERTY_NAME, '*' . $query . '*');
        }

        return new AndCondition($conditions);

    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_content_object_type_rights_editing_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('repository_registrations_browser');
    }

}
?>