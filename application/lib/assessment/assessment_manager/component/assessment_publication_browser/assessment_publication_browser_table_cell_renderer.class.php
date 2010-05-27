<?php
/**
 * $Id: assessment_publication_browser_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component.assessment_publication_browser
 */
require_once dirname(__FILE__) . '/assessment_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/assessment_publication_table/default_assessment_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../assessment_publication.class.php';
require_once dirname(__FILE__) . '/../../assessment_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author
 */

class AssessmentPublicationBrowserTableCellRenderer extends DefaultAssessmentPublicationTableCellRenderer
{
    /**
     * The browser component
     * @var AssessmentManagerAssessmentPublicationsBrowserComponent
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function AssessmentPublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $assessment_publication)
    {
        if ($column === AssessmentPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($assessment_publication);
        }

        return parent :: render_cell($column, $assessment_publication);
    }

    /**
     * Gets the action links to display
     * @param AssessmentPublication $assessment_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($assessment_publication)
    {
        $assessment = $assessment_publication->get_publication_object();

        $toolbar_data = array();
        $toolbar_data[] = array('href' => $this->browser->get_assessment_publication_viewer_url($assessment_publication), 'label' => Translation :: get('TakeAssessment'), 'img' => Theme :: get_common_image_path() . 'action_next.png');
        $toolbar_data[] = array('href' => $this->browser->get_assessment_results_viewer_url($assessment_publication), 'label' => Translation :: get('ViewResults'), 'img' => Theme :: get_common_image_path() . 'action_view_results.png');

        $user = $this->browser->get_user();

        if ($user->is_platform_admin() || $user->get_id() == $assessment_publication->get_publisher())
        {
            $toolbar_data[] = array('href' => $this->browser->get_delete_assessment_publication_url($assessment_publication), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
            $toolbar_data[] = array('href' => $this->browser->get_update_assessment_publication_url($assessment_publication), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');

            if ($assessment_publication->get_hidden())
            {
                $toolbar_data[] = array('href' => $this->browser->get_change_assessment_publication_visibility_url($assessment_publication), 'label' => Translation :: get('Show'), 'img' => Theme :: get_common_image_path() . 'action_visible_na.png');
            }
            else
            {
                $toolbar_data[] = array('href' => $this->browser->get_change_assessment_publication_visibility_url($assessment_publication), 'label' => Translation :: get('Hide'), 'img' => Theme :: get_common_image_path() . 'action_visible.png');
            }

            $toolbar_data[] = array('href' => $this->browser->get_export_qti_url($assessment_publication), 'label' => Translation :: get('Export'), 'img' => Theme :: get_common_image_path() . 'action_export.png');
            $toolbar_data[] = array('href' => $this->browser->get_move_assessment_publication_url($assessment_publication), 'label' => Translation :: get('Move'), 'img' => Theme :: get_common_image_path() . 'action_move.png');

            

            if ($assessment->is_complex_content_object())
            {
                $toolbar_data[] = array('href' => $this->browser->get_build_assessment_url($assessment_publication), 'img' => Theme :: get_common_image_path() . 'action_build.png', 'label' => Translation :: get('BuildComplex'));
            }
        }

        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>