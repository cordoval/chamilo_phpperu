<?php
/**
 * $Id: survey_user_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component.assessment_survey_publisher.survey_user_table
 */
require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/survey_user_table_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class SurveyUserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    private $table_actions;
    private $browser;

    /**
     * Constructor.
     * @param string $publish_url_format URL for publishing the selected
     * learning object.
     * @param string $edit_and_publish_url_format URL for editing and publishing
     * the selected learning object.
     */
    function SurveyUserTableCellRenderer($browser)
    {
        $this->table_actions = array();
        $this->browser = $browser;
    }

    /*
	 * Inherited
	 */
    function render_cell($column, $survey_invitation)
    {
        if ($column === SurveyUserTableColumnModel :: get_action_column())
        {
            return $this->get_actions($survey_invitation);
        }
        else
        {
            switch ($column->get_name())
            {
                case SurveyInvitation :: PROPERTY_USER_ID :
                    $user = UserDataManager :: get_instance()->retrieve_user($survey_invitation->get_user_id());
                    if ($user != null)
                        return $user->get_fullname();
                    else
                        return Translation :: get('Anonymous');
                
                case SurveyInvitation :: PROPERTY_EMAIL :
                    return $survey_invitation->get_email();
                case SurveyInvitation :: PROPERTY_VALID :
                    if ($survey_invitation->get_valid())
                        return 'Yes';
                    else
                        return 'No';
                default :
                    return '';
            }
        }
    }

    function get_actions($publication)
    {
        $actions = array();
        
        return Utilities :: build_toolbar($actions);
    }

/**
 * Gets the links to publish or edit and publish a learning object.
 * @param ContentObject $content_object The learning object for which the
 * links should be returned.
 * @return string A HTML-representation of the links.
 */
/*private function get_publish_links($content_object)
	{
		$toolbar_data = array();
		$table_actions = $this->table_actions;
		
		foreach($table_actions as $table_action)
		{
			$table_action['href'] = sprintf($table_action['href'], $content_object->get_id());
			$toolbar_data[] = $table_action;
		}
		
		return Utilities :: build_toolbar($toolbar_data);
	}*/
}
?>