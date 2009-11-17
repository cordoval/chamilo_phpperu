<?php
/**
 * $Id: complex_menu.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
/**
 * This class provides a navigation menu to allow a user to browse through his
 * categories of learning objects.
 * @author Sven Vanpoucke
 */
class ComplexMenu extends HTML_Menu
{
    
    private $cloi;
    private $root;
    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;
    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;
    
    /**
     * Boolean to determine wheter the nodes of the tree which are not complex are shown in the tree or not
     */
    private $view_entire_structure;
    
    /*
     * Boolean to determine wheter the url should be added or not
     * @var Bool
     */
    private $show_url; 
    
    /**
     * The datamanger that is used
     * @var RepositoryDataManager
     */
    private $dm;

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
    function ComplexMenu($root, $cloi, $url_format = '?application=repository&go=build_complex&builder_action=browse', $view_entire_structure = false, $show_url = true)
    {
        $url_format .= '&cloi=%s&root_lo=%s';
        $this->view_entire_structure = $view_entire_structure;
        $extra = array('publish');
        
        foreach ($extra as $item)
        {
            if (Request :: get($item))
                $url_format .= '&' . $item . '=' . Request :: get($item);
        }
        
        $this->show_url = $show_url;
        $this->cloi = $cloi;
        $this->root = $root;
        $this->urlFmt = $url_format;
        $this->dm = RepositoryDataManager :: get_instance();
        
        $menu = $this->get_menu($root);
        
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_cloi_url($cloi));
    }

    function get_menu($root)
    {
        $menu = array();
        $datamanager = $this->dm;
        $lo = $datamanager->retrieve_content_object($root->get_id());
        $menu_item = array();
        $menu_item['title'] = $lo->get_title();
        
        if($this->show_url)
        	$menu_item['url'] = $this->get_cloi_url();
        
        $sub_menu_items = $this->get_menu_items($root->get_id());
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        
        $menu_item['class'] = 'type_' . $lo->get_type();
        //$menu_item['class'] = 'type_category';
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
    private function get_menu_items($parent_id)
    {
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent_id, ComplexContentObjectItem :: get_table_name());
        $datamanager = $this->dm;
        $clois = $datamanager->retrieve_complex_content_object_items($condition);
        
        while ($cloi = $clois->next_result())
        {
            if ($cloi->is_complex() || $this->view_entire_structure)
            {
                $lo = $datamanager->retrieve_content_object($cloi->get_ref());
                
                if($lo->get_type() == 'learning_path_item')
                {
                	$lo = $datamanager->retrieve_content_object($lo->get_reference());
                }
                
                $menu_item = array();
                $menu_item['title'] = $lo->get_title();
                
                if($this->show_url)
                	$menu_item['url'] = $this->get_cloi_url($cloi);
                
                $sub_menu_items = $this->get_menu_items($cloi->get_ref());
                if (count($sub_menu_items) > 0)
                {
                    $menu_item['sub'] = $sub_menu_items;
                }
                
                $menu_item['class'] = 'type_' . $lo->get_type();
                //$menu_item['class'] = 'type_category';
                $menu_item[OptionsMenuRenderer :: KEY_ID] = $cloi->get_id();
                $menu[$cloi->get_id()] = $menu_item;
            }
        }
        
        return $menu;
    }

    private function get_cloi_url($cloi)
    {
        if ($cloi == null || $cloi->get_ref() == $this->root)
        {
            $new = str_replace('&cloi=%s', '', $this->urlFmt);
            return htmlentities(sprintf($new, $this->root->get_id()));
        }
        return htmlentities(sprintf($this->urlFmt, $cloi->get_id(), $this->root->get_id()));
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
        foreach ($breadcrumbs as $crumb)
        {
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
        $renderer = new TreeMenuRenderer();
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }
}