<?php
/**
 * $Id: category_menu.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.category_manager
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
/**
 * This class provides a navigation menu to allow a user to browse through his
 * reservations categories
 * @author Sven Vanpoucke
 */
class StreamingMediaMenu extends HTML_Menu
{
    const TREE_NAME = __CLASS__;
    const ACTION_CREATE = 'create';
    const ACTION_ALL_VIDEOS = 'all_videos';
    const ACTION_MY_VIDEOS = 'my_videos';
    
    private $current_item;
    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;    
    private $streaming_manager;

    function StreamingMediaMenu($current_item, $streaming_manager)
    {
        $this->current_item = $current_item;
        $this->streaming_manager = $streaming_manager;
        $menu = $this->get_menu();
        parent :: __construct($menu);
        
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url($current_item));
    }

    function get_menu()
    {
        $menu = array();    
        
        $create = array();
        $create['title'] = Translation :: get('Create');
        $create['url'] = $this->get_url_create();
        //$create['class'] = 'create';
        
        $all_videos = array();
        $all_videos['title'] = Translation :: get('AllVideos');
        $all_videos['url'] = $this->get_url_all_videos();

        $my_videos = array();
        $my_videos['title'] = Translation :: get('MyVideos');
        $my_videos['url'] = $this->get_url_my_videos();
        
        $menu[] = $create;
        $menu[] = $all_videos;
        $menu[] = $my_videos;

        return $menu;
    }
    
    private function get_url_create()
    {
    	return $this->streaming_manager->get_url(array(Application :: PARAM_ACTION => self :: ACTION_CREATE, Application :: PARAM_APPLICATION => StreamingMediaManager :: CLASS_NAME));
    }
    
    private function get_url_all_videos()
    {
    	return $this->streaming_manager->get_url(array(Application :: PARAM_ACTION => self :: ACTION_ALL_VIDEOS, Application :: PARAM_APPLICATION => StreamingMediaManager :: CLASS_NAME));
    }
    
    private function get_url_my_videos()
    {
    	return $this->streaming_manager->get_url(array(Application :: PARAM_ACTION => self :: ACTION_MY_VIDEOS, Application :: PARAM_APPLICATION => StreamingMediaManager :: CLASS_NAME));
    }

    private function get_url($id)
    {
        if (! $id)
            $id = 0;
        
        return $this->streaming_manager->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id));
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     * @return array The breadcrumbs.
     */
    function get_breadcrumbs()
    {
        $trail = new BreadcrumbTrail(false);
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            if ($crumb['title'] == Translation :: get('StreamingMedias'))
                continue;
            $trail->add(new Breadcrumb($crumb['url'], $crumb['title']));
        }
        return $trail;
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