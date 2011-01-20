<?php
namespace application\handbook;

use common\libraries\EqualityCondition;
use repository\ComplexContentObjectItem;
use HTML_Menu;
use HTML_Menu_ArrayRenderer;
use repository\RepositoryDataManager;
use common\libraries\BreadcrumbTrail;
use user\UserDataManager;
use common\libraries\TreeMenuRenderer;
use common\libraries\Breadcrumb;
use common\libraries\Utilities;
use repository\content_object\handbook_item\HandbookItem;
use repository\content_object\handbook\Handbook;
use repository\content_object\glossary\Glossary;

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
    private $handbook_publication_id;
    private $top_handbook_id;


    /**
     * Creates a new category navigation menu.
     * @param string $url_format The format to use for the URL of a category.
     *                           Passed to sprintf(). Defaults to the string
     *                           "?category=%s".
     *
     */
    function __construct($url_format, $handbook_id, $handbook_selection_id, $handbook_publication_id, $top_handbook_id)
    {
        
        $this->urlFmt ='run.php?go='.HandbookManager::ACTION_VIEW_HANDBOOK.'&application=handbook&'. HandbookManager::PARAM_TOP_HANDBOOK_ID.'=%s&'.HandbookManager::PARAM_HANDBOOK_SELECTION_ID.'=%s&'. HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID.'=%s&' . HandbookManager::PARAM_COMPLEX_OBJECT_ID . '=%s&' . HandbookManager::PARAM_HANDBOOK_ID . '=%s' ;

        $this->handbook_id = $handbook_id;
        $this->handbook_selection_id = $handbook_selection_id;
        $this->handbook_publication_id = $handbook_publication_id;
        $this->top_handbook_id = $top_handbook_id;
        
 
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();

        if (!$this->top_handbook_id && !$this->$handbook_selection_id)
        {
            //NO HANDBOOK ID & NO SELECTION (SHOULD NOT EXIST)
            $this->forceCurrentUrl($this->get_root_url());
        }
        elseif (!$this->handbook_selection_id && $this->top_handbook_id)
        {
            //NO SELECTION
            $this->forceCurrentUrl($this->get_publication_url($this->top_handbook_id, $this->handbook_publication_id));
        }
        else
        {
            //SELECTION;
            $this->get_selection_url();
            
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
        $hdm = HandbookDataManager :: get_instance();
        $rdm = RepositoryDataManager :: get_instance();
        $handbook = $rdm->retrieve_content_object($this->top_handbook_id);
        $pub['title'] = $handbook->get_title();
        $pub['url'] = $this->get_publication_url($this->handbook_id, $this->handbook_publication_id);
        $pub['class'] = 'handbook';
        $pub['sub'] = $this->get_handbook_items($this->top_handbook_id, $this->top_handbook_id);
        $menu[] = $pub;
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
                $pub['sub'] = $this->get_handbook_items($this->top_handbook_id, $this->handbook_id);
                $menu[] = $pub;
         }
        return $menu;
    }

    private function get_handbook_items($top_handbook_id, $handbook_id)
    {
        $menu = array();
        $rdm = RepositoryDataManager :: get_instance();
        $children = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $handbook_id, ComplexContentObjectItem :: get_table_name()));
        while ($child = $children->next_result())
        {
            $lo = $rdm->retrieve_content_object($child->get_ref());
            $item = array();
            if($lo->get_type() == HandbookItem::get_type_name())
            {
                $lo = $rdm->retrieve_content_object($lo->get_reference());
            }
            if($lo->get_type() != Glossary::get_type_name())
            {
                //do not show glossary            
                if ($lo->get_type() == Handbook :: get_type_name())
                {
                    $items = $this->get_handbook_items($top_handbook_id, $lo->get_id());
                    if (count($items) > 0)
                    $item['sub'] = $items;
                    $item['url'] = $this->get_sub_item_url($top_handbook_id, $child->get_ref(),$this->handbook_publication_id, $child->get_id(), $lo->get_id());
                }
                else
                {
                    $item['url'] = $this->get_sub_item_url($top_handbook_id, $child->get_ref(),$this->handbook_publication_id, $child->get_id(), $handbook_id);

                }
                $alternatives = HandbookManager::get_alternatives_preferences_types($lo->get_id(), $this->handbook_id);
                if($alternatives['text_main'] != null)
                {
                    $item['title'] = $alternatives['text_main']->get_title();
                }
                else if($alternatives['handbook_main'] != null)
                {
                    $item['title'] = $alternatives['handbook_main']->get_title();
                }
                else
                {
                    $item['title'] = $lo->get_title();
                }


                $item['class'] = $lo->get_type();

                $menu[] = $item;
            }
        }

        return $menu;
    }

    private function get_publication_url($hid, $hpid)
    {
        $fmt = str_replace('&'.HandbookManager::PARAM_HANDBOOK_SELECTION_ID.'=%s', '', $this->urlFmt);
         $fmt = str_replace('&'.HandbookManager::PARAM_HANDBOOK_ID.'=%s', '', $this->urlFmt);

        return htmlentities(sprintf($fmt, $hid, $hpid));
    }

    private function get_root_url($hpid)
    {
        
        $fmt = str_replace('&'.HandbookManager::PARAM_HANDBOOK_SELECTION_ID.'=%s', '', $this->urlFmt);
        $fmt = str_replace('&'.HandbookManager::PARAM_TOP_HANDBOOK_ID.'=%s', '', $fmt);
        $fmt = str_replace('&'.HandbookManager::PARAM_COMPLEX_OBJECT_ID.'=%s', '', $fmt);
        $fmt = str_replace('&'.HandbookManager::PARAM_HANDBOOK_ID.'=%s', '', $fmt);
//        return $fmt;
        return htmlentities(sprintf($fmt, $hpid));
    }

    private function get_sub_item_url($top_handbook_id, $handbook_selection_id, $hpid, $coid, $handbook_id)
    {
        return htmlentities(sprintf($this->urlFmt, $top_handbook_id, $handbook_selection_id, $hpid, $coid, $handbook_id));
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

        if($_SESSION[HandbookManager::PARAM_MENU_STYLE] == HandbookManager::MENU_OPEN)
        {
//       show complete menu: nothing collapsed
         $this->setMenuType('sitemap');
        }
        else
        {
//        all collapsed
            $this->setMenuType('tree');
        }
        

        $this->render($renderer);
//        $this->render($renderer, 'prevnext');
        return $renderer->toHTML();
    }

    static function get_tree_name()
    {
    	return Utilities :: get_classname_from_namespace(self :: TREE_NAME, true);
    }

    function get_selection_url()
    {
        $rdm = RepositoryDataManager::get_instance();
        $complex_set = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $this->handbook_selection_id, ComplexContentObjectItem :: get_table_name()));
        if($co = $complex_set->next_result())
        {
           $cloid = $co->get_id();
        }

            return $this->forceCurrentUrl($this->get_sub_item_url($this->top_handbook_id, $this->handbook_selection_id, $this->handbook_publication_id, $cloid, $this->handbook_id));

    }
}