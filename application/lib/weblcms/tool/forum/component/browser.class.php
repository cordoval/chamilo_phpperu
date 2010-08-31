<?php
/**
 * $Id: forum_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.forum.component
 */
require_once dirname(__FILE__) . '/../forum_tool.class.php';

require_once Path :: get_repository_path() . '/lib/content_object/forum/forum.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/forum/display/forum_display.class.php';
require_once 'HTML/Table.php';

class ForumToolBrowserComponent extends ForumTool
{
    private $action_bar;
    private $introduction_text;
    private $size; //Number of published forums

    
    function run()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'forum');

        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Introduction :: get_type_name());
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        $condition = new AndCondition($conditions);

        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications($condition);
        $this->introduction_text = $publications->next_result();
        
        $this->size = 0;
        $this->allowed = $this->is_allowed(WeblcmsRights :: DELETE_RIGHT) || $this->is_allowed(WeblcmsRights :: EDIT_RIGHT);
        $this->action_bar = $this->get_action_bar();
        
        $table = $this->get_table_html();
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses forum tool');
        $this->display_header();
        
   		if ($this->get_course()->get_intro_text())
        {
            echo $this->display_introduction_text($this->introduction_text);
        }
        
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
        if ($this->allowed)
        {
            $table->setCellAttributes(0, 0, array('colspan' => 6, 'class' => 'category'));
        }
        else
        {
            $table->setCellAttributes(0, 0, array('colspan' => 5, 'class' => 'category'));
        }
        
        $table->setHeaderContents(1, 0, Translation :: get('Forum'));
        $table->setCellAttributes(1, 0, array('colspan' => 2));
        $table->setHeaderContents(1, 2, Translation :: get('Topics'));
        $table->setCellAttributes(1, 2, array('width' => 50));
        $table->setHeaderContents(1, 3, Translation :: get('Posts'));
        $table->setCellAttributes(1, 3, array('width' => 50));
        $table->setHeaderContents(1, 4, Translation :: get('LastPost'));
        $table->setCellAttributes(1, 4, array('width' => 130));
        
        if ($this->allowed)
        {
            $table->setHeaderContents(1, 5, '');
            $table->setCellAttributes(1, 5, array('width' => 145));
        }
    }

    function create_table_categories($table, &$row)
    {
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $this->get_parent()->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_TOOL, $this->get_parent()->get_tool_id());
        $condition = new AndCondition($conditions);
        
        $categories = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication_categories($condition);
        
        while ($category = $categories->next_result())
        {
            if($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
            {
            	$item = new ToolbarItem(
	        		Translation :: get('ManageRights'),
	        		Theme :: get_common_image_path() . 'action_rights.png',
	        		$this->get_url(array(WeblcmsManager :: PARAM_CATEGORY => $category->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_RIGHTS)),
	        		ToolbarItem :: DISPLAY_ICON
	        	);	
	        	$actions = $item->as_html();
            }
            
        	$table->setCellContents($row, 0, '<a href="javascript:void();">' . $category->get_name() . '</a> ' . $actions);
            $table->setCellAttributes($row, 0, array('colspan' => 2, 'class' => 'category'));
            $table->setCellContents($row, 2, '');
            
            if ($this->allowed)
            {
                $table->setCellAttributes($row, 2, array('colspan' => 4, 'class' => 'category_right'));
            }
            else
            {
                $table->setCellAttributes($row, 2, array('colspan' => 3, 'class' => 'category_right'));
            }
            
            $row ++;
            $this->create_table_forums($table, $row, $category->get_id());
        }
    
    }

    function create_table_forums($table, &$row, $parent)
    {
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $user_id = array();
            $course_group_ids = array();
        }
        else
        {
        	$user_id = $this->get_user_id();
            $course_groups = $this->get_course_groups();
                
            $course_group_ids = array();
                
            foreach($course_groups as $course_group)
            {
              	$course_group_ids[] = $course_group->get_id();
            }
        }
        
        $datamanager = WeblcmsDataManager :: get_instance();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'forum');
        $conditions[] = new InCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $parent);
        
        /*$access = array();
        $access[] = new InCondition('user_id', $user_id, $datamanager->get_alias('content_object_publication_user'));
        $access[] = new InCondition('course_group_id', $course_group_ids, $datamanager->get_alias('content_object_publication_course_group'));
        if (! empty($user_id) || ! empty($course_group_ids))
        {
            $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $datamanager->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_alias('content_object_publication_course_group'))));
        }
        $conditions[] = new OrCondition($access);*/
        
        $access = array();
        if($user_id)
        {
    		$access[] = new InCondition(ContentObjectPublicationUser :: PROPERTY_USER, $user_id, ContentObjectPublicationUser :: get_table_name());
        }
    	
    	if(count($course_group_ids) > 0)
    	{
        	$access[] = new InCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, $course_group_ids, ContentObjectPublicationCourseGroup :: get_table_name());
    	}
        	
        if (! empty($user_id) || ! empty($course_group_ids))
        {
            $access[] = new AndCondition(array(
            			new EqualityCondition(ContentObjectPublicationUser :: PROPERTY_USER, null, ContentObjectPublicationUser :: get_table_name()), 
            			new EqualityCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, null, ContentObjectPublicationCourseGroup :: get_table_name())));
        }
        
        $conditions[] = new OrCondition($access);
        
        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Forum :: get_type_name());
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        $condition = new AndCondition($conditions);
        
        $publications = $datamanager->retrieve_content_object_publications($condition, new ObjectTableOrder(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_ASC));
        
        $size = $publications->size();
        $this->size += $size;
        
        $counter = 0;
        while ($publication = $publications->next_result())
        {
            $first = $counter == 0 ? true : false;
            $last = $counter == ($size - 1) ? true : false;
            
            //$forum = $rdm->retrieve_content_object($publication->get_id(), 'forum');
            $forum = $publication->get_content_object();
            $title = '<a href="' . $this->get_url(array(Tool :: PARAM_ACTION => ForumTool :: ACTION_VIEW_FORUM, ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_VIEW_FORUM, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())) . '">' . $forum->get_title() . '</a><br />' . strip_tags($forum->get_description());
            
            if ($publication->is_hidden())
            {
                $title = '<span style="color: grey;">' . $title . '</span>';
            }
            
            $last_post = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($publication->get_content_object()->get_last_post());
            
            $table->setCellContents($row, 0, '<img title="' . Translation :: get('NoNewPosts') . '" src="' . Theme :: get_image_path() . 'forum/forum_read.png" />');
            $table->setCellAttributes($row, 0, array('width' => 50, 'class' => 'row1', 'style' => 'height:50px;'));
            $table->setCellContents($row, 1, $title);
            $table->setCellAttributes($row, 1, array('width' => '0%', 'class' => 'row1'));
            $table->setCellContents($row, 2, $forum->get_total_topics());
            $table->setCellAttributes($row, 2, array('class' => 'row2', 'align' => 'center'));
            $table->setCellContents($row, 3, $forum->get_total_posts());
            $table->setCellAttributes($row, 3, array('class' => 'row2', 'align' => 'center'));
            
            if ($last_post)
            {
                //$link = $this->get_url(array(ComplexDisplay::PARAM_DISPLAY_ACTION => ForumDisplay::ACTION_VIEW_TOPIC,Tool :: PARAM_PUBLICATION_ID => $this->pid, 'cid' => $last_post->get_id())) . '#post_' . $last_post->get_id();
                $table->setCellContents($row, 4, DatetimeUtilities :: format_locale_date(null,$last_post->get_add_date()) . '<br />' . UserDataManager :: get_instance()->retrieve_user($last_post->get_user_id())->get_fullname()); // .
            //' <a href="' . $link . '"><img title="' . Translation :: get('ViewLastPost') .
            //'" src="' . Theme :: get_image_path() . 'forum/icon_topic_latest.gif" /></a>');
            }
            else
            {
                $table->setCellContents($row, 4, '-');
            }
            
            $table->setCellAttributes($row, 4, array('align' => 'center', 'class' => 'row2'));
            
            if ($this->allowed)
            {
                $table->setCellContents($row, 5, $this->get_forum_actions($publication, $first, $last));
                $table->setCellAttributes($row, 5, array('class' => 'row2'));
            }
            
            $row ++;
            $counter ++;
        }
    }

    function get_forum_actions($publication, $first, $last)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            if ($publication->is_hidden())
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('Show'),
		        		Theme :: get_common_image_path() . 'action_invisible.png',
		        		$this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY)),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('Hide'),
		        		Theme :: get_common_image_path() . 'action_visible.png',
		        		$this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY)),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
            
            if ($first)
            {
                $actions[] = array('label' => Translation :: get('MoveUpNA'), 'img' => Theme :: get_common_image_path() . 'action_up_na.png');
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('MoveUpNA'),
		        		Theme :: get_common_image_path() . 'action_up_na.png',
		        		null,
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('MoveUp'),
		        		Theme :: get_common_image_path() . 'action_up.png',
		        		$this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_UP)),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
            
            if ($last)
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('MoveDownNA'),
		        		Theme :: get_common_image_path() . 'action_down_na.png',
		        		null,
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('MoveDown'),
		        		Theme :: get_common_image_path() . 'action_down.png',
		        		$this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_DOWN)),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
            
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Move'),
	        		Theme :: get_common_image_path() . 'action_move.png',
	        		$this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_TO_CATEGORY)),
	        		ToolbarItem :: DISPLAY_ICON
	        ));
            
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Edit'),
	        		Theme :: get_common_image_path() . 'action_edit.png',
	        		$this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_UPDATE)),
	        		ToolbarItem :: DISPLAY_ICON
	        ));
	        
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Delete'),
	        		Theme :: get_common_image_path() . 'action_delete.png',
	        		$this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_DELETE)),
	        		ToolbarItem :: DISPLAY_ICON,
	        		true
	        ));
	        
	        $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('ManageRights'),
	        		Theme :: get_common_image_path() . 'action_rights.png',
	        		$this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_RIGHTS)),
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        
        }
    
        if(WebApplication :: is_active('gradebook'))
        {
        	require_once dirname (__FILE__) . '/../../../../gradebook/evaluation_manager/evaluation_manager.class.php';
        	$internal_item = EvaluationManager :: retrieve_internal_item_by_publication(WeblcmsManager :: APPLICATION_NAME, $publication->get_id());
        	if($internal_item && $internal_item->get_calculated() != 1)
        	{
        		$evaluate_url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EVALUATE_TOOL_PUBLICATION, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()));
				$toolbar->add_item(new ToolbarItem(
		        		Translation :: get('Evaluate'),
		        		Theme :: get_common_image_path() . 'action_evaluation.png',
		        		$evaluate_url,
		        		ToolbarItem :: DISPLAY_ICON
		        ));
        	}
        }
        
        return '<div style="float: right;">' . $toolbar->as_html() . '</div>';
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        if ($this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_RIGHTS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(Tool :: PARAM_ACTION => ForumTool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
    	if (! $this->introduction_text && $this->get_course()->get_intro_text() && $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        //		if(!$this->introduction_text && $this->get_course()->get_intro_text())
        //		{
        //			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //		}
        

        return $action_bar;
    }
}
?>