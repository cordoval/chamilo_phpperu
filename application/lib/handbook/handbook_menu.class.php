<?php

require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';

/**
 * This class provides a navigation menu representing the structure of a handbook
 * 
 */
class HandbookMenu extends HTML_Menu
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

    private $handbook_id;
    private $handbook_selection_id;
   

    /**
     * Creates a new category navigation menu.
     * @param string $url_format The format to use for the URL of a category.
     *                           Passed to sprintf(). Defaults to the string
     *                           "?category=%s".
     * 
     */
    function HandbookMenu($url_format, $handbook_id, $handbook_selection_id)
    {
//        $this->urlFmt = $url_format;
//        $this->urlFmt = 'run.php?go='.HandbookManager::ACTION_VIEW_HANDBOOK.'&application=handbook&'. HandbookManager::PARAM_HANDBOOK_ID.'='.$handbook_id;
        $this->urlFmt ='run.php?go='.HandbookManager::ACTION_VIEW_HANDBOOK.'&application=handbook&'. HandbookManager::PARAM_HANDBOOK_ID.'=%s&'.HandbookManager::PARAM_HANDBOOK_SELECTION_ID.'=%s';

        $this->handbook_id = $handbook_id;
        $this->handbook_selection_id = $handbook_selection_id;
   
        
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();

        if (! $handbook_id && ! $$handbook_selection_id)
        {
            $this->forceCurrentUrl($this->get_root_url());
        }
        elseif (! $handbook_selection_id && $handbook_id)
        {
            $this->forceCurrentUrl($this->get_publication_url($handbook_id));
        }
        else
        {
            $this->forceCurrentUrl($this->get_sub_item_url($handbook_id, $handbook_selection_id));
        }
    
    }

    /**
     * Returns the menu items.
     * @param array $extra_items An array of extra tree items, added to the
     *                           root.
     * @return array An array with all menu items. The structure of this array
     *               is the structure needed by PEAR::HTML_Menu, on which this
     *               class is based.
     */
    private function get_menu()
    {
        
        $menu = array();
        
        $handbooks = array();
        
        $udm = UserDataManager :: get_instance();
        $handbooks['title'] = 'handbook';
        $handbooks['url'] = $this->get_root_url();
        $handbooks['class'] = 'home';
        $subs = $this->get_publications();
        
        if (count($subs) > 0)
            $handbooks['sub'] = $subs;
        
        $menu[] = $handbooks;
        
        return $menu;
    }

    private function get_publications()
    {
        $menu = array();
        
        $hdm = HandbookDataManager :: get_instance();
        $rdm = RepositoryDataManager :: get_instance();
  
        $handbook = $rdm->retrieve_content_object($this->handbook_id);
        if($handbook)
        {
                $pub = array();
                $pub['title'] = $handbook->get_title();
                $pub['url'] = $this->get_publication_url($this->handbook_id);
                $pub['class'] = 'handbook';
                $pub['sub'] = $this->get_handbook_items($this->handbook_id, $this->handbook_id);
                $menu[] = $pub;
         }

        
        return $menu;
    }

    private function get_handbook_items($parent, $handbook_id)
    {
        $menu = array();
        $rdm = RepositoryDataManager :: get_instance();
        
        $children = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name()));
        
        while ($child = $children->next_result())
        {
            $lo = $rdm->retrieve_content_object($child->get_ref());
            
            $item = array();
            if($lo->get_type() == HandbookItem::get_type_name())
            {
                $lo = $rdm->retrieve_content_object($lo->get_reference());
            }
            if ($lo->get_type() == Handbook :: get_type_name())
            {
                $items = $this->get_handbook_items($lo->get_id(), $handbook_id);
                if (count($items) > 0)
                    $item['sub'] = $items;
            }
            
            $item['title'] = $lo->get_title();
            $item['url'] = $this->get_sub_item_url($handbook_id, $child->get_id());
           $item['class'] = $lo->get_type();
            $menu[] = $item;
        }
        
        return $menu;
    }

    private function get_publication_url($hid)
    {
        $fmt = str_replace('&'.HandbookManager::PARAM_HANDBOOK_SELECTION_ID.'=%s', '', $this->urlFmt);
        return htmlentities(sprintf($fmt, $hid));
    }

    private function get_root_url()
    {
        $fmt = str_replace('&'.HandbookManager::PARAM_HANDBOOK_SELECTION_ID.'=%s', '', $this->urlFmt);
        $fmt = str_replace('&'.HandbookManager::PARAM_HANDBOOK_ID.'=%s', '', $fmt);
        return $fmt;
    }

    private function get_sub_item_url($handbook_id, $handbook_selection_id)
    {
        return htmlentities(sprintf($this->urlFmt, $handbook_id, $handbook_selection_id));
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
            if ($crumb['title'] == Translation :: get('MyHandbook'))
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