<?php
namespace application\weblcms;

use HTML_Menu_ArrayRenderer;
use common\libraries\TreeMenuRenderer;
use HTML_Menu;
use common\libraries\Application;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\CollapsedTreeMenuRenderer;
use common\libraries\Path;

require_once dirname(__FILE__) . '/../category_manager/course_category.class.php';
require_once Path::get_common_libraries_class_path() .'html/menu/collapsed_tree_menu_renderer.class.php';

/**
 * Dispays course's categories in a tree. Tree is collapsed by default.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 * @package application.lib.weblcms.course
 */
class CourseCategoryCatalogMenu extends HTML_Menu
{
    const TREE_NAME = __CLASS__;

    static function get_tree_name()
    {
        return Utilities :: get_classname_from_namespace(self :: TREE_NAME, true);
    }

    /**
     * The string passed to sprintf() to format category URLs
     */
    private $url_format;
    
    /**
     * If true display the number of items per category beside the category's title.
     * @var bool
     */
    private $display_child_count = true;

    /**
     * If true the menu  has been initialized with a tree structure. If false it has not been initialized.
     * @var bool
     */
    private $initialized = false;

    /**
     * Creates a new category navigation menu.
     *
     * @param string $url_format The format to use for the URL of a category.  Passed to sprintf(). Defaults to the string "?category=%s".
     * @param array $extra_items An array of extra tree items, added to the
     * root.
     */
    function __construct($url_format = '?course_category=%s', $display_child_count = false)
    {
        $this->url_format = $url_format;
        $this->display_child_count = $display_child_count;
        parent :: __construct();
    }

    /**
     * If true displays the number of children belonging to the category. If false do not display the number of children.
     * 
     * @return bool
     */
    public function get_display_child_count(){
        return $this->display_child_count;
    }
    
    public function set_display_child_count($value){
        $this->display_child_count = $value;
    }

    public function is_initialized(){
        return $this->initialized;
    }

    public function init($extra_items = array()){
        $menu = $this->get_menu($extra_items);
        $this->setMenu($menu);
        $this->initialized = true;
    }

    /**
     * Returns the menu items.
     *
     * @param array $extra_items An array of extra tree items, added to the  root.
     * @return array An array with all menu items. The structure of this array is the structure needed by PEAR::HTML_Menu, on which this  class is based.
     */
    public function get_menu($extra_items = array())
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $usercategories = $wdm->retrieve_course_categories();
        $categories = array();
        while ($category = $usercategories->next_result())
        {
            $categories[$category->get_parent()][] = $category;
        }
        $result = $this->get_sub_menu_items($categories, 0);
        if (count($extra_items))
        {
            $result = array_merge($result, $extra_items);
        }
        return $result;
    }

    /**
     * Returns the items of the sub menu.
     *
     * @param array $categories The categories to include in this menu.
     * @param int $parent The parent category ID.
     * @return array An array with all menu items. The structure of this array is the structure needed by PEAR::HTML_Menu, on which this class is based.
     */
    protected function get_sub_menu_items($categories, $parent)
    {
        $sub_tree = array();
        foreach ($categories[$parent] as $index => $category)
        {
            $menu_item = array();

            $wdm = WeblcmsDataManager :: get_instance();
            if ($this->get_display_child_count()) {
                $wdm = WeblcmsDataManager :: get_instance();
                $count = $wdm->count_courses(new EqualityCondition(Course :: PROPERTY_CATEGORY, $category->get_id()));
                $count_text =  " ($count)'";
            }else{
                $count_text =  '';
            }
            $menu_item['title'] = $category->get_name() . $count_text;
            if (Request :: get(Application :: PARAM_ACTION) == WeblcmsManager :: ACTION_COURSE_CATEGORY_MANAGER)
            {
                $menu_item['url'] = $this->get_category_url($category->get_id());
            }
            else
            {
                $menu_item['url'] = $this->get_category_url($category->get_id());
            }
            $sub_menu_items = $this->get_sub_menu_items($categories, $category->get_id());
            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }
            $menu_item['class'] = 'type_category';
            $menu_item['node_id'] = $category->get_id();
            $sub_tree[$category->get_id()] = $menu_item;
        }
        return $sub_tree;
    }

    /**
     * Gets the URL of a given category
     *
     * @param int $category The id of the category
     * @return string The requested URL
     */
    protected function get_category_url($category)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->url_format, $category));
    }

    /**
     * Renders the menu as a tree
     * @return string The HTML formatted tree
     */
    function render_as_tree()
    {
        if(! $this->is_initialized()){
            $this->init();
        }
        $renderer = new CollapsedTreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }
}