<?php
/**
 * $Id: browser.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager.component
 */
require_once dirname(__FILE__) . '/../forum_manager.class.php';
require_once dirname(__FILE__) . '/../../forum_publication.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/forum/forum.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/forum/display/forum_display.class.php';

require_once 'HTML/Table.php';

class ForumManagerBrowserComponent extends ForumManager
{
    private $action_bar;
    private $introduction_text;
    private $size; //Number of published forums

    
    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        $this->action_bar = $this->get_action_bar();
        
        $table = $this->get_table_html();
        
        $this->display_header();
        
        echo $this->action_bar->as_html();
        echo $table->toHtml();
        
        if ($this->size == 0)
            echo '<br><div style="text-align: center"><h3>' . Translation :: get('NoPublications') . '</h3></div>';
        
        $this->display_footer();
    }

    function get_table_html()
    {
        $table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 1));
        
        $this->create_table_header($table);
        $row = 2;
        $this->create_table_forums($table, $row, 0);
        $this->create_table_categories($table, $row);
        
        return $table;
    }

    function create_table_header($table)
    {
        $table->setCellContents(0, 0, '');
        $table->setCellAttributes(0, 0, array('colspan' => 6, 'class' => 'category'));
        
        $table->setHeaderContents(1, 0, Translation :: get('Forum'));
        $table->setCellAttributes(1, 0, array('colspan' => 2));
        $table->setHeaderContents(1, 2, Translation :: get('Topics'));
        $table->setCellAttributes(1, 2, array('width' => 50));
        $table->setHeaderContents(1, 3, Translation :: get('Posts'));
        $table->setCellAttributes(1, 3, array('width' => 50));
        $table->setHeaderContents(1, 4, Translation :: get('LastPost'));
        $table->setCellAttributes(1, 4, array('width' => 130));
        $table->setHeaderContents(1, 5, '');
        $table->setCellAttributes(1, 5, array('width' => 125));
    }

    function create_table_categories($table, &$row)
    {
        $categories = ForumDataManager :: get_instance()->retrieve_forum_publication_categories();
        
        while ($category = $categories->next_result())
        {
            if($this->get_user()->is_platform_admin())
            {
        		$toolbar = new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_rights_editor_url($category->get_id()), ToolbarItem :: DISPLAY_ICON);
            	$rights = $toolbar->as_html();
            }
            
            $table->setCellContents($row, 0, '<a href="javascript:void();">' . $category->get_name() . '</a> ' . $rights);
            $table->setCellAttributes($row, 0, array('colspan' => 2, 'class' => 'category'));
            
            $table->setCellContents($row, 2, '');
            $table->setCellAttributes($row, 2, array('colspan' => 4, 'class' => 'category_right'));
            $row ++;
            $this->create_table_forums($table, $row, $category->get_id());
        }
    
    }

    function create_table_forums($table, &$row, $parent)
    {
        $order[] = new ObjectTableOrder(ForumPublication :: PROPERTY_DISPLAY_ORDER);
        
        $condition = new EqualityCondition(ForumPublication :: PROPERTY_CATEGORY_ID, $parent);
        $publications = $this->retrieve_forum_publications($condition, null, null, $order);
        
        $rdm = RepositoryDataManager :: get_instance();
        $udm = UserDataManager :: get_instance();
        
        $size = $publications->size();
        $this->size += $size;
        
        $counter = 0;
        while ($publication = $publications->next_result())
        {
            $first = $counter == 0 ? true : false;
            $last = $counter == ($size - 1) ? true : false;
            
            $forum = $rdm->retrieve_content_object($publication->get_forum_id(), Forum :: get_type_name());
            $title = '<a href="' . $this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_VIEW, ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_VIEW_FORUM, ForumManager :: PARAM_PUBLICATION_ID => $publication->get_id())) . '">' . $forum->get_title() . '</a><br />' . Utilities :: truncate_string($forum->get_description());
            $last_post = $rdm->retrieve_complex_content_object_item($forum->get_last_post());
            
            if ($publication->is_hidden())
            {
                $title = '<span style="color: grey;">' . $title . '</span>';
            }
            
            $src = Theme :: get_image_path() . 'forum/forum_read.png';
            if($forum->is_locked())
            {
            	$src = Theme :: get_common_image_path() . 'action_lock.png';
            }
            
            $table->setCellContents($row, 0, '<img title="' . Translation :: get('NoNewPosts') . '" src="' . $src . '" />');
            $table->setCellAttributes($row, 0, array('width' => 50, 'class' => 'row1', 'style' => 'height:50px; text-align: center;'));
            $table->setCellContents($row, 1, $title);
            $table->setCellAttributes($row, 1, array('width' => '0%', 'class' => 'row1'));
            $table->setCellContents($row, 2, $forum->get_total_topics());
            $table->setCellAttributes($row, 2, array('class' => 'row2', 'align' => 'center'));
            $table->setCellContents($row, 3, $forum->get_total_posts());
            $table->setCellAttributes($row, 3, array('class' => 'row2', 'align' => 'center'));
            if ($last_post)
            {
                $link = '';
            	//$link = $this->get_url(array(ComplexDisplay::PARAM_DISPLAY_ACTION => ForumDisplay::ACTION_VIEW_TOPIC,'pid' => $this->pid, 'cid' => $topic->get_id())) . '#post_' . $last_post->get_id();
                $table->setCellContents($row, 4, DatetimeUtilities :: format_locale_date(null,$last_post->get_add_date()) . '<br />' . $udm->retrieve_user($last_post->get_user_id())->get_fullname() . ' <a href="' . $link . '"><img title="' . Translation :: get('ViewLastPost') . '" src="' . Theme :: get_image_path() . 'forum/icon_topic_latest.gif" /></a>');
            }
            else
            {
                $table->setCellContents($row, 5, '-');
            }
            //$table->setCellContents($row, 4, $last_post);
            $table->setCellAttributes($row, 4, array('align' => 'center', 'class' => 'row2'));
            $table->setCellContents($row, 5, $this->get_forum_actions($publication, $first, $last));
            $table->setCellAttributes($row, 5, array('class' => 'row2'));
            $row ++;
            $counter ++;
        }
    }

    function get_forum_actions($publication, $first, $last)
    {
    	$toolbar = new Toolbar();
        if ($this->get_user()->is_platform_admin() || $publication->get_author() == $this->get_user_id())
        {
        	$delete = new ToolbarItem(Translation :: get('Delete'), 
        					Theme :: get_common_image_path() . 'action_delete.png', 
        					$this->get_url(array(ForumManager :: PARAM_PUBLICATION_ID => $publication->get_id(), ForumManager :: PARAM_ACTION => ForumManager :: ACTION_DELETE)),
        					ToolbarItem :: DISPLAY_ICON, 
        					true );
        					
            if ($publication->is_hidden())
            {
            	$toolbar->add_item(new ToolbarItem(Translation :: get('Show'), 
        					Theme :: get_common_image_path() . 'action_invisible.png', 
        					$this->get_url(array(ForumManager :: PARAM_PUBLICATION_ID => $publication->get_id(), ForumManager :: PARAM_ACTION => ForumManager :: ACTION_TOGGLE_VISIBILITY)),
        					ToolbarItem :: DISPLAY_ICON
        					));
            }
            else
            {
            	$toolbar->add_item(new ToolbarItem(Translation :: get('Hide'), 
        					Theme :: get_common_image_path() . 'action_visible.png', 
        					$this->get_url(array(ForumManager :: PARAM_PUBLICATION_ID => $publication->get_id(), ForumManager :: PARAM_ACTION => ForumManager :: ACTION_TOGGLE_VISIBILITY)),
        					ToolbarItem :: DISPLAY_ICON
        					));
            }
            
            if ($first)
            {
            	$toolbar->add_item(new ToolbarItem(Translation :: get('MoveUpNA'), 
        					Theme :: get_common_image_path() . 'action_up_na.png', 
        					null,
        					ToolbarItem :: DISPLAY_ICON
        					));
            }
            else
            {
            	$toolbar->add_item(new ToolbarItem(Translation :: get('MoveUp'), 
        					Theme :: get_common_image_path() . 'action_up.png', 
        					$this->get_url(array(ForumManager :: PARAM_PUBLICATION_ID => $publication->get_id(), ForumManager :: PARAM_ACTION => ForumManager :: ACTION_MOVE, ForumManager :: PARAM_MOVE => - 1)),
        					ToolbarItem :: DISPLAY_ICON
        					));
            }
            
            if ($last)
            {
            	$toolbar->add_item(new ToolbarItem(Translation :: get('MoveDownNA'), 
        					Theme :: get_common_image_path() . 'action_down_na.png', 
        					null,
        					ToolbarItem :: DISPLAY_ICON
        					));
            }
            else
            {
            	$toolbar->add_item(new ToolbarItem(Translation :: get('MoveDown'), 
        					Theme :: get_common_image_path() . 'action_down.png', 
        					$this->get_url(array(ForumManager :: PARAM_PUBLICATION_ID => $publication->get_id())),
        					ToolbarItem :: DISPLAY_ICON
        					));
               
            }
            
            //			$actions[] = array(
            //				'href' => $this->get_url(array('pid' => $publication->get_id(), ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_MOVE_TO_CATEGORY)),
            //				'label' => Translation :: get('Move'),
            //				'img' => Theme :: get_common_image_path() . 'action_move.png'
            //			);
            
			$toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), 
        					Theme :: get_common_image_path() . 'action_edit.png', 
        					$this->get_url(array(ForumManager :: PARAM_PUBLICATION_ID => $publication->get_id(), ForumManager :: PARAM_ACTION => ForumManager :: ACTION_EDIT)),
        					ToolbarItem :: DISPLAY_ICON
        					));
            
            $toolbar->add_item($delete);
            
            $forum = RepositoryDataManager :: get_instance()->retrieve_content_object($publication->get_forum_id(), Forum :: get_type_name());
        	if($forum->get_locked())
        	{
        		$parameters[ForumManager :: PARAM_ACTION] = ForumManager :: ACTION_CHANGE_LOCK;
        		$parameters[ForumManager :: PARAM_PUBLICATION_ID] = $publication->get_id();
        		$toolbar->add_item(new ToolbarItem(Translation :: get('Unlock'), 
        			Theme :: get_common_image_path() . 'action_unlock.png', 
        			$this->get_url($parameters),
        			ToolbarItem :: DISPLAY_ICON
        		));
        	}
        	else
        	{
        		$parameters[ForumManager :: PARAM_ACTION] = ForumManager :: ACTION_CHANGE_LOCK;
        		$parameters[ForumManager :: PARAM_PUBLICATION_ID] = $publication->get_id();
        		$toolbar->add_item(new ToolbarItem(Translation :: get('Lock'), 
        			Theme :: get_common_image_path() . 'action_lock.png', 
        			$this->get_url($parameters),
        			ToolbarItem :: DISPLAY_ICON
        		));
        	}
            
	        if(WebApplication :: is_active('gradebook'))
	        {
	        	require_once dirname (__FILE__) . '/../../../gradebook/evaluation_manager/evaluation_manager.class.php';
        		if(EvaluationManager :: retrieve_internal_item_by_publication(ForumManager :: APPLICATION_NAME, $publication->get_id()))
        			$toolbar->add_item(new ToolbarItem(Translation :: get('Evaluation'), 
        					Theme :: get_common_image_path() . 'action_evaluation.png', 
        					$this->get_url(array(ForumManager :: PARAM_PUBLICATION_ID => $publication->get_id(), ForumManager :: PARAM_ACTION => ForumManager :: ACTION_EVALUATE)),
        					ToolbarItem :: DISPLAY_ICON
        					));
	        }
        
        }
        
        return '<div style="float: right;">' . $toolbar->as_html() . '</div>';
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_CREATE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path().'action_category.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        

        //		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
        //		{
        //			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //		}
        

        //$action_bar->set_help_action(HelpManager :: get_tool_bar_help_item('general'));
        
		if($this->get_user()->is_platform_admin())
		{
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_category_manager_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_rights_editor_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
        
        return $action_bar;
    }
    
    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('forum_browser');
    }
}
?>