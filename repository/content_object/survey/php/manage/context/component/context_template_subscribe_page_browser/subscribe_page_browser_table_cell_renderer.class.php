<?php namespace repository\content_object\survey;

require_once dirname(__FILE__) . '/subscribe_page_browser_table_column_model.class.php';

class SurveyContextTemplateSubscribePageBrowserTableCellRenderer extends DefaultSurveyPageTableCellRenderer
{
    
    private $browser;
   
    function __construct($browser)
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
        
        $toolbar = New Toolbar();
       
        $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Subscribe'),
        			Theme :: get_common_image_path().'action_subscribe.png', 
					$this->browser->get_template_suscribe_page_url($template->get_id(), $page->get_id()),
				 	ToolbarItem :: DISPLAY_ICON
		));
		        
        return $toolbar->as_html();
    }
}
?>