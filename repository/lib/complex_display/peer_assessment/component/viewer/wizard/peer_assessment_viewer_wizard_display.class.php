<?php
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
        
        if($current_page->get_page_number() == 0)
        {
        	$html = array();
            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = Translation :: get('NoPeerAssessmentPageAddedToPeerAssessment');
            $html[] = '</div>';
            echo implode("\n", $html);
        }
        elseif ($current_page->get_page_number() == 1)
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

            /*$html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
            $html[] = '</div>';
            
            $html[] = '<br />';*/
            
               
            $html[] = '<h3>' . Translation :: get('Competence') . '</h3>';
	        $html[] = '<table class="data_table">';
	        $html[] = '<thead>';
	        $html[] = '<tr>';
	        $html[] = '<th>'. Translation :: get('Type') .'</th>';
	        $html[] = '<th>' . Translation :: get('Title') . '</th>';
	        $html[] = '<th>' . Translation :: get('Users') . '</th>';
	        $html[] = '<th class="numeric">' . Translation :: get('Finished') . '</th>';
	        $html[] = '<th class="action"></th>';
	        $html[] = '</tr>';
	        $html[] = '</thead>';
	        $html[] = '<tbody>';
	        $html[] = '</tbody>';
	        
			// Retrieve competences
	        $competences = $this->parent->get_peer_assessment_page_competences($this->parent->get_peer_assessment());

            foreach($competences as $competence)
            {
            	$url = '';
            	$html[] = '<tr>';
            	$html[] = '<td><img src="'. Theme :: get_common_image_path() . 'content_object/competence.png' .'" alt=""/></td>';

            	$html[] = '<td><a href="'. $url .'">'.$competence->get_title().'</a></td>';
            	$html[] = '<td></td>';
            	
            	/*if($competence->isFinished())
            	{
            		$image = 'button_start';
            	}
            	else
            	{*/
            		$image = 'button_cancel';
            	//}
            	
            	$html[] = '<td><img src="' . Theme :: get_common_image_path() . 'buttons/'.$image.'.png' .'" alt="" /></td>';
            	$html[] = '<td><a href="'. $url .'"><img src="' . Theme :: get_common_image_path() . 'action_next.png' .'" alt=""/></a></td>';
            	$html[] = '</tr>';
            	
	            /*$indicators = $this->parent->get_peer_assessment_page_indicators_via_competence($this->parent->get_peer_assessment(), $competence);	
	            foreach($indicators as $indicator)
	            {
	            	$html[] = '<tr>';
	            	$html[] = '<td></td>';
	            	$html[] = '<td>'.$indicator->get_title().'</td>';        	
	            	$html[] = '<td></td>';
	            	$html[] = '<td></td>';
	            	$html[] = '<td></td>';
	            	$html[] = '</tr>';
	            	

		            $criterias = $this->parent->get_peer_assessment_page_criterias_via_indicator($this->parent->get_peer_assessment(), $indicator);	
		            foreach($criterias as $criteria)
		            {
		            	$html[] = '<tr>';
		            	$html[] = '<td></td>';
		            	$html[] = '<td>'.$criteria->get_title().'</td>';        	
		            	$html[] = '<td></td>';
		            	$html[] = '<td></td>';
		            	$html[] = '<td></td>';
		            	$html[] = '</tr>';
		            }
	            }*/
            }
            $html[] = '</table>';
            
			
	
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
               
            $html[] = '<h3>' . Translation :: get('Indicator') . '</h3>';
	        $html[] = '<table class="data_table">';
	        $html[] = '<thead>';
	        $html[] = '<tr>';
	        $html[] = '<th>'. Translation :: get('Type') .'</th>';
	        $html[] = '<th>' . Translation :: get('Title') . '</th>';
	        $html[] = '<th>' . Translation :: get('Users') . '</th>';
	        $html[] = '<th class="numeric">' . Translation :: get('Finished') . '</th>';
	        $html[] = '<th class="action"></th>';
	        $html[] = '</tr>';
	        $html[] = '</thead>';
	        $html[] = '<tbody>';
	        $html[] = '</tbody>';
	        
			// Retrieve competences
	        $competences = $this->parent->get_peer_assessment_page_competences($this->parent->get_peer_assessment());

            foreach($competences as $competence)
            {	
            	// Retrieve indicators
	            $indicators = $this->parent->get_peer_assessment_page_indicators_via_competence($this->parent->get_peer_assessment(), $competence);
	
	            foreach($indicators as $indicator)
	            {
	            	$html[] = '<tr>';
	            	$html[] = '<td></td>';
	            	$html[] = '<td>'.$indicator->get_title().'</td>';        	
	            	$html[] = '<td></td>';
	            	$html[] = '<td></td>';
	            	$html[] = '<td></td>';
	            	$html[] = '</tr>';
	            }
            }
            $html[] = '</table>';
            
				
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

        $this->parent->get_parent()->display_footer();

    }
}
?>