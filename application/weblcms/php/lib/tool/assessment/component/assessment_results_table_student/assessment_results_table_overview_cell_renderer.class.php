<?php
/**
 * $Id: assessment_results_table_overview_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_results_table_student
 */
require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/assessment_results_table_overview_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class AssessmentResultsTableOverviewStudentCellRenderer extends DefaultContentObjectTableCellRenderer
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
    function AssessmentResultsTableOverviewStudentCellRenderer($browser)
    {
        $this->table_actions = array();
        $this->browser = $browser;
    }

    /*
	 * Inherited
	 */
    function render_cell($column, $publication)
    {
        $assessment = $publication->get_content_object();
        if ($column === AssessmentResultsTableOverviewStudentColumnModel :: get_action_column())
        {
            return $this->get_actions($publication);
        }
        else
        {
            switch ($column->get_name())
            {
                case Translation :: get(Assessment :: PROPERTY_TITLE) :
                    return $assessment->get_title();
                case Translation :: get(Assessment :: PROPERTY_ASSESSMENT_TYPE) :
                    return $assessment->get_assessment_type_name();
                case Translation :: get(Assessment :: PROPERTY_AVERAGE_SCORE) :
                    $track = new WeblcmsAssessmentAttemptsTracker();
                    $avg = $track->get_average_score($publication, $this->browser->get_user_id());
                    if (! isset($avg))
                    {
                        return 'No results';
                    }
                    else
                    {
                        return $avg . '%';
                    }
                case Translation :: get(Assessment :: PROPERTY_TIMES_TAKEN) :
                    $track = new WeblcmsAssessmentAttemptsTracker();
                    return $track->get_times_taken($publication, $this->browser->get_user_id());
                default :
                    return '';
            }
        }
    }

    function get_actions($publication)
    {
    	$toolbar = new Toolbar();

        $toolbar->add_item(new ToolbarItem(Translation :: get('ViewResults'), Theme :: get_common_image_path() . 'action_view_results.png', $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $publication->get_id())), ToolbarItem::DISPLAY_ICON ));        
        
        return $toolbar->as_html();
    }

    /**
     * Gets the links to publish or edit and publish a learning object.
     * @param ContentObject $content_object The learning object for which the
     * links should be returned.
     * @return string A HTML-representation of the links.
     */
    private function get_publish_links($content_object)
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(new ToolbarItem(Translation :: get('ViewResults'), Theme :: get_common_image_path() . 'action_view_results.png', $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $publication->get_id())), ToolbarItem::DISPLAY_ICON ));        
    	
        $table_actions = $this->table_actions;
        
        foreach ($table_actions as $table_action)
        {
            //$table_action['href'] = sprintf($table_action['href'], $content_object->get_id());
            $toolbar->add_item(new ToolbarItem(null,null, sprintf($table_action['href'], $content_object->get_id()), ToolbarItem::DISPLAY_ICON ));
            //$toolbar_data[] = $table_action;
        }
        
        return $toolbar->as_html();
    }
}
?>