<?php 
namespace application\survey;

use common\libraries\ObjectTableCellRenderer;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;

require_once dirname(__FILE__) . '/participant_browser_table_column_model.class.php';

class SurveyParticipantBrowserTableCellRenderer extends DefaultParticipantTableCellRenderer
{
    /**
     * The browser component
     * @var SurveySurveyPublicationsBrowserComponent
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
    function render_cell($column, $survey_participant_tracker)
    {
        if ($column === SurveyParticipantBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($survey_participant_tracker);
        }
        
        return parent :: render_cell($column, $survey_participant_tracker);
    }

    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($survey_participant_tracker)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('TakeSurvey'), Theme :: get_common_image_path() . 'action_next.png', $this->browser->get_survey_participant_publication_viewer_url($survey_participant_tracker), ToolbarItem :: DISPLAY_ICON));
     	$toolbar->add_item(new ToolbarItem(Translation :: get('TakeSurvey'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_survey_participant_delete_url($survey_participant_tracker), ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}
?>