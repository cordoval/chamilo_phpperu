<?php
/**
 * $Id: survey_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_browser
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
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('TakeSurvey'),
        		Theme :: get_common_image_path() . 'action_next.png',
        		$this->browser->get_survey_publication_viewer_url($publication),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('InviteParticipants'),
        		Theme :: get_common_image_path() . 'action_invite_users.png',
        		$this->browser->get_mail_survey_participant_url($publication),
        		ToolbarItem :: DISPLAY_ICON,
        		true
        ));
        
        return parent :: get_actions($publication, $toolbar);
    }

}
?>