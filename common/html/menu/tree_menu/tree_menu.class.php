<?php
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';

class TreeMenu extends HTML_Menu
{
	private $name;
	
	function TreeMenu($name, $data_provider)
	{
		$this->name = $name;
		$menu = $data_provider->get_tree_menu_data()->to_array();
		parent :: __construct(array($menu));
		$this->array_renderer = new HTML_Menu_ArrayRenderer();
//        $this->forceCurrentUrl($data_provider->get_selected_tree_menu_item()->get_url());
	}
	
 	/**
     * Get the breadcrumbs which lead to the current category.
     * @return array The breadcrumbs.
     */
    function get_breadcrumbs()
    {
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        $trail = BreadcrumbTrail :: get_instance();
        $i = 0;
        foreach ($breadcrumbs as $crumb)
        {
            if ($i == 0)
            {
                $i ++;
                continue;
            }
            
            $trail->add(new Breadcrumb($crumb['url'], substr($crumb['title'], 0, strpos($crumb['title'], '(') - 1)));
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
    
    function get_tree_name()
    {
    	return $this->name;
    }
}
?>