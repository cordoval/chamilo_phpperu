<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */

require_once Path :: get_repository_path() . '/lib/content_object/forum/forum.class.php';
require_once dirname(__FILE__) . '/browser/forum_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/browser/forum_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser/forum_post_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/browser/forum_post_browser_table_column_model.class.php';

class ForumBuilderBrowserComponent extends ForumBuilder
{

    function run()
    {
        
        $browser = ComplexBuilderComponent :: factory(ComplexBuilderComponent::BROWSER_COMPONENT, $this);
        
        $browser->run();
        /*
        $menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail = new BreadcrumbTrail(false);
        $trail->merge($menu_trail);
        $trail->add_help('repository forum builder');
        
        if ($this->get_complex_content_object_item())
        {
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_complex_content_object_item()->get_ref());
        }
        else
        {
            $content_object = $this->get_root_content_object();
        }
        
        $this->display_header($trail);
        $action_bar = $this->get_action_bar($content_object);
        
        echo '<br />';
        if ($action_bar)
        {
            echo $action_bar->as_html();
            echo '<br />';
        }
        
        $display = ContentObjectDisplay :: factory($this->get_root_content_object());
        echo $display->get_full_html();
        
        echo '<br />';
        echo $this->get_creation_links($content_object);
        echo '<div class="clear">&nbsp;</div><br />';
        
        echo $this->get_table_html();

        echo '<div class="clear">&nbsp;</div>';
        
        $this->display_footer();
        */
    }

    function get_table_html()
    {
        /*
        $parameters = array(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => ($this->get_complex_content_object_item() ? $this->get_complex_content_object_item()->get_id() : null));
        
        if ($this->get_complex_content_object_item())
        {
            $parameters[ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item()->get_id();
        }
        
        if(get_class($this->get_complex_content_object_item()) == 'ComplexForumTopic')
        {
        	$conditions = array();
	        $conditions[] = $this->get_complex_content_object_table_condition();
	        $subcondition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, ForumPost :: get_type_name());
	        $conditions[] = new SubSelectcondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'content_object', $subcondition);
	        $condition = new AndCondition($conditions);
	        
	        $html[] = '<h3>' . Translation :: get('Posts') . '</h3>';
	        $table = new ComplexBrowserTable($this->get_parent(), array_merge($this->get_parameters(), $parameters), $condition, true, new ForumPostBrowserTableColumnModel(true), new ForumPostBrowserTableCellRenderer($this->get_parent(), $condition));
	        $html[] = $table->as_html();
        }
        else 
        {
	        $conditions = array();
	        $conditions[] = $this->get_complex_content_object_table_condition();
	        $subcondition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Forum :: get_type_name());
	        $conditions[] = new SubSelectcondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'content_object', $subcondition);
	        $condition = new AndCondition($conditions);
	        
	        $html[] = '<h3>' . Translation :: get('Forums') . '</h3>';
	        $table = new ComplexBrowserTable($this->get_parent(), array_merge($this->get_parameters(), $parameters), $condition, true, null, null, 'forum_table');
	        $html[] = $table->as_html();
	        
	        $conditions = array();
	        $conditions[] = $this->get_complex_content_object_table_condition();
	        $subcondition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, ForumTopic :: get_type_name());
	        $conditions[] = new SubSelectcondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'content_object', $subcondition);
	        $condition = new AndCondition($conditions);
	        
	        $html[] = '<br /><h3>' . Translation :: get('ForumTopics') . '</h3>';
	        $table = new ComplexBrowserTable($this->get_parent(), array_merge($this->get_parameters(), $parameters), $condition, true, new ForumBrowserTableColumnModel(true), new ForumBrowserTableCellRenderer($this->get_parent(), $condition), 'topic_table');
	        $html[] = $table->as_html();
        }
        
        return implode("\n", $html);
        */
    }
}

?>