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
    function SurveyMenu($current_participant, $url_format = '?application=survey&go=view&survey_publication=%s&survey_participant=%s', $include_root = false, $show_complete_tree = false, $hide_current_participant = false)
    {
        $this->include_root = $include_root;
        $this->show_complete_tree = $show_complete_tree;
        $this->hide_current_participant = $hide_current_participant;
        
        $track = new SurveyParticipantTracker();
        $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_ID, $current_participant);
        $trackers = $track->retrieve_tracker_items($condition);
        
        $this->current_participant = $trackers[0];
               
        //        if ($current_category == '0' || is_null($current_category))
        //        {
        //            $condition = new EqualityCondition(SurveyPublication :: PROPERTY_ID, 0);
        //            $group = GroupDataManager :: get_instance()->retrieve_groups($condition, null, 1, new ObjectTableOrder(Group :: PROPERTY_NAME))->next_result();
        //            $this->current_category = $group;
        //        }
        //        else
        //        {
        //            $this->current_category = GroupDataManager :: get_instance()->retrieve_group($current_category);
        //        }
        

        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url($this->current_participant));
    }

    function get_menu()
    {
        $include_root = $this->include_root;
        
//        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, 0);
//        $group = GroupDataManager :: get_instance()->retrieve_groups($condition, null, 1, new ObjectTableOrder(Group :: PROPERTY_NAME))->next_result();
//        
        $participant = $this->current_participant;
        
        if (! $include_root)
        {
            return $this->get_menu_items($participant->get_id());
        }
//        else
//        {
//            $menu = array();
//            
//            $menu_item = array();
//            $menu_item['title'] = $group->get_name();
//            $menu_item['url'] = $this->get_url($group->get_id());
//            
//            $sub_menu_items = $this->get_menu_items($group->get_id());
//            if (count($sub_menu_items) > 0)
//            {
//                $menu_item['sub'] = $sub_menu_items;
//            }
//            
//            $menu_item['class'] = 'home';
//            $menu_item[OptionsMenuRenderer :: KEY_ID] = $group->get_id();
//            $menu[$group->get_id()] = $menu_item;
//            return $menu;
//        }
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
        
        $track = new SurveyParticipantTracker();
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $current_participant->get_survey_publication_id());
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $current_participant->get_user_id());
        $condition = new AndCondition($conditions);
        $trackers = $track->retrieve_tracker_items($condition);
        
        
//        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, $parent_id);
//        $groups = GroupDataManager :: get_instance()->retrieve_groups($condition, null, null, new ObjectTableOrder(Group :: PROPERTY_NAME));
       
        foreach ($trackers as $participant)
        {
            $participant_id = $participant->get_id();
            
            
            
            if (! ($participant_id == $current_participant->get_id() && $hide_current_participant))
            {
                $dm = SurveyContextDataManager::get_instance();
                $survey_context = $dm->retrieve_survey_context_by_id($participant->get_context_id());
            	$menu_item = array();
                $menu_item['title'] = $survey_context->get_name();
                $menu_item['url'] = $this->get_url($participant);
                
//                if ($group->is_parent_of($current_category) || $group->get_id() == $current_category->get_id() || $show_complete_tree)
//                {
//                    if ($group->has_children())
//                    {
//                        $menu_item['sub'] = $this->get_menu_items($group->get_id());
//                    }
//                }
//                else
//                {
//                    if ($group->has_children())
//                    {
//                        $menu_item['children'] = 'expand';
//                    }
//                }
                
                $menu_item['class'] = 'category';
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
    	return htmlentities(sprintf($this->urlFmt, $survey_publication_id,$participant_id));
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