<?php
/**
 * $Id: forum_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.forum.component
 */
require_once dirname(__FILE__) . '/../forum_display.class.php';

require_once 'HTML/Table.php';

class ForumDisplayForumViewerComponent extends ForumDisplay
{
    private $action_bar;
    private $forums;
    private $topics;

    function run()
    {
        if (!$this->get_complex_content_object_item())
        {
            $forum = $this->get_root_content_object();
        }
        else
        {
            $rdm = RepositoryDataManager :: get_instance();
            $forum = $rdm->retrieve_content_object($this->get_complex_content_object_item()->get_ref());
        }
        $this->retrieve_children($forum);
        
        $this->action_bar = $this->get_action_bar();
        $topics_table = $this->get_topics_table_html();
        $forum_table = $this->get_forums_table_html();
        
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(array()), $forum->get_title()));
        
        if ($this->get_complex_content_object_item())
        {
            $forums = $this->retrieve_children_trail($forum);
            while ($forums)
            {
                $forum = $forums[0]->get_ref();
                
                if ($forums[0]->get_id() != $this->get_complex_content_object_item_id())
                {
                    $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $forums[0]->get_id())), $forum->get_title()));
                    $forums = $this->retrieve_children_trail($forum);
                }
                else
                {
                    $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $forums[0]->get_id())), $forum->get_title()));
                    $forums = null;
                }
            }
        }
        
        //$this->display_header($trail);
        $this->display_header($this->get_complex_content_object_breadcrumbs());
        echo $this->action_bar->as_html();
        echo $topics_table->toHtml();
        
        if (count($this->forums) > 0)
        {
        	echo '<br /><br />';
        	echo $forum_table->toHtml();
        }
        
        $this->display_footer();
    }

    private function retrieve_children_trail($forum)
    {
        $rdm = RepositoryDataManager :: get_instance();
        
        $children = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $forum->get_id(), ComplexContentObjectItem :: get_table_name()));
        while ($child = $children->next_result())
        {
            $lo = $rdm->retrieve_content_object($child->get_ref());
            $child->set_ref($lo);
            if ($lo->get_type() != ForumTopic :: get_type_name())
            {
                $forums[] = $child;
            }
        }
        
        return $forums;
    }

    function retrieve_children($current_forum)
    {
        $rdm = RepositoryDataManager :: get_instance();
        
        $order_property[] = new ObjectTableOrder('add_date');
        $children = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $current_forum->get_id(), ComplexContentObjectItem :: get_table_name()), $order_property);
        while ($child = $children->next_result())
        {
            $lo = $rdm->retrieve_content_object($child->get_ref());
            $child->set_ref($lo);
            if ($lo->get_type() == ForumTopic :: get_type_name())
            {
                $this->topics[] = $child;
            }
            else
            {
                $this->forums[] = $child;
            }
        }
        
        $this->sort_topics();
    }

    private function sort_topics()
    {
        $sorted_array = array();
        foreach ($this->topics as $key => $value)
        {
            $type = ($value->get_type()) ? $value->get_type() : 100;
            $sorted_array[$type][] = $value;
        }
        
        ksort($sorted_array);
        
        $array = array();
        foreach ($sorted_array as $key => $value)
        {
            foreach ($value as $key2 => $value2)
            {
                $array[] = $value2;
            }
        }
        
        $this->topics = $array;
    }

    function get_topics_table_html()
    {
        $table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 1));
        
        $this->create_topics_table_header($table);
        $row = 2;
        $this->create_topics_table_content($table, $row);
        $this->create_topics_table_footer($table, $row);
        
        return $table;
    }

    function create_topics_table_header($table)
    {
        $table->setCellContents(0, 0, '<b>' . Translation :: get('Topics') . '</b>');
        $table->setCellAttributes(0, 0, array('colspan' => 7, 'class' => 'category'));
        
        $table->setHeaderContents(1, 0, Translation :: get('Topics'));
        $table->setCellAttributes(1, 0, array('colspan' => 2));
        $table->setHeaderContents(1, 2, Translation :: get('Author'));
        $table->setCellAttributes(1, 2, array('width' => 130));
        $table->setHeaderContents(1, 3, Translation :: get('Replies'));
        $table->setCellAttributes(1, 3, array('width' => 50));
        $table->setHeaderContents(1, 4, Translation :: get('Views'));
        $table->setCellAttributes(1, 4, array('width' => 50));
        $table->setHeaderContents(1, 5, Translation :: get('LastPost'));
        $table->setCellAttributes(1, 5, array('width' => 140));
        $table->setHeaderContents(1, 6, '');
        $table->setCellAttributes(1, 6, array('width' => 60));
    }

    function create_topics_table_footer($table, $row)
    {
        $table->setCellContents($row, 0, '');
        $table->setCellAttributes($row, 0, array('colspan' => 7, 'class' => 'category'));
    }

    function create_topics_table_content($table, &$row)
    {
        $udm = UserDataManager :: get_instance();
        $rdm = RepositoryDataManager :: get_instance();
        
        if (count($this->topics) == 0)
        {
            $table->setCellAttributes($row, 0, array('colspan' => 7, 'style' => 'text-align: center; padding-top: 10px;'));
            $table->setCellContents($row, 0, '<h3>' . Translation :: get('NoTopics') . '</h3>');
            $row ++;
            return;
        }
        
        foreach ($this->topics as $topic)
        {
            $title = '<a href="' . $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_VIEW_TOPIC, 
            											ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $topic->get_id())) . '">' . $topic->get_ref()->get_title() . '</a>';
            
            $count = $rdm->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $topic->get_ref()->get_id(), ComplexContentObjectItem :: get_table_name()));
            $last_post = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $topic->get_ref()->get_id(), ComplexContentObjectItem :: get_table_name()), array(new ObjectTableOrder(ComplexContentObjectItem :: PROPERTY_ADD_DATE, SORT_DESC)), 0, 1)->next_result();
            
            $src = 'forum/topic_read.png';
            $hover = 'NoNewPosts';
            switch ($topic->get_type())
            {
                case 1 :
                    $src = 'forum/sticky_read.gif';
                    $hover = 'Sticky';
                    break;
                case 2 :
                    $src = 'forum/important_read.gif';
                    $hover = 'Important';
                    break;
            }
            $table->setCellContents($row, 0, '<img title="' . Translation :: get($hover) . '" src="' . Theme :: get_image_path() . $src . '"/>');
            $table->setCellAttributes($row, 0, array('width' => 25, 'class' => 'row1', 'style' => 'height: 30px;'));
            $table->setCellContents($row, 1, $title);
            $table->setCellAttributes($row, 1, array('class' => 'row1'));
            $table->setCellContents($row, 2, $udm->retrieve_user($topic->get_user_id())->get_fullname());
            $table->setCellAttributes($row, 2, array('align' => 'center', 'class' => 'row2'));
            $table->setCellContents($row, 3, ($count > 0) ? $count - 1 : $count);
            $table->setCellAttributes($row, 3, array('align' => 'center', 'class' => 'row1'));
            
            $conditions[] = new EqualityCondition('publication_id', $this->pid);
            $conditions[] = new EqualityCondition('forum_topic_id', $topic->get_id());
            $condition = new AndCondition($conditions);
            
            $views = TrackingDataManager :: get_instance()->count_tracker_items('weblcms_forum_topic_views_tracker', $condition);
            
            $table->setCellContents($row, 4, $views);
            $table->setCellAttributes($row, 4, array('align' => 'center', 'class' => 'row2'));
            
            if ($last_post)
            {
                $link = $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_VIEW_TOPIC, 'pid' => $this->pid, 'cid' => $topic->get_id())) . '#post_' . $last_post->get_id();
                $table->setCellContents($row, 5, DatetimeUtilities :: format_locale_date(null, $last_post->get_add_date()) . '<br />' . $udm->retrieve_user($last_post->get_user_id())->get_fullname() . ' <a href="' . $link . '"><img title="' . Translation :: get('ViewLastPost') . '" src="' . Theme :: get_image_path() . 'forum/icon_topic_latest.gif" /></a>');
            }
            else
            {
                $table->setCellContents($row, 5, '-');
            }
            
            $table->setCellAttributes($row, 5, array('align' => 'center', 'class' => 'row1'));
            $table->setCellContents($row, 6, $this->get_topic_actions($topic));
            $table->setCellAttributes($row, 6, array('align' => 'center', 'class' => 'row1'));
            $row ++;
        }
    }

    function get_topic_actions($topic)
    {
    	$tool_bar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
    	$parameters = array();
    	$parameters[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id(); 
        $parameters[ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $topic->get_id();
        
        if ($this->get_parent()->is_allowed(DELETE_RIGHT))
        {
        	$parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_DELETE_TOPIC;
        	
        	$tool_bar->add_item(new ToolbarItem(Translation :: get('Delete'), 
        		Theme :: get_common_image_path() . 'action_delete.png', 
        		$this->get_url($parameters),
        		ToolbarItem :: DISPLAY_ICON,       		
        		true
        	));
        }
        
        if ($this->get_parent()->is_allowed(EDIT_RIGHT))
        { 
         	if ($topic->get_type() == 1)
        	{
        		$parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_MAKE_STICKY;
        		
        		$tool_bar->add_item(new ToolbarItem(Translation :: get('UnSticky'), 
        			Theme :: get_common_image_path() . 'action_remove_sticky.png', 
        			$this->get_url($parameters),
        			ToolbarItem :: DISPLAY_ICON	
        		));
        			
	            $tool_bar->add_item(new ToolbarItem(Translation :: get('ImportantNa'), 
        			Theme :: get_common_image_path() . 'action_make_important_na.png',
        			null,
        			ToolbarItem :: DISPLAY_ICON
        		));
	        }
	        else
	        {
	            if ($topic->get_type() == 2)
	            {
	            	$parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_MAKE_IMPORTANT;
	            	
	            	$tool_bar->add_item(new ToolbarItem(Translation :: get('StickyNa'), 
        				Theme :: get_common_image_path() . 'action_make_sticky_na.png',
        				null,
        				ToolbarItem :: DISPLAY_ICON
        			));

	                $tool_bar->add_item(new ToolbarItem(Translation :: get('UnImportant'), 
        				Theme :: get_common_image_path() . 'action_remove_important.png', 
        				$this->get_url($parameters),
        				ToolbarItem :: DISPLAY_ICON
        			));
	            }
	            else
	            {
	            	$parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_MAKE_STICKY;
	            	$tool_bar->add_item(new ToolbarItem(Translation :: get('MakeSticky'), 
        				Theme :: get_common_image_path() . 'action_make_sticky.png', 
        				$this->get_url($parameters),
        				ToolbarItem :: DISPLAY_ICON
        			));

        			$parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_MAKE_IMPORTANT;
        			$tool_bar->add_item(new ToolbarItem(Translation :: get('MakeImportant'), 
        				Theme :: get_common_image_path() . 'action_make_important.png', 
        				$this->get_url($parameters),
        				ToolbarItem :: DISPLAY_ICON
        			));
	            }
        	}
        }
        
        return $tool_bar->as_html();
    }

    function get_forums_table_html()
    {
        $table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 1));
        
        $this->create_forums_table_header($table);
        $row = 2;
        $this->create_forums_table_content($table, $row);
        $this->create_forums_table_footer($table, $row);
        
        return $table;
    }

    function create_forums_table_header($table)
    {
        $table->setCellContents(0, 0, '<b>' . Translation :: get('Subforums') . '</b>');
        $table->setCellAttributes(0, 0, array('colspan' => 6, 'class' => 'category'));
        
        $table->setHeaderContents(1, 0, Translation :: get('Forum'));
        $table->setCellAttributes(1, 0, array('colspan' => 2));
        $table->setHeaderContents(1, 2, Translation :: get('Topics'));
        $table->setCellAttributes(1, 2, array('width' => 50));
        $table->setHeaderContents(1, 3, Translation :: get('Posts'));
        $table->setCellAttributes(1, 3, array('width' => 50));
        $table->setHeaderContents(1, 4, Translation :: get('LastPost'));
        $table->setCellAttributes(1, 4, array('width' => 140));
        $table->setHeaderContents(1, 5, '');
        $table->setCellAttributes(1, 5, array('width' => 40));
    }

    function create_forums_table_footer($table, $row)
    {
        $table->setCellContents($row, 0, '');
        $table->setCellAttributes($row, 0, array('colspan' => 6, 'class' => 'category'));
    }

    function create_forums_table_content($table, &$row)
    {
        if (count($this->forums) == 0)
        {
            $table->setCellAttributes($row, 0, array('colspan' => 6, 'style' => 'text-align: center; padding-top: 10px;'));
            $table->setCellContents($row, 0, '<h3>' . Translation :: get('NoSubforums') . '</h3>');
            $row ++;
            return;
        }
        foreach ($this->forums as $forum)
        {
            $last_post = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($forum->get_ref()->get_last_post());
            $udm = UserDataManager :: get_instance();
            $title = '<a href="' . $this->get_url(array(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $forum->get_id())) . '">' . $forum->get_ref()->get_title() . '</a><br />' . strip_tags($forum->get_ref()->get_description());
            
            $table->setCellContents($row, 0, '<img title="' . Translation :: get('NoNewPosts') . '" src="' . Theme :: get_image_path() . 'forum/forum_read.png" />');
            $table->setCellAttributes($row, 0, array('width' => 50, 'class' => 'row1', 'style' => 'height:50px;'));
            $table->setCellContents($row, 1, $title);
            $table->setCellAttributes($row, 1, array('class' => 'row1'));
            $table->setCellContents($row, 2, $forum->get_ref()->get_total_topics());
            $table->setCellAttributes($row, 2, array('class' => 'row2', 'align' => 'center'));
            $table->setCellContents($row, 3, $forum->get_ref()->get_total_posts());
            $table->setCellAttributes($row, 3, array('class' => 'row2', 'align' => 'center'));
            if ($last_post)
            {
                //$link = $this->get_url(array(ComplexDisplay::PARAM_DISPLAY_ACTION => ForumDisplay::ACTION_VIEW_TOPIC,'pid' => $this->pid, 'cid' => $topic->get_id())) . '#post_' . $last_post->get_id();
                $table->setCellContents($row, 4, DatetimeUtilities :: format_locale_date(null,$last_post->get_add_date()) . '<br />' . $udm->retrieve_user($last_post->get_user_id())->get_fullname()); // .
            //' <a href="' . $link . '"><img title="' . Translation :: get('ViewLastPost') .
            //'" src="' . Theme :: get_image_path() . 'forum/icon_topic_latest.gif" /></a>');
            }
            else
            {
                $table->setCellContents($row, 4, '-');
            }
            $table->setCellAttributes($row, 4, array('align' => 'center', 'class' => 'row2'));
            $table->setCellContents($row, 5, $this->get_forum_actions($forum, true, true));
            $table->setCellAttributes($row, 5, array('class' => 'row2'));
            $row ++;
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('NewTopic'), Theme :: get_common_image_path() . 'action_add.png', 
        		$this->get_url(array(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
        							 ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_CREATE_TOPIC)), 
        		ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if ($this->is_allowed(ADD_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('NewSubForum'), Theme :: get_common_image_path() . 'action_add.png', 
            	$this->get_url(array(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
            						 ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_CREATE_SUBFORUM)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        return $action_bar;
    }

    function get_forum_actions($forum, $first, $last)
    {
    	$tool_bar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
    	
    	$parameters = array();
    	$parameters[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id(); 
        $parameters[ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $forum->get_id();
    	
        if ($this->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_DELETE_SUBFORUM;
        	$delete = new ToolbarItem(Translation :: get('Delete'),
            	Theme :: get_common_image_path() . 'action_delete.png',
            	$this->get_url($parameters), 
            	ToolbarItem :: DISPLAY_ICON,
            	true
            );
        }
        
        if ($this->get_parent()->is_allowed(EDIT_RIGHT))
        {
            
            /*if($first)
            {
                $actions[] = array(
                    'label' => Translation :: get('MoveUpNA'),
                    'img' => Theme :: get_common_image_path() . 'action_up_na.png'
                );
            }
            else
            {
                $actions[] = array(
                    'href' => $this->get_url(array('subforum' => $forum->get_id(), Tool :: PARAM_ACTION => ForumTool :: ACTION_MOVE_SUBFORUM, Tool :: PARAM_MOVE => -1)),
                    'label' => Translation :: get('MoveUp'),
                    'img' => Theme :: get_common_image_path() . 'action_up.png'
                );
            }

            if($last)
            {
                $actions[] = array(
                    'label' => Translation :: get('MoveDownNA'),
                    'img' => Theme :: get_common_image_path() . 'action_down_na.png'
                );
            }
            else
            {
                $actions[] = array(
                    'href' => $this->get_url(array('subforum' => $forum->get_id(), Tool :: PARAM_ACTION => ForumTool :: ACTION_MOVE_SUBFORUM, Tool :: PARAM_MOVE => 1)),
                    'label' => Translation :: get('MoveDown'),
                    'img' => Theme :: get_common_image_path() . 'action_down.png'
                );
            }*/
            
        	$parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_EDIT_SUBFORUM;
            	$tool_bar->add_item( new ToolbarItem(Translation :: get('Edit'), 
            	Theme :: get_common_image_path() . 'action_edit.png',
            	$this->get_url($parameters),
            	ToolbarItem :: DISPLAY_ICON
            ));
            
            $tool_bar->add_item($delete);
        
        }
        return $tool_bar->as_html();
    }
}
?>