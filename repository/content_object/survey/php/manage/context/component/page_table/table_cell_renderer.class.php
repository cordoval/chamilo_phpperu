<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/table_column_model.class.php';

class SurveyPageTableCellRenderer extends DefaultSurveyPageTableCellRenderer
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
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $survey_page)
    {
        
    	if ($column === SurveyPageTableColumnModel :: get_modification_column())
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
        		Translation :: get('AddPage'),
        		Theme :: get_common_image_path() . 'action_add.png',
        		$this->browser->get_context_template_suscribe_page_url($survey_page),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        return $toolbar->as_html();
    }
}
?>