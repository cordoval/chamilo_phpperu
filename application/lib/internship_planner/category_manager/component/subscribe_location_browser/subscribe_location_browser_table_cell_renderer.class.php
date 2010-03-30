<?php

require_once dirname(__FILE__) . '/subscribe_location_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/location_table/default_location_table_cell_renderer.class.php';

class SubscribeLocationBrowserTableCellRenderer extends DefaultInternshipPlannerLocationTableCellRenderer
{
    
    private $browser;
   
    function SubscribeLocationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $location)
    {
        if ($column === SubscribeLocationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($location);
        }
           
        return parent :: render_cell($column, $location);
    }

//    function render_id_cell($location){
//    	$category = $this->browser->get_category();
//    	return $category->get_id() . '|' . $location->get_id();
//    }
    
    /**
     * Gets the action links to display
     * @param Location $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($location)
    {
        $category = $this->browser->get_category();
        $toolbar_data = array();
        
        $subscribe_url = $this->browser->get_category_rel_location_subscribing_url($category, $location);
        $toolbar_data[] = array('href' => $subscribe_url, 'label' => Translation :: get('Subscribe'), 'img' => Theme :: get_common_image_path() . 'action_subscribe.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>