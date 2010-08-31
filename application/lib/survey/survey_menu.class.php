<?php
/**
 * $Id: survey_menu.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package survey.lib
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
/**
 * This class provides a navigation menu to allow a user to browse through
 * categories of courses.
 * @author Bart Mollet
 */
class SurveyMenu extends HTML_Menu
{
    const TREE_NAME = __CLASS__;
    
    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;
    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;
    
    private $include_root;
    
    private $current_participant;
    
    private $show_complete_tree;
    
    private $hide_current_participant;

    /**
     * Creates a new category navigation menu.
     * @param int $owner The ID of the owner of the categories to provide in
     * this menu.
     * @param int $current_category The ID of the current category in the menu.
     * @param string $url_format The format to use for the URL of a category.
     * Passed to sprintf(). Defaults to the string
     * "?category=%s".
     * @param array $extra_items An array of extra tree items, added to the
     * root.
     */
    function SurveyMenu($current_participant, $url_format = '?application=survey&go=view&survey_publication=%s&survey_participant=%s', $include_root = true, $show_complete_tree = false, $hide_current_participant = false)
    {
        $this->include_root = $include_root;
        $this->show_complete_tree = $show_complete_tree;
        $this->hide_current_participant = $hide_current_participant;
        
        $track = new SurveyParticipantTracker();
        $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_ID, $current_participant);
        $trackers = $track->retrieve_tracker_items($condition);
        
        $this->current_participant = $trackers[0];
        
        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url($this->current_participant));
    }

    function get_menu()
    {
        $include_root = $this->include_root;
        
        $track = new SurveyParticipantTracker();
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->current_participant->get_survey_publication_id());
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->current_participant->get_user_id());
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_PARENT_ID, 0);
        $condition = new AndCondition($conditions);
        $trackers = $track->retrieve_tracker_items($condition);
        
//        dump($trackers);
        
        if (! $include_root)
        {
//            dump('include root');
            exit();
            $menu = array();
            foreach ($trackers as $tracker)
            {
                $menu = array_merge($this->get_menu_items($tracker->get_id()), $menu);
            }
            return $menu;
        }
        else
        {
            $menu = array();
            
//            dump('not include root');
            
            foreach ($trackers as $tracker)
            {
//                dump($tracker);
                $menu_item = array();
                $menu_item['title'] = $tracker->get_context_name();
                $menu_item['url'] = $this->get_url($tracker);
                
                $sub_menu_items = $this->get_menu_items($tracker->get_id());
//                
//                dump($sub_menu_items);
//                exit();
                
                if (count($sub_menu_items) > 0)
                {
                    $menu_item['sub'] = $sub_menu_items;
                }
                if ($tracker->get_status() == SurveyParticipantTracker :: STATUS_FINISHED)
                {
                    $menu_item['class'] = 'survey_finished';
                }
                else
                {
                    $menu_item['class'] = 'survey';
                }
                
                $menu_item[OptionsMenuRenderer :: KEY_ID] = $tracker->get_id();
                $menu[$tracker->get_id()] = $menu_item;
            }
            return $menu;
        }
    }

    /**
     * Returns the menu items.
     * @param array $extra_items An array of extra tree items, added to the
     * root.
     * @return array An array with all menu items. The structure of this array
     * is the structure needed by PEAR::HTML_Menu, on which this
     * class is based.
     */
    private function get_menu_items($parent_id = 0)
    {
        $current_participant = $this->current_participant;
        
        $show_complete_tree = $this->show_complete_tree;
        $hide_current_participant = $this->hide_current_participant;
        
        //        $track = new SurveyParticipantTracker();
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $current_participant->get_survey_publication_id());
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $current_participant->get_user_id());
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_PARENT_ID, $parent_id);
        $condition = new AndCondition($conditions);
        $trackers = Tracker :: get_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
        $tracker_count = Tracker :: count_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
//        dump($tracker_count);
        
//        while($tracker = $trackers->next_result()){
//        	dump($tracker);
//        }
        
        //        dump($trackers);
        

        //        exit;
       while ($participant = $trackers->next_result())
        {
            
        	$participant_id = $participant->get_id();
            
//        	dump($participant_id);
        	
            if (! ($participant_id == $current_participant->get_id() && $hide_current_participant))
            {
                
                $menu_item = array();
                $menu_item['title'] = $participant->get_context_name();
                $menu_item['url'] = $this->get_url($participant);
                
                if ($participant->has_children())
                {
//                    dump($participant->get_id());
//                    exit;
                    $menu_item['sub'] = $this->get_menu_items($participant->get_id());
                }
                
                if ($participant->get_status() == SurveyParticipantTracker :: STATUS_FINISHED)
                {
                    $menu_item['class'] = 'survey_finished';
                }
                else
                {
                    $menu_item['class'] = 'survey';
                }
                
                $menu_item[OptionsMenuRenderer :: KEY_ID] = $participant->get_id();
                $menu[$participant->get_id()] = $menu_item;
            }
        }
        
        return $menu;
    }

    /**
     * Gets the URL of a given category
     * @param int $category The id of the category
     * @return string The requested URL
     */
    function get_url($participant)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        $survey_publication_id = $participant->get_survey_publication_id();
        $participant_id = $participant->get_id();
        return htmlentities(sprintf($this->urlFmt, $survey_publication_id, $participant_id));
    }

    private function get_home_url($category)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(str_replace('&group_id=%s', '', $this->urlFmt));
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     * @return array The breadcrumbs.
     */
    function get_breadcrumbs()
    {
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            $crumb['name'] = $crumb['title'];
            unset($crumb['title']);
        }
        return $breadcrumbs;
    }

    /**
     * Renders the menu as a tree
     * @return string The HTML formatted tree
     */
    function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

    static function get_tree_name()
    {
        return Utilities :: camelcase_to_underscores(self :: TREE_NAME);
    }
}