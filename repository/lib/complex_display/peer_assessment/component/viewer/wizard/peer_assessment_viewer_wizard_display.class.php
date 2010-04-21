<?php
require_once dirname(__FILE__) . '/table_competence/default_competence_table_column_model.class.php';
require_once dirname(__FILE__) . '/table_competence/default_competence_table_cell_renderer.class.php';

/**
 * @author Sven Vanpoucke
 * @author Nick Van Loocke
 */

class PeerAssessmentViewerWizardDisplay extends HTML_QuickForm_Action_Display
{

    private $parent;

    public function PeerAssessmentViewerWizardDisplay($parent)
    {

        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {
        $html = array();
        //$this->parent->get_parent()->display_header();

        if ($current_page->get_page_number() != 0)
        {
            $html[] = '<div class="assessment">';
            $html[] = '<h2>' . $this->parent->get_peer_assessment()->get_title() . '</h2>';
            
	        if ($this->parent->get_peer_assessment()->has_description())
	        {
	            $html[] = '<div class="description">';
	            $html[] = $this->parent->get_peer_assessment()->get_description();
	            $html[] = '<div class="clear"></div>';
	            $html[] = '</div>';
	        }

            $html[] = '<br />';

            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
            $html[] = '</div>';

            $html[] = '<br />';
            
            $competences = $this->parent->get_peer_assessment_page_competences($this->parent->get_peer_assessment());
            
            foreach($competences as $competence)
            {
            	$html[] = $competence->get_title();
            	$html[] = '<br />';
            }
            
            // Creation of a table with the competence objects in it should come here ...
            //$html[] = $table = new DefaultCompetenceTableColumnModel($this, array(Application :: PARAM_APPLICATION => PeerAssessmentManager :: APPLICATION_NAME, Application :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));
			//$html[] = $table->as_html();

	
            
            $html[] = '<br />';
            $html[] = '</div>';

            $html[] = '<div>';
            $html[] = $current_page->toHtml();
            $html[] = '</div>';

            $html[] = '<br />';

            $html[] = '<div class="assessment">';
            $html[] = '<br />';

            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
            $html[] = '</div>';

            $html[] = '</div>';

            echo implode("\n", $html);
        }
        else
        {
            $html = array();
            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = Translation :: get('NoPeerAssessmentPageAddedToPeerAssessment');
            $html[] = '</div>';
            echo implode("\n", $html);
        }

        $this->parent->get_parent()->display_footer();

    }
}
?>