<?php
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';

class VideoConferencingMenu extends HTML_Menu
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
    private $external_repository_manager;
    private $menu_items;

    function VideoConferencingMenu($current_item, $video_conferencing_manager, $menu_items)
    {
        $this->current_item = $current_item;
        $this->video_conferencing_manager = $video_conferencing_manager;
        $this->menu_items = $menu_items;
        //$menu = $this->get_menu();
        parent :: __construct($menu_items);

        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url());
    }

    function get_menu_items()
    {
        return $this->menu_items;
    }

    function count_menu_items()
    {
        return count($this->menu_items);
    }

    private function get_url()
    {
        return $this->video_conferencing_manager->get_url();
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     * @return array The breadcrumbs.
     */
    function get_breadcrumbs()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            if ($crumb['title'] == Translation :: get('VideoConferencing'))
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
?>