<?php
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once Path :: get_application_path() . '/lib/gradebook/internal_item.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/content_object_publication.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/weblcms_data_manager.class.php';

class WeblcmsPublicationsCategoryMenu extends HTML_Menu
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
    
    private $current_category;

    /**
     * Creates a new category navigation menu.
     * @param int $owner The ID of the owner of the categories to provide in
     * this menu.
     * @param int $current_category The ID of the current category in the menu.
     * @param string $url_format The format to use for the URL of a category.
     *                           Passed to sprintf(). Defaults to the string
     *                           "?category=%s".
     * @param array $extra_items An array of extra tree items, added to the
     *                           root.
     */
    function WeblcmsPublicationsCategoryMenu($current_category, $url_format = '?application=gradebook&go=home&publication_type=weblcms&tool=%s')
    {
    	$this->current_category = $current_category;
    	$this->urlFmt = $url_format;
        
        $menu = $this->get_menu();
        parent :: __construct($menu);
        
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url($current_category));
    }

    function get_menu()
    {
        $menu = array();
        
        $menu_item = array();
        $menu_item['title'] = Translation :: get('Tools');// . ' (' . $this->get_publication_count(0) . ')';
        $menu_item['url'] = $this->get_url(0);
        $menu_item['id'] = 0;
        $menu_item['class'] = 'home';
        $sub_menu_items = $this->get_menu_items();
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        
        $menu_item[OptionsMenuRenderer :: KEY_ID] = 0;
        $menu[0] = $menu_item;
        
        return $menu;
    }

    /**
     * Returns the menu items.
     * @param array $extra_items An array of extra tree items, added to the
     *                           root.
     * @return array An array with all menu items. The structure of this array
     *               is the structure needed by PEAR::HTML_Menu, on which this
     *               class is based.
     */
    private function get_menu_items()
    {
    	$tools = array();
        $condition = new EqualityCondition(InternalItem :: PROPERTY_APPLICATION, 'weblcms');
        $gdm = GradebookDataManager :: get_instance();
        $internal_items = $gdm->retrieve_internal_items_by_application($condition);
        while ($internal_item = $internal_items->next_result())
        {
        	$wdm = WeblcmsDataManager :: get_instance();
        	$content_object_publication = $wdm->retrieve_content_object_publication($internal_item->get_publication_id());
        	$tools[] = $content_object_publication->get_tool();
        }
        
        $menu = array();
        foreach ($tools as $tool)
        {
            $menu_item = array();
            $menu_item['title'] = $tool;
            $menu_item['url'] = $this->get_url($tool);
			$menu_item['class'] = 'tool';
			$menu[] = $menu_item;
        }
        
        return $menu;
    }

    /**
     * Gets the URL of a given category
     * @param int $category The id of the category
     * @return string The requested URL
     */
    function get_url($tool)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->urlFmt, $tool));
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     * @return array The breadcrumbs.
     */
    function get_breadcrumbs()
    {
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        $trail = new BreadcrumbTrail(false);
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
    
    static function get_tree_name()
    {
    	return Utilities :: camelcase_to_underscores(self :: TREE_NAME);
    }
}
?>