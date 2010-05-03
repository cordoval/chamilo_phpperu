<?php

require_once dirname(__FILE__) . '/rel_page_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../tables/template_rel_page_table/default_template_rel_page_table_cell_renderer.class.php';

class SurveyContextTemplateRelPageBrowserTableCellRenderer extends DefaultSurveyContextTemplateRelPageTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function SurveyContextTemplateRelPageBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $templaterelpage)
    {
        if ($column === SurveyContextTemplateRelPageBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($templaterelpage);
        }
    
  
        return parent :: render_cell($column, $templaterelpage);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($templaterelpage)
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_template_rel_page_unsubscribing_url($templaterelpage), 'label' => Translation :: get('Unsubscribe'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
    
    function render_id_cell($templaterelpage){
    	return $templaterelpage->get_page_id();
    }
    
}
?>