<?php
/**
 * $Id: portfolio_menu.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';

/**
 * This class provides a navigation menu to allow a user to browse through portfolio publications
 * @author Sven Vanpoucke
 */
class PortfolioMenu extends HTML_Menu
{
    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;
    
    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;
    
    private $user;
    private $view_user;

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
    function PortfolioMenu($user, $url_format, $pid, $cid, $view_user)
    {
        $this->urlFmt = $url_format;
        $this->user = $user;
        $this->view_user = $view_user;
        
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        
        if (! $pid && ! $cid)
        {
            $this->forceCurrentUrl($this->get_root_url());
        }
        elseif (! $cid && $pid)
        {
            $this->forceCurrentUrl($this->get_publication_url($pid));
        }
        else
        {
            $this->forceCurrentUrl($this->get_sub_item_url($pid, $cid));
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
        
        $users = array();
        $users['title'] = Translation :: get('MyPortfolio');
        $users['url'] = $this->get_root_url();
        $users['class'] = 'home';
        $subs = $this->get_publications();
        
        if (count($subs) > 0)
            $users['sub'] = $subs;
        
        $menu[] = $users;
        
        /*$institution = array ();
        $institution['title'] = Translation :: get('Institution');
        $institution['url'] = $this->get_root_url();
        $institution['class'] = 'home';
        $subs = $this->get_institute_publications();

        if(count($subs) > 0)
        	$institution['sub'] = $subs;

        $menu[] = $institution;*/
        
        return $menu;
    }

    private function get_institute_publications()
    {
    }

    private function get_publications()
    {
        $menu = array();
        
        $pdm = PortfolioDataManager :: get_instance();
        $rdm = RepositoryDataManager :: get_instance();
        
        $condition = new EqualityCondition(PortfolioPublication :: PROPERTY_PUBLISHER, $this->view_user);
        $publications = $pdm->retrieve_portfolio_publications($condition);
        while ($publication = $publications->next_result())
        {
            if ($publication->is_visible_for_target_user($this->user->get_id()))
            {
                $lo = $rdm->retrieve_content_object($publication->get_content_object());
                
                $pub = array();
                $pub['title'] = $lo->get_title();
                $pub['url'] = $this->get_publication_url($publication->get_id());
                $pub['class'] = 'portfolio';
                $pub['sub'] = $this->get_portfolio_items($publication->get_content_object(), $publication->get_id());
                $menu[] = $pub;
            }
        }
        
        return $menu;
    }

    private function get_portfolio_items($parent, $pub_id)
    {
        $menu = array();
        $rdm = RepositoryDataManager :: get_instance();
        
        $children = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name()));
        
        while ($child = $children->next_result())
        {
            $lo = $rdm->retrieve_content_object($child->get_ref());
            
            $item = array();
            
            $lo = $rdm->retrieve_content_object($lo->get_reference()); 
            
            if ($lo->get_type() == 'portfolio')
            {
                $items = $this->get_portfolio_items($lo->get_id(), $pub_id);
                if (count($items) > 0)
                    $item['sub'] = $items;
            }
            
            $item['title'] = $lo->get_title();
            $item['url'] = $this->get_sub_item_url($pub_id, $child->get_id());
            $item['class'] = 'portfolio';
            
            $menu[] = $item;
        }
        
        return $menu;
    }

    private function get_publication_url($pid)
    {
        $fmt = str_replace('&cid=%s', '', $this->urlFmt);
        return htmlentities(sprintf($fmt, $pid));
    }

    private function get_root_url()
    {
        $fmt = str_replace('&cid=%s', '', $this->urlFmt);
        $fmt = str_replace('&pid=%s', '', $fmt);
        return $fmt;
    }

    private function get_sub_item_url($pid, $cid)
    {
        return htmlentities(sprintf($this->urlFmt, $pid, $cid));
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
            if ($crumb['title'] == Translation :: get('MyPortfolio'))
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
        $renderer = new TreeMenuRenderer();
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }
}