<?php

namespace application\profiler;

use common\libraries\WebApplication;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\PatternMatchCondition;
use user\User;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use common\libraries\BreadcrumbTrail;

/**
 * $Id: browser.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('profiler') . 'profiler_manager/component/profile_publication_browser/profile_publication_browser_table.class.php';

class ProfilerManagerBrowserComponent extends ProfilerManager
{

    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $menu = new ProfilerMenu($this->get_category());

        $this->action_bar = $this->get_action_bar();

        $output = $this->get_publications_html();

        $this->display_header();

        echo $this->action_bar->as_html();
        echo '<div class="clear"></div>';

        echo '<div style="width: 12%; overflow: auto; float: left;">';
        echo $this->get_menu();
        echo '</div><div style="width: 85%; float: right;">';
        echo $output;
        echo '</div>';

        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array('category' => $this->get_category())));

        //the root has a different type then a specific category!
        if (!$this->get_category())
        {
            $RIGHT_PUBLISH = ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::RIGHT_PUBLISH, 0, 0);
            $RIGHT_EDIT_RIGHTS = ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::RIGHT_EDIT_RIGHTS, 0, 0);
        }
        else
        {
            $RIGHT_PUBLISH = ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::RIGHT_PUBLISH, $this->get_category(), ProfilerRights::TYPE_CATEGORY);
            $RIGHT_EDIT_RIGHTS = ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::RIGHT_EDIT_RIGHTS, $this->get_category(), ProfilerRights::TYPE_CATEGORY);
        }

        if ($RIGHT_PUBLISH)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_profile_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('category' => $this->get_category())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($RIGHT_EDIT_RIGHTS)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_rights_editor_url($this->get_category()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        if ($RIGHT_PUBLISH)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_profiler_category_manager_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }

    function get_menu()
    {
        $menu = new ProfilerMenu($this->get_category());
        return $menu->render_as_tree();
    }

    function get_category()
    {
        return Request :: get('category') ? Request :: get('category') : 0;
    }

    private function get_publications_html()
    {
        $parameters = $this->get_parameters(true);

        $table = new ProfilePublicationBrowserTable($this, null, $parameters, $this->get_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_condition()
    {
        /* $search_conditions = $this->get_search_condition();
          //$search_conditions = null;
          $condition = null;
          if (isset($this->firstletter))
          {
          $conditions = array();
          $conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, $this->firstletter. '*');
          $conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, chr(ord($this->firstletter)+1). '*');
          $conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, chr(ord($this->firstletter)+2). '*');
          $condition = new OrCondition($conditions);
          if (count($search_conditions))
          {
          $condition = new AndCondition($condition, $search_conditions);
          }
          }
          else
          {
          if (count($search_conditions))
          {
          $condition = $search_conditions;
          }
          }

          return $condition; */
        $condition = new EqualityCondition(ProfilerPublication :: PROPERTY_CATEGORY, $this->get_category());
        $search = $this->action_bar->get_query();

        if (isset($search) && $search != '')
        {
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $search . '*');
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $search . '*');
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $search . '*');
            $or_condition = new OrCondition($conditions);

            $conditions = array();
            $conditions[] = $condition;
            $conditions[] = $or_condition;

            $condition = new AndCondition($conditions);
        }

        return $condition;
    }
    
    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('profiler_browser');
    }
    
    function get_additional_parameters()
    {
    	return array('category');
    }

}

?>