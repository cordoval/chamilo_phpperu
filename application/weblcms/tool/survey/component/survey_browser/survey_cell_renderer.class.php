<?php
/**
 * $Id: survey_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.survey.component.survey_browser
 */
require_once dirname(__FILE__) . '/../../../../browser/object_publication_table/object_publication_table_cell_renderer.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class SurveyCellRenderer extends ObjectPublicationTableCellRenderer
{

    function SurveyCellRenderer($browser)
    {
        parent :: __construct($browser);
    }

    /*
	 * Inherited
	 */
    function render_cell($column, $publication)
    {
        if ($column === ObjectPublicationTableColumnModel :: get_action_column())
        {
            return $this->get_actions($publication)->as_html();
        }
        
        return parent :: render_cell($column, $publication);
    }

    function get_actions($publication)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        //$toolbar->add_item(new ToolbarItem(Translation :: get('TakeSurvey'), Theme :: get_common_image_path() . 'action_next.png', $this->table_renderer->get_url(array(Tool :: PARAM_ACTION => SurveyTool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT, SurveyTool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON));
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('InviteParticipants'), Theme :: get_common_image_path() . 'action_invite_users.png', $this->table_renderer->get_url(array(Tool :: PARAM_ACTION => SurveyTool :: ACTION_MAIL_SURVEY_PARTICIPANTS, SurveyTool :: PARAM_PUBLICATION_ID => $publication->get_id())), 

        ToolbarItem :: DISPLAY_ICON, true));
        
        return parent :: get_actions($publication, $toolbar);
    }

}
?>