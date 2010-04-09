<?php
/**
 * @package reporting.lib
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once Path :: get_reporting_path() . 'lib/reporting_template.class.php';
require_once Path :: get_reporting_path() . 'lib/reporting_block.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through reporting blocks
 * @author Hans De Bisschop
 * @author Magali Gillard
 */
class ReportingTemplateMenu
{
    const TREE_NAME = __CLASS__;
    
    /**
     * The reporting_templates
     */
    private $reporting_templates;
    /**
     * The string passed to sprintf() to format category URLs
     */
    private $url_format;
    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    /**
     * Creates a new category navigation menu.
     * @param int $owner The ID of the owner of the categories to provide in
     * this menu.
     * @param int $current_block The ID of the current block in the menu.
     * @param string $url_format The format to use for the URL of a category.
     * Passed to sprintf(). Defaults to the string
     * "?category=%s".
     * @param array $extra_items An array of extra tree items, added to the
     * root.
     * @param string[] $filter_count_on_types - Array to define the types on which the count on the categories should be filtered
     */
    function ReportingTemplateMenu($reporting_templates = array(), $current_block, $url_format = '?block=%s')
    {
        if (! is_array($reporting_templates))
        {
            $reporting_templates = array($reporting_templates);
        }
        
        $this->reporting_templates = $reporting_templates;
        $this->url_format = $url_format;
        
        //$menu = $this->get_menu_items();
        
        //parent :: __construct($menu);
        
        //$this->array_renderer = new HTML_Menu_ArrayRenderer();
        //$this->forceCurrentUrl($this->get_reporting_block_url($current_block));
    }

    /**
     * Returns the menu items.
     * @param array $extra_items An array of extra tree items, added to the
     * root.
     * @return array An array with all menu items. The structure of this array
     * is the structure needed by PEAR::HTML_Menu, on which this
     * class is based.
     */
    private function get_menu_items()
    {
        $html = array();
        $reporting_templates = $this->reporting_templates;
        
        foreach ($reporting_templates as $reporting_template)
        {
            $html[] = '<ul>';
            $html[] = '<li class="tool_list_menu title">' .Translation :: get(get_class($reporting_template)) . '</li>';
        	/*$menu_item = array();
            $menu_item['title'] = Translation :: get(get_class($reporting_template));
            $menu_item['url'] = '#';
            $menu_item['class'] = 'category';
            $menu_item[OptionsMenuRenderer :: KEY_ID] = $reporting_template->get_id();*/
            $parameters = $reporting_template->get_parameters();
            
            $reporting_blocks = $reporting_template->get_reporting_blocks();
            
            if (count($reporting_blocks) > 0)
            {
                //$sub_menu_items = array();
                
                foreach ($reporting_blocks as $reporting_block)
                {
                    $bloc_parameters = array_merge($parameters, array(ReportingManager :: PARAM_REPORTING_BLOCK_ID => $reporting_block->get_id()));
                	
                	$html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_chart.png)"><a href="' . $reporting_template->get_parent()->get_url($bloc_parameters) . '">' . Translation :: get(get_class($reporting_block)) . '</a></li>';
                	
                	/*$sub_menu_item = array();
                    $sub_menu_item['title'] = Translation :: get(get_class($reporting_block));
                    $sub_menu_item['url'] = $reporting_template->get_parent()->get_url($bloc_parameters);
                    $sub_menu_item['class'] = 'category';
                    $sub_menu_item[OptionsMenuRenderer :: KEY_ID] = $reporting_block->get_id();
                    
                    $sub_menu_items[] = $sub_menu_item;*/
                }
                
                //$menu_item['sub'] = $sub_menu_items;
            }
            
            //$menu[] = $menu_item;
        }
        
        return implode("\n", $html);
    }

    /**
     * Gets the URL of a given category
     * @param int $category The id of the category
     * @return string The requested URL
     */
    private function get_reporting_block_url($block)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->url_format, $block));
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     * @return array The breadcrumbs.
     */
    //    function get_breadcrumbs()
    //    {
    //        $trail = new BreadcrumbTrail(false);
    //        $this->render($this->array_renderer, 'urhere');
    //        $breadcrumbs = $this->array_renderer->toArray();
    //        foreach ($breadcrumbs as $crumb)
    //        {
    //            $str = Translation :: get('MyRepository');
    //            if (substr($crumb['title'], 0, strlen($str)) == $str)
    //                continue;
    //            $trail->add(new Breadcrumb($crumb['url'], substr($crumb['title'], 0, strpos($crumb['title'], '('))));
    //
    //        }
    //        return $trail;
    //    }
    

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

    function as_html()
    {
        $html[] = '<div id="tool_bar" class="tool_bar tool_bar_left">';
        
        $html[] = '<div id="tool_bar_hide_container" class="hide">';
        $html[] = '<a id="tool_bar_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_left_hide.png" /></a>';
        $html[] = '<a id="tool_bar_show" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_left_show.png" /></a>';
        $html[] = '</div>';
        
        $html[] = '<div class="tool_menu">';
        $html[] = $this->get_menu_items(); 
        $html[] = '</div>';
        
        $html[] = '</div>';
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' . '"></script>';
        $html[] = '<div class="clear"></div>';
        
        return implode("\n", $html);
    }
}
?>