<?php
namespace application\portfolio;

use HTML_Menu;
use HTML_Menu_ArrayRenderer;
use user\UserDataManager;
use repository\RepositoryDataManager;
use common\libraries\EqualityCondition;
use repository\ComplexContentObjectItem;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\TreeMenuRenderer;
use common\libraries\Breadcrumb;
use repository\content_object\portfolio_item\PortfolioItem;
use repository\content_object\portfolio\Portfolio;
use common\libraries\BreadcrumbTrail;

/**
 * This class provides a navigation menu to allow a user to browse through portfolio publications
 * @author Sven Vanpoucke
 */
class PortfolioMenu extends HTML_Menu
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

    private $user;
    private $view_user;

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
    function __construct($user, $url_format, $pid, $cid, $view_user)
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
     * root.
     * @return array An array with all menu items. The structure of this array
     * is the structure needed by PEAR::HTML_Menu, on which this
     * class is based.
     */
    private function get_menu()
    {

        $menu = array();

        $users = array();

        $udm = UserDataManager :: get_instance();
        $users['title'] = $udm->retrieve_user($this->view_user)->get_fullname();
        $users['url'] = $this->get_root_url();
        $users['class'] = 'home';
        $subs = $this->get_publications();

        if (count($subs) > 0)
            $users['sub'] = $subs;

        $menu[] = $users;

        return $menu;
    }

    //    private function get_institute_publications()
    //    {
    //    }


    private function get_publications()
    {
        $menu = array();

        $pdm = PortfolioDataManager :: get_instance();
        $rdm = RepositoryDataManager :: get_instance();

        $condition = new EqualityCondition(PortfolioPublication :: PROPERTY_OWNER_ID, $this->view_user);
        $publications = $pdm->retrieve_portfolio_publications($condition);
        while ($publication = $publications->next_result())
        {
            //            if ($publication->is_visible_for_target_user($this->user->get_id()))
            //            { TODO: CHECK ON VISIBILITY WITH NEW METHODS
            $lo = $rdm->retrieve_content_object($publication->get_content_object());

            $pub = array();
            $pub['title'] = $lo->get_title();
            $pub['url'] = $this->get_publication_url($publication->get_id());
            $pub['class'] = 'portfolio';
            $pub['sub'] = $this->get_portfolio_items($publication->get_content_object(), $publication->get_id());
            $menu[] = $pub;
            //            }
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
            if ($lo->get_type() == PortfolioItem :: get_type_name())
            {
                $lo = $rdm->retrieve_content_object($lo->get_reference());
            }
            if ($lo->get_type() == Portfolio :: get_type_name())
            {
                $items = $this->get_portfolio_items($lo->get_id(), $pub_id);
                if (count($items) > 0)
                    $item['sub'] = $items;
            }

            $item['title'] = $lo->get_title();
            $item['url'] = $this->get_sub_item_url($pub_id, $child->get_id());
            $item['class'] = $lo->get_type();
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
        $trail = BreadcrumbTrail :: get_instance();
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
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

    static function get_tree_name()
    {
        return Utilities :: get_classname_from_namespace(self :: TREE_NAME, true);
    }
}