<?php

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/survey_page_table/default_survey_page_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../survey_manager.class.php';

class SurveyPageBrowserTableCellRenderer extends DefaultSurveyPageTableCellRenderer
{
    /**
     * The browser component
     * @var SurveyManagerSurveyPagesBrowserComponent
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function SurveyPageBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $survey_page)
    {
        
    	if ($column === SurveyPageBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($survey_page);
        }
        
        return parent :: render_cell($column, $survey_page);
    }

    /**
     * Gets the action links to display
     * @param SurveyPage $survey_page The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($survey_page)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('BrowseSurveyPageQuestions'),
        		Theme :: get_common_image_path() . 'action_view_results.png',
        		$this->browser->get_browse_survey_page_questions_url($survey_page),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        return $toolbar->as_html();
    }
}
?>