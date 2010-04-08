<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */

require_once dirname(__FILE__) . '/../forum_builder_component.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/forum/forum.class.php';
require_once dirname(__FILE__) . '/browser/forum_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/browser/forum_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser/forum_post_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/browser/forum_post_browser_table_column_model.class.php';

class ForumBuilderBrowserComponent extends ForumBuilderComponent
{

    function run()
    {
        $menu_trail = $this->get_clo_breadcrumbs();
        $trail = new BreadcrumbTrail(false);
        //$trail->add(new Breadcrumb($this->get_url(array('builder_action' => null, Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('Repository')));
        $trail->merge($menu_trail);
        $trail->add_help('repository forum builder');
        
        if ($this->get_cloi())
        {
            $lo = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_cloi()->get_ref());
        }
        else
        {
            $lo = $this->get_root_lo();
        }
        
        $this->display_header($trail);
        $action_bar = $this->get_action_bar($lo);
        
        echo '<br />';
        if ($action_bar)
        {
            echo $action_bar->as_html();
            echo '<br />';
        }
        
        $display = ContentObjectDisplay :: factory($this->get_root_lo());
        echo $display->get_full_html();
        
        echo '<br />';
        echo $this->get_creation_links($lo);
        echo '<div class="clear">&nbsp;</div><br />';
        
        //echo '<div style="width: 18%; overflow: auto; float: left;">';
        //$this->get_clo_menu();
        //echo '</div><div style="width: 80%; float: right;">';
        echo $this->get_table_html();
        //echo '</div>';
        echo '<div class="clear">&nbsp;</div>';
        
        $this->display_footer();
    }

    function get_table_html()
    {
        $parameters = array(ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), ComplexBuilder :: PARAM_CLOI_ID => ($this->get_cloi() ? $this->get_cloi()->get_id() : null));
        
        if ($this->get_cloi())
        {
            $parameters[ComplexBuilder :: PARAM_CLOI_ID] = $this->get_cloi()->get_id();
        }
        
        if(get_class($this->get_cloi()) == 'ComplexForumTopic')
        {
        	$conditions = array();
	        $conditions[] = $this->get_clo_table_condition();
	        $subcondition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, 'forum_post');
	        $conditions[] = new SubSelectcondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'repository_content_object', $subcondition);
	        $condition = new AndCondition($conditions);
	        
	        $html[] = '<h3>' . Translation :: get('Posts') . '</h3>';
	        $table = new ComplexBrowserTable($this->get_parent(), array_merge($this->get_parameters(), $parameters), $condition, true, new ForumPostBrowserTableColumnModel(true), new ForumPostBrowserTableCellRenderer($this->get_parent(), $condition));
	        $html[] = $table->as_html();
        }
        else 
        {
	        $conditions = array();
	        $conditions[] = $this->get_clo_table_condition();
	        $subcondition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, 'forum');
	        $conditions[] = new SubSelectcondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'repository_content_object', $subcondition);
	        $condition = new AndCondition($conditions);
	        
	        $html[] = '<h3>' . Translation :: get('Forums') . '</h3>';
	        $table = new ComplexBrowserTable($this->get_parent(), array_merge($this->get_parameters(), $parameters), $condition, true, null, null, 'forum_table');
	        $html[] = $table->as_html();
	        
	        $conditions = array();
	        $conditions[] = $this->get_clo_table_condition();
	        $subcondition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, 'forum_topic');
	        $conditions[] = new SubSelectcondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'repository_content_object', $subcondition);
	        $condition = new AndCondition($conditions);
	        
	        $html[] = '<br /><h3>' . Translation :: get('ForumTopics') . '</h3>';
	        $table = new ComplexBrowserTable($this->get_parent(), array_merge($this->get_parameters(), $parameters), $condition, true, new ForumBrowserTableColumnModel(true), new ForumBrowserTableCellRenderer($this->get_parent(), $condition), 'topic_table');
	        $html[] = $table->as_html();
        }
        
        return implode("\n", $html);
    }
}

?>