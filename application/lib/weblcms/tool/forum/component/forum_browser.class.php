<?php
/**
 * $Id: forum_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.forum.component
 */
require_once dirname(__FILE__) . '/../forum_tool.class.php';
require_once dirname(__FILE__) . '/../forum_tool_component.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/forum/forum.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/forum/forum_display.class.php';
require_once 'HTML/Table.php';

class ForumToolBrowserComponent extends ForumToolComponent
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
        
        $this->size = 0;
        $this->allowed = $this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT);
        $this->action_bar = $this->get_action_bar();
        
        $table = $this->get_table_html();
        
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses forum tool');
        $this->display_header($trail, true);
        
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
            $table->setCellAttributes(1, 5, array('width' => 125));
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
            $table->setCellContents($row, 0, '<a href="javascript:void();">' . $category->get_name() . '</a>');
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
        if ($this->is_allowed(EDIT_RIGHT))
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
        
        $access = array();
        $access[] = new InCondition('user_id', $user_id, $datamanager->get_database()->get_alias('content_object_publication_user'));
        $access[] = new InCondition('course_group_id', $course_group_ids, $datamanager->get_database()->get_alias('content_object_publication_course_group'));
        if (! empty($user_id) || ! empty($course_group_ids))
        {
            $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $datamanager->get_database()->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_database()->get_alias('content_object_publication_course_group'))));
        }
        $conditions[] = new OrCondition($access);
        
        $subselect_condition = new EqualityCondition('type', 'forum');
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
        $condition = new AndCondition($conditions);
        
        $publications = $datamanager->retrieve_content_object_publications_new($condition, new ObjectTableOrder(Forum :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_ASC));
        
        $size = $publications->size();
        $this->size += $size;
        
        $counter = 0;
        while ($publication = $publications->next_result())
        {
            $first = $counter == 0 ? true : false;
            $last = $counter == ($size - 1) ? true : false;
            
            //$forum = $rdm->retrieve_content_object($publication->get_id(), 'forum');
            $forum = $publication->get_content_object();
            $title = '<a href="' . $this->get_url(array(Tool :: PARAM_ACTION => ForumTool :: ACTION_VIEW_FORUM, ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_VIEW_FORUM, Tool :: PARAM_PUBLICATION_ID => $publication->get_content_object()->get_id())) . '">' . $forum->get_title() . '</a><br />' . strip_tags($forum->get_description());
            
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
                //$link = $this->get_url(array(ComplexDisplay::PARAM_DISPLAY_ACTION => ForumDisplay::ACTION_VIEW_TOPIC,'pid' => $this->pid, 'cid' => $last_post->get_id())) . '#post_' . $last_post->get_id();
                $table->setCellContents($row, 4, $last_post->get_add_date() . '<br />' . UserDataManager :: get_instance()->retrieve_user($last_post->get_user_id())->get_fullname()); // .
            //' <a href="' . $link . '"><img title="' . Translation :: get('ViewLastPost') .
            //'" src="' . Theme :: get_image_path() . 'forum/icon_topic_latest.gif" /></a>');
            }
            else
            {
                $table->setCellContents($row, 4, '-');
            }
            
            $table->setCellAttributes($row, 4, array('class' => 'row2'));
            
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
        if ($this->is_allowed(DELETE_RIGHT))
        {
            $delete = array('href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_DELETE)), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
        }
        
        if ($this->is_allowed(EDIT_RIGHT))
        {
            if ($publication->is_hidden())
            {
                $actions[] = array('href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY)), 'label' => Translation :: get('Show'), 'img' => Theme :: get_common_image_path() . 'action_invisible.png');
            }
            else
            {
                $actions[] = array('href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY)), 'label' => Translation :: get('Hide'), 'img' => Theme :: get_common_image_path() . 'action_visible.png');
            }
            
            if ($first)
            {
                $actions[] = array('label' => Translation :: get('MoveUpNA'), 'img' => Theme :: get_common_image_path() . 'action_up_na.png');
            }
            else
            {
                $actions[] = array('href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_MOVE, Tool :: PARAM_MOVE => - 1)), 'label' => Translation :: get('MoveUp'), 'img' => Theme :: get_common_image_path() . 'action_up.png');
            }
            
            if ($last)
            {
                $actions[] = array('label' => Translation :: get('MoveDownNA'), 'img' => Theme :: get_common_image_path() . 'action_down_na.png');
            }
            else
            {
                $actions[] = array('href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_MOVE, Tool :: PARAM_MOVE => 1)), 'label' => Translation :: get('MoveDown'), 'img' => Theme :: get_common_image_path() . 'action_down.png');
            }
            
            $actions[] = array('href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_TO_CATEGORY)), 'label' => Translation :: get('Move'), 'img' => Theme :: get_common_image_path() . 'action_move.png');
            
            $actions[] = array('href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_EDIT)), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
            
            $actions[] = $delete;
        
        }
        
        return '<div style="float: right;">' . Utilities :: build_toolbar($actions) . '</div>';
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        if ($this->is_allowed(ADD_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        //		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
        //		{
        //			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //		}
        

        return $action_bar;
    }
}
?>