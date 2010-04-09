<?php
require_once dirname(__FILE__) . '/peer_assessment_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/peer_assessment_publication_table/default_peer_assessment_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../peer_assessment_publication.class.php';
require_once dirname(__FILE__) . '/../../peer_assessment_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 * @author Nick Van Loocke
 */

class PeerAssessmentPublicationBrowserTableCellRenderer extends DefaultPeerAssessmentPublicationTableCellRenderer
{
    /**
     * The browser component
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function PeerAssessmentPublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $peer_assessment_publication)
    {
        if ($property = $column->get_name())
        {
            switch ($property)
            {
                case ContentObject :: PROPERTY_TITLE :
                    $url = $this->browser->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_VIEW_PEER_ASSESSMENT, PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT, PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id()));
                    return '<a href="' . $url . '">' . htmlspecialchars($peer_assessment_publication->get_content_object()->get_title()) . '</a>';
                case ContentObject :: PROPERTY_DESCRIPTION :
                    return $peer_assessment_publication->get_content_object()->get_description();
            }
        }
        if ($column === PeerAssessmentPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($peer_assessment_publication);
        }
        return parent :: render_cell($column, $peer_assessment_publication);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($peer_assessment_publication)
    {

        $toolbar_data = array();        
        $user = $this->browser->get_user();
        
        $toolbar_data[] = array('href' => $this->browser->get_peer_assessment_publication_viewer_url($peer_assessment_publication), 'label' => Translation :: get('TakePeerAssessment'), 'img' => Theme :: get_common_image_path() . 'action_next.png');
        $toolbar_data[] = array('href' => $this->browser->get_peer_assessment_results_viewer_url($peer_assessment_publication), 'label' => Translation :: get('ViewResults'), 'img' => Theme :: get_common_image_path() . 'action_view_results.png');
        
        
    	if ($user->is_platform_admin() || $user->get_id() == $peer_assessment_publication->get_publisher())
        {
            $toolbar_data[] = array('href' => $this->browser->get_delete_peer_assessment_publication_url($peer_assessment_publication), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png','confirm' => true);
            $toolbar_data[] = array('href' => $this->browser->get_update_peer_assessment_publication_url($peer_assessment_publication), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
            
            if ($peer_assessment_publication->get_hidden())
            {
                $toolbar_data[] = array('href' => $this->browser->get_change_peer_assessment_publication_visibility_url($peer_assessment_publication), 'label' => Translation :: get('Show'), 'img' => Theme :: get_common_image_path() . 'action_visible_na.png');
            }
            else
            {
                $toolbar_data[] = array('href' => $this->browser->get_change_peer_assessment_publication_visibility_url($peer_assessment_publication), 'label' => Translation :: get('Hide'), 'img' => Theme :: get_common_image_path() . 'action_visible.png');
            }

            $toolbar_data[] = array('href' => $this->browser->get_move_peer_assessment_publication_url($peer_assessment_publication), 'label' => Translation :: get('Move'), 'img' => Theme :: get_common_image_path() . 'action_move.png');
			$toolbar_data[] = array('href' => $this->browser->get_build_peer_assessment_url($peer_assessment_publication), 'img' => Theme :: get_common_image_path() . 'action_build.png', 'label' => Translation :: get('BuildComplex'));
        }
        
        
        // Gradebook
        if(WebApplication :: is_active('gradebook'))
        {
        	require_once dirname (__FILE__) . '/../../../../gradebook/gradebook_manager/gradebook_manager.class.php';
        	if(GradebookManager :: retrieve_internal_item_by_publication('peer_assessment', $peer_assessment_publication->get_id()))
        		$toolbar_data[] = array('href' => $this->browser->get_evaluation_publication_url($peer_assessment_publication), 'label' => Translation :: get('Evaluation'), 'img' => Theme :: get_common_image_path() . 'action_evaluation.png');
        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>