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
    function render_cell($column, $template_rel_page)
    {
        if ($column === SurveyContextTemplateRelPageBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($template_rel_page);
        }
    
  
        return parent :: render_cell($column, $template_rel_page);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($template_rel_page)
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_template_unsubscribing_page_url($template_rel_page), 'label' => Translation :: get('Unsubscribe'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
    
    function render_id_cell($template_rel_page){
    	$id = $template_rel_page->get_survey_id().'|'.$template_rel_page->get_template_id().'|'.$template_rel_page->get_page_id();
    	return $id;
    }
    
}
?>