<?php

require_once dirname(__FILE__) . '/subscribe_page_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../tables/page_table/default_page_table_cell_renderer.class.php';

class SurveyContextTemplateSubscribePageBrowserTableCellRenderer extends DefaultSurveyPageTableCellRenderer
{
    
    private $browser;
   
    function SurveyContextTemplateSubscribePageBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $page)
    {
       	if ($column === SurveyContextTemplateSubscribePageBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($page);
        }
           
        return parent :: render_cell($column, $page);
    }

    function render_id_cell($page){
    	$template = $this->browser->get_survey_context_template();
    	return $template->get_id() . '|' . $page->get_id();
    }
    
    /**
     * Gets the action links to display
     * @param Location $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($page)
    {
        $template = $this->browser->get_survey_context_template();
        $toolbar_data = array();
        
        $subscribe_url = $this->browser->get_template_suscribe_page_url($template->get_id(), $page->get_id());
        $toolbar_data[] = array('href' => $subscribe_url, 'label' => Translation :: get('Subscribe'), 'img' => Theme :: get_common_image_path() . 'action_subscribe.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>