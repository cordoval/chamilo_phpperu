<?php
require_once dirname(__FILE__) . '/../peer_assessment_display.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_display_component.class.php';
require_once 'HTML/Table.php';

class PeerAssessmentDisplayPeerAssessmentViewerComponent extends PeerAssessmentDisplayComponent
{
    private $action_bar;
    private $peer_assessment;
    private $current_peer_assessment;
    private $is_subpeer_assessment;
    private $peer_assessments;
    private $topics;
    private $pid;

    function run()
    {
        $this->pid = Request :: get('pid');
        $this->peer_assessment = RepositoryDataManager :: get_instance()->retrieve_content_object($this->pid);
        
        $current_id = Request :: get('peer_assessment');
        if (! isset($current_id))
        {
            $this->current_peer_assessment = $this->peer_assessment;
            $this->is_subpeer_assessment = false;
            $this->retrieve_children($this->current_peer_assessment);
        }
        else
        {
            $rdm = RepositoryDataManager :: get_instance();
            $this->current_peer_assessment = $rdm->retrieve_complex_content_object_item($current_id);
            $lo_current_peer_assessment = $rdm->retrieve_content_object($this->current_peer_assessment->get_ref());
            $this->retrieve_children($lo_current_peer_assessment);
            $this->is_subpeer_assessment = true;
        }
        
        $this->action_bar = $this->get_action_bar();
        $topics_table = $this->get_topics_table_html();
        $peer_assessment_table = $this->get_peer_assessments_table_html();
        
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(array('pid' => $this->pid, 'peer_assessment' => null)), $this->peer_assessment->get_title()));
        
        if ($this->is_subpeer_assessment)
        {
            $peer_assessments = $this->retrieve_children_trail($this->peer_assessment);
            while ($peer_assessments)
            {
                $peer_assessment = $peer_assessments[0]->get_ref();
                
                if ($peer_assessments[0]->get_id() != Request :: get('peer_assessment'))
                {
                    $trail->add(new Breadcrumb($this->get_url(array('pid' => $this->pid, 'peer_assessment' => $peer_assessments[0]->get_id())), $peer_assessment->get_title()));
                    $peer_assessments = $this->retrieve_children_trail($peer_assessment);
                }
                else
                {
                    $trail->add(new Breadcrumb($this->get_url(array('pid' => $this->pid, 'peer_assessment' => $peer_assessments[0]->get_id())), $peer_assessment->get_title()));
                    $peer_assessments = null;
                }
            }
        }
        echo $this->action_bar->as_html();
        
        echo '<div id="trailbox2">' . $trail->render() . '</div>';
        echo '<div class="clear"></div><br />';
        echo $topics_table->toHtml();
        echo '<br /><br />';
        
        if (count($this->peer_assessments) > 0)
        {
        	echo $peer_assessment_table->toHtml();
        }
    }

    private function retrieve_children_trail($peer_assessment)
    {
        $rdm = RepositoryDataManager :: get_instance();
        
        $children = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $peer_assessment->get_id(), ComplexContentObjectItem :: get_table_name()));
        while ($child = $children->next_result())
        {
            $lo = $rdm->retrieve_content_object($child->get_ref());
            $child->set_ref($lo);
            if ($lo->get_type() != 'peer_assessment_topic')
            {
                $peer_assessments[] = $child;
            }
        }
        
        return $peer_assessments;
    }

    function retrieve_children($current_peer_assessment)
    {
        $rdm = RepositoryDataManager :: get_instance();
        
        $order_property[] = new ObjectTableOrder('add_date');
        $children = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $current_peer_assessment->get_id(), ComplexContentObjectItem :: get_table_name()), $order_property);
        while ($child = $children->next_result())
        {
            $lo = $rdm->retrieve_content_object($child->get_ref());
            $child->set_ref($lo);
            if ($lo->get_type() == 'peer_assessment_topic')
            {
                $this->topics[] = $child;
            }
            else
            {
                $this->peer_assessments[] = $child;
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
        $table = new HTML_Table(array('class' => 'peer_assessment', 'cellspacing' => 1));
        
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
            $title = '<a href="' . $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_TOPIC, 'pid' => $this->pid, 'cid' => $topic->get_id())) . '">' . $topic->get_ref()->get_title() . '</a>';
            
            $count = $rdm->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $topic->get_ref()->get_id(), ComplexContentObjectItem :: get_table_name()));
            $last_post = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $topic->get_ref()->get_id(), ComplexContentObjectItem :: get_table_name()), array(new ObjectTableOrder(ComplexContentObjectItem :: PROPERTY_ADD_DATE, SORT_DESC)), 0, 1)->next_result();
            
            $src = 'peer_assessment/topic_read.png';
            $hover = 'NoNewPosts';
            switch ($topic->get_type())
            {
                case 1 :
                    $src = 'peer_assessment/sticky_read.gif';
                    $hover = 'Sticky';
                    break;
                case 2 :
                    $src = 'peer_assessment/important_read.gif';
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
            $conditions[] = new EqualityCondition('peer_assessment_topic_id', $topic->get_id());
            $condition = new AndCondition($conditions);
            
            $views = TrackingDataManager :: get_instance()->count_tracker_items('weblcms_peer_assessment_topic_views_tracker', $condition);
            
            $table->setCellContents($row, 4, $views);
            $table->setCellAttributes($row, 4, array('align' => 'center', 'class' => 'row2'));
            
            if ($last_post)
            {
                $link = $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_TOPIC, 'pid' => $this->pid, 'cid' => $topic->get_id())) . '#post_' . $last_post->get_id();
                $table->setCellContents($row, 5, $last_post->get_add_date() . '<br />' . $udm->retrieve_user($last_post->get_user_id())->get_fullname() . ' <a href="' . $link . '"><img title="' . Translation :: get('ViewLastPost') . '" src="' . Theme :: get_image_path() . 'peer_assessment/icon_topic_latest.gif" /></a>');
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
        if ($this->get_parent()->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $actions[] = array('href' => $this->get_url(array('pid' => $this->pid, 'peer_assessment' => $this->current_peer_assessment->get_id(), 'is_subpeer_assessment' => $this->is_subpeer_assessment, ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_DELETE_TOPIC, 'topic' => $topic->get_id())), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
        }
        
        if ($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
        { 
         	if ($topic->get_type() == 1)
        	{
	            $actions[] = array('href' => $this->get_url(array('pid' => $this->pid, 'peer_assessment' => $this->current_peer_assessment->get_id(), 'is_subpeer_assessment' => $this->is_subpeer_assessment, ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_MAKE_STICKY, ComplexDisplay :: PARAM_SELECTED_CLOI_ID => $topic->get_id())), 'label' => Translation :: get('UnSticky'), 'img' => Theme :: get_common_image_path() . 'action_remove_sticky.png');
	            $actions[] = array('label' => Translation :: get('ImportantNa'), 'img' => Theme :: get_common_image_path() . 'action_make_important_na.png');
	        }
	        else
	        {
	            if ($topic->get_type() == 2)
	            {
	                $actions[] = array('label' => Translation :: get('StickyNa'), 'img' => Theme :: get_common_image_path() . 'action_make_sticky_na.png');
	                $actions[] = array('href' => $this->get_url(array('pid' => $this->pid, 'peer_assessment' => $this->current_peer_assessment->get_id(), 'is_subpeer_assessment' => $this->is_subpeer_assessment, ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_MAKE_IMPORTANT, ComplexDisplay :: PARAM_SELECTED_CLOI_ID => $topic->get_id())), 'label' => Translation :: get('UnImportant'), 'img' => Theme :: get_common_image_path() . 'action_remove_important.png');
	            }
	            else
	            {
	                $actions[] = array('href' => $this->get_url(array('pid' => $this->pid, 'peer_assessment' => $this->current_peer_assessment->get_id(), 'is_subpeer_assessment' => $this->is_subpeer_assessment, ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_MAKE_STICKY, ComplexDisplay :: PARAM_SELECTED_CLOI_ID => $topic->get_id())), 'label' => Translation :: get('MakeSticky'), 'img' => Theme :: get_common_image_path() . 'action_make_sticky.png');
	                $actions[] = array('href' => $this->get_url(array('pid' => $this->pid, 'peer_assessment' => $this->current_peer_assessment->get_id(), 'is_subpeer_assessment' => $this->is_subpeer_assessment, ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_MAKE_IMPORTANT, ComplexDisplay :: PARAM_SELECTED_CLOI_ID => $topic->get_id())), 'label' => Translation :: get('MakeImportant'), 'img' => Theme :: get_common_image_path() . 'action_make_important.png');
	            }
        	}
        
        	//$actions[] = array('href' => $this->get_url(array('pid' => $this->pid, 'peer_assessment' => $this->current_peer_assessment->get_id(), 'is_subpeer_assessment' => $this->is_subpeer_assessment, ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_DELETE_TOPIC, 'topic' => $topic->get_id())), 'label' => Translation :: get('MakeSticky'), 'img' => Theme :: get_common_image_path() . 'action_make_sticky.png', 'confirm' => false);
        	//$actions[] = array('href' => $this->get_url(array('pid' => $this->pid, 'peer_assessment' => $this->current_peer_assessment->get_id(), 'is_subpeer_assessment' => $this->is_subpeer_assessment, ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_DELETE_TOPIC, 'topic' => $topic->get_id())), 'label' => Translation :: get('MakeImportant'), 'img' => Theme :: get_common_image_path() . 'action_make_important.png', 'confirm' => false);
        }
        
        return '<div style="float: right;">' . Utilities :: build_toolbar($actions) . '</div>';
    }

    function get_peer_assessments_table_html()
    {
        $table = new HTML_Table(array('class' => 'peer_assessment', 'cellspacing' => 1));
        
        $this->create_peer_assessments_table_header($table);
        $row = 2;
        $this->create_peer_assessments_table_content($table, $row);
        $this->create_peer_assessments_table_footer($table, $row);
        
        return $table;
    }

    function create_peer_assessments_table_header($table)
    {
        $table->setCellContents(0, 0, '<b>' . Translation :: get('Subpeer_assessments') . '</b>');
        $table->setCellAttributes(0, 0, array('colspan' => 6, 'class' => 'category'));
        
        $table->setHeaderContents(1, 0, Translation :: get('PeerAssessment'));
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

    function create_peer_assessments_table_footer($table, $row)
    {
        $table->setCellContents($row, 0, '');
        $table->setCellAttributes($row, 0, array('colspan' => 6, 'class' => 'category'));
    }

    function create_peer_assessments_table_content($table, &$row)
    {
        if (count($this->peer_assessments) == 0)
        {
            $table->setCellAttributes($row, 0, array('colspan' => 6, 'style' => 'text-align: center; padding-top: 10px;'));
            $table->setCellContents($row, 0, '<h3>' . Translation :: get('NoSubpeer_assessments') . '</h3>');
            $row ++;
            return;
        }
        
        foreach ($this->peer_assessments as $peer_assessment)
        {
            $last_post = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($peer_assessment->get_ref()->get_last_post());
            $udm = UserDataManager :: get_instance();
            $title = '<a href="' . $this->get_url(array('pid' => $this->pid, 'peer_assessment' => $peer_assessment->get_id())) . '">' . $peer_assessment->get_ref()->get_title() . '</a><br />' . strip_tags($peer_assessment->get_ref()->get_description());
            
            $table->setCellContents($row, 0, '<img title="' . Translation :: get('NoNewPosts') . '" src="' . Theme :: get_image_path() . 'peer_assessment/peer_assessment_read.png" />');
            $table->setCellAttributes($row, 0, array('width' => 50, 'class' => 'row1', 'style' => 'height:50px;'));
            $table->setCellContents($row, 1, $title);
            $table->setCellAttributes($row, 1, array('class' => 'row1'));
            $table->setCellContents($row, 2, $peer_assessment->get_ref()->get_total_topics());
            $table->setCellAttributes($row, 2, array('class' => 'row2', 'align' => 'center'));
            $table->setCellContents($row, 3, $peer_assessment->get_ref()->get_total_posts());
            $table->setCellAttributes($row, 3, array('class' => 'row2', 'align' => 'center'));
            if ($last_post)
            {
                //$link = $this->get_url(array(ComplexDisplay::PARAM_DISPLAY_ACTION => PeerAssessmentDisplay::ACTION_VIEW_TOPIC,'pid' => $this->pid, 'cid' => $topic->get_id())) . '#post_' . $last_post->get_id();
                $table->setCellContents($row, 4, $last_post->get_add_date() . '<br />' . $udm->retrieve_user($last_post->get_user_id())->get_fullname()); // .
            //' <a href="' . $link . '"><img title="' . Translation :: get('ViewLastPost') .
            //'" src="' . Theme :: get_image_path() . 'peer_assessment/icon_topic_latest.gif" /></a>');
            }
            else
            {
                $table->setCellContents($row, 4, '-');
            }
            $table->setCellAttributes($row, 4, array('class' => 'row2'));
            $table->setCellContents($row, 5, $this->get_peer_assessment_actions($peer_assessment, true, true));
            $table->setCellAttributes($row, 5, array('class' => 'row2'));
            $row ++;
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('NewTopic'), /*Theme :: get_image_path() . 'peer_assessment/buttons/button_topic_new.gif'*/ Theme :: get_common_image_path() . 'action_add.png', $this->get_url(array('pid' => $this->pid, 'peer_assessment' => $this->current_peer_assessment->get_id(), 'is_subpeer_assessment' => $this->is_subpeer_assessment, ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_CREATE_TOPIC)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if ($this->get_parent()->get_parent()->is_allowed(ADD_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('NewSubPeerAssessment'), /*Theme :: get_image_path() . 'peer_assessment/buttons/button_topic_new.gif'*/ Theme :: get_common_image_path() . 'action_add.png', $this->get_url(array('pid' => $this->pid, 'peer_assessment' => $this->current_peer_assessment->get_id(), 'is_subpeer_assessment' => $this->is_subpeer_assessment, ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_CREATE_SUBPEER_ASSESSMENT)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        //$action_bar->set_help_action(HelpManager :: get_tool_bar_help_item('peer_assessment tool'));
        

        //$action_bar->add_tool_action($this->get_access_details_toolbar_item($this));
        

        return $action_bar;
    }

    function get_peer_assessment_actions($peer_assessment, $first, $last)
    {
        if ($this->get_parent()->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $delete = array('href' => $this->get_url(array('subpeer_assessment' => $peer_assessment->get_id(), 'is_subpeer_assessment' => $this->is_subpeer_assessment, 'peer_assessment' => $this->current_peer_assessment->get_id(), ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_DELETE_SUBPEER_ASSESSMENT, 'pid' => $this->pid)), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
        }
        
        if ($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
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
                    'href' => $this->get_url(array('subpeer_assessment' => $peer_assessment->get_id(), Tool :: PARAM_ACTION => PeerAssessmentTool :: ACTION_MOVE_SUBPEER_ASSESSMENT, Tool :: PARAM_MOVE => -1)),
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
                    'href' => $this->get_url(array('subpeer_assessment' => $peer_assessment->get_id(), Tool :: PARAM_ACTION => PeerAssessmentTool :: ACTION_MOVE_SUBPEER_ASSESSMENT, Tool :: PARAM_MOVE => 1)),
                    'label' => Translation :: get('MoveDown'),
                    'img' => Theme :: get_common_image_path() . 'action_down.png'
                );
            }*/
            
            $actions[] = array('href' => $this->get_url(array('subpeer_assessment' => $peer_assessment->get_id(), 'is_subpeer_assessment' => $this->is_subpeer_assessment, 'peer_assessment' => $this->current_peer_assessment->get_id(), ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_EDIT_SUBPEER_ASSESSMENT, 'pid' => $this->pid)), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
            
            $actions[] = $delete;
        
        }
        
        return '<div style="float: right;">' . Utilities :: build_toolbar($actions) . '</div>';
    }
}
?>