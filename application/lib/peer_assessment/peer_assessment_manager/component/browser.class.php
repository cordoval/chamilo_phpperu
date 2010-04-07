<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_manager_component.class.php';
require_once dirname(__FILE__) . '/../../peer_assessment_publication.class.php';
require_once dirname(__FILE__).'/peer_assessment_publication_browser/peer_assessment_publication_browser_table.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/peer_assessment/peer_assessment.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/peer_assessment/peer_assessment_display.class.php';
require_once 'HTML/Table.php';
/**
 *	@author Nick Van Loocke
 */

class PeerAssessmentManagerBrowserComponent extends PeerAssessmentManagerComponent
{
    private $action_bar;
    private $introduction_text;
    private $size; //Number of published peer_assessments

    
    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        $this->action_bar = $this->get_action_bar();
        
        //$table = $this->get_table_html();
        $table = $this->get_table();
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowsePeerAssessment')));
        
        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        echo $table;
        
        if ($this->size == 0)
            echo '<br><div style="text-align: center"><h3>' . Translation :: get('NoPublications') . '</h3></div>';
        
        $this->display_footer();
    }
    
    function get_table()
    {
        $table = new PeerAssessmentPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => PeerAssessmentManager :: APPLICATION_NAME, Application :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE), null);
        return $table->as_html();
    }
	
    /*function get_table_html()
    {
        $table = new HTML_Table(array('class' => 'peer_assessment', 'cellspacing' => 1));
        
        $this->create_table_header($table);
        $row = 2;
        $this->create_table_peer_assessments($table, $row, 0);
        $this->create_table_categories($table, $row);
        
        return $table;
    }*/

    /*function create_table_header($table)
    {
        $table->setCellContents(0, 0, '');
        $table->setCellAttributes(0, 0, array('colspan' => 6, 'class' => 'category'));
        
        $table->setHeaderContents(1, 0, Translation :: get('PeerAssessment'));
        $table->setCellAttributes(1, 0, array('colspan' => 2));
        $table->setHeaderContents(1, 2, Translation :: get('Competency'));
        $table->setCellAttributes(1, 2, array('width' => 50));
        $table->setHeaderContents(1, 3, Translation :: get('Indicator'));
        $table->setCellAttributes(1, 3, array('width' => 50));
        $table->setHeaderContents(1, 4, Translation :: get('Criteria'));
        $table->setCellAttributes(1, 4, array('width' => 130));
        $table->setHeaderContents(1, 5, '');
        $table->setCellAttributes(1, 5, array('width' => 125));
    }

    function create_table_categories($table, &$row)
    {
        $categories = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication_categories();
        
        while ($category = $categories->next_result())
        {
            $table->setCellContents($row, 0, '<a href="javascript:void();">' . $category->get_name() . '</a>');
            $table->setCellAttributes($row, 0, array('colspan' => 2, 'class' => 'category'));
            $table->setCellContents($row, 2, '');
            $table->setCellAttributes($row, 2, array('colspan' => 4, 'class' => 'category_right'));
            $row ++;
            $this->create_table_peer_assessments($table, $row, $category->get_id());
        }
    
    }*/

    function create_table_peer_assessments($table, &$row, $parent)
    {
        $order[] = new ObjectTableOrder(PeerAssessmentPublication :: PROPERTY_DISPLAY_ORDER);
        
        $condition = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_CATEGORY_ID, $parent);
        $publications = $this->retrieve_peer_assessment_publications($condition, null, null, $order);
        
        $rdm = RepositoryDataManager :: get_instance();
        $udm = UserDataManager :: get_instance();
        
        $size = $publications->size();
        $this->size += $size;
        
        $counter = 0;
        while ($publication = $publications->next_result())
        {
            $first = $counter == 0 ? true : false;
            $last = $counter == ($size - 1) ? true : false;
            
            $peer_assessment = $rdm->retrieve_content_object($publication->get_peer_assessment_id(), 'peer_assessment');
            $title = '<a href="' . $this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_VIEW, ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT, PeerAssessmentManager :: PARAM_PUBLICATION_ID => $publication->get_peer_assessment_id())) . '">' . $peer_assessment->get_title() . '</a><br />' . Utilities :: truncate_string($peer_assessment->get_description());
            
            //$last_post = $rdm->retrieve_complex_content_object_item($peer_assessment->get_last_post());
            
            if ($publication->is_hidden())
            {
                $title = '<span style="color: grey;">' . $title . '</span>';
            }
            
            $table->setCellContents($row, 0, '<img title="' . Translation :: get('NoNewPosts') . '" src="' . Theme :: get_image_path() . 'peer_assessment/peer_assessment_read.png" />');
            $table->setCellAttributes($row, 0, array('width' => 50, 'class' => 'row1', 'style' => 'height:50px;'));
            $table->setCellContents($row, 1, $title);
            $table->setCellAttributes($row, 1, array('width' => '0%', 'class' => 'row1'));
            $table->setCellContents($row, 2/*, $peer_assessment->get_total_topics()*/);
            $table->setCellAttributes($row, 2, array('class' => 'row2', 'align' => 'center'));
            $table->setCellContents($row, 3/*, $peer_assessment->get_total_posts()*/);
            $table->setCellAttributes($row, 3, array('class' => 'row2', 'align' => 'center'));
            if ($last_post)
            {
                //$link = $this->get_url(array(ComplexDisplay::PARAM_DISPLAY_ACTION => PeerAssessmentDisplay::ACTION_VIEW_TOPIC,'pid' => $this->pid, 'cid' => $topic->get_id())) . '#post_' . $last_post->get_id();
                $table->setCellContents($row, 4, $last_post->get_add_date() . '<br />' . $udm->retrieve_user($last_post->get_user_id())->get_fullname() . ' <a href="' . $link . '"><img title="' . Translation :: get('ViewLastPost') . '" src="' . Theme :: get_image_path() . 'peer_assessment/icon_topic_latest.gif" /></a>');
            }
            else
            {
                $table->setCellContents($row, 5, '-');
            }
            //$table->setCellContents($row, 4, $last_post);
            $table->setCellAttributes($row, 4, array('class' => 'row2'));
            $table->setCellContents($row, 5, $this->get_peer_assessment_actions($publication, $first, $last));
            $table->setCellAttributes($row, 5, array('class' => 'row2'));
            $row ++;
            $counter ++;
        }
    }

    function get_peer_assessment_actions($publication, $first, $last)
    {
        if ($this->is_allowed(DELETE_RIGHT))
        {
            $delete = array('href' => $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $publication->get_id(), PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_DELETE)), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
        }
        
        if ($this->is_allowed(EDIT_RIGHT))
        {
            if ($publication->is_hidden())
            {
                $actions[] = array('href' => $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $publication->get_id(), PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_TOGGLE_VISIBILITY)), 'label' => Translation :: get('Show'), 'img' => Theme :: get_common_image_path() . 'action_invisible.png');
            }
            else
            {
                $actions[] = array('href' => $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $publication->get_id(), PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_TOGGLE_VISIBILITY)), 'label' => Translation :: get('Hide'), 'img' => Theme :: get_common_image_path() . 'action_visible.png');
            }
            
            if ($first)
            {
                $actions[] = array('label' => Translation :: get('MoveUpNA'), 'img' => Theme :: get_common_image_path() . 'action_up_na.png');
            }
            else
            {
                $actions[] = array('href' => $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $publication->get_id(), PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_MOVE, PeerAssessmentManager :: PARAM_MOVE => - 1)), 'label' => Translation :: get('MoveUp'), 'img' => Theme :: get_common_image_path() . 'action_up.png');
            }
            
            if ($last)
            {
                $actions[] = array('label' => Translation :: get('MoveDownNA'), 'img' => Theme :: get_common_image_path() . 'action_down_na.png');
            }
            else
            {
                $actions[] = array('href' => $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $publication->get_id(), PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_MOVE, PeerAssessmentManager :: PARAM_MOVE => 1)), 'label' => Translation :: get('MoveDown'), 'img' => Theme :: get_common_image_path() . 'action_down.png');
            }
            
            

            $actions[] = array('href' => $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $publication->get_id(), PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_EDIT)), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
            
            $actions[] = $delete;
        
        }
        
        return '<div style="float: right;">' . Utilities :: build_toolbar($actions) . '</div>';
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_CREATE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_category_manager_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
}
?>