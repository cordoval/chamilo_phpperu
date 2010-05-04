<?php
require_once dirname(__FILE__) . '/../../../../peer_assessment/component/viewer/wizard/inc/peer_assessment_question_display.class.php';

class IndicatorsPeerAssessmentViewerWizardPage extends PeerAssessmentViewerWizardPage
{
    private $page_number;

    function IndicatorsPeerAssessmentViewerWizardPage($name, $parent, $page_number)
    {
        parent :: PeerAssessmentViewerWizardPage($name, $parent);
        $this->page_number = $page_number;
        $this->addAction('process', new PeerAssessmentViewerWizardProcess($this));

    }

    function buildForm()
    {        
        // ********************************		
        // Prints of the list of indicators
        // ******************************** 

    	$html[] = '<div class="assessment">';
	            
        // Peer assessment title en description
        $html[] = '<h2>' . $this->get_parent()->get_peer_assessment()->get_title() . '</h2>';
            
        if ($this->get_parent()->get_peer_assessment()->has_description())
        {
            $html[] = '<div class="description">';
            $html[] = $this->get_parent()->get_peer_assessment()->get_description();
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }

        $publication_id = Request :: get('peer_assessment_publication');
        $competence_id = Request :: get('competence');
        $indicator_id = Request :: get('indicator');
        $feedback = Request :: get('feedback');
        $type = Request :: get('go');

        // Content object id
	    $peer_assessment_id = $this->get_parent()->get_peer_assessment()->get_id();
	
        // Publication object     	
	    $peer_assessment_publication = $this->get_parent()->get_peer_assessment_publication($publication_id);
        
	    // Groups
        $groups = $this->get_parent()->get_peer_assessment_publication_groups($publication_id)->as_array();
        $count_groups = sizeof($this->get_parent()->get_peer_assessment_publication_groups($publication_id)->as_array());
			          	    
	    // Users
		$users = $this->get_parent()->get_peer_assessment_publication_users($publication_id)->as_array();   
		$count_users = sizeof($this->get_parent()->get_peer_assessment_publication_users($publication_id)->as_array());
	    
		// Competence object
        $competence = RepositoryDataManager :: get_instance()->retrieve_content_object($competence_id);
        // Retrieve indicators of the selected competence
		$indicators = $this->get_parent()->get_peer_assessment_page_indicators_via_competence($this->get_parent()->get_peer_assessment(), $competence);			
        // Form	
        //$form = new FormValidator('peer_assessment_publication_mover', 'post');
	

		// Still got to fix this ...
		$count_indicators = 1;
		if($count_indicators == 0)
		{
			/*$html[] = '<div class="clear"></div>';
        	$html[] = '<div class="error-message">';
        	$html[] = Translation :: get('NoIndicatorsInCompetence');
        	$html[] = '<div class="close_message" id="closeMessage"></div>';
        	$html[] = '</div>';*/
		}
		else
		{
	        // Header table
	        $table_header[] = '<h3>' . Translation :: get('Indicator') . '</h3>';            
	        $table_header[] = '<table class="data_table">';
	        $table_header[] = '<thead>';
	        $table_header[] = '<tr>';
	        $table_header[] = '<th>'. Translation :: get('Type') .'</th>';
	        $table_header[] = '<th>' . Translation :: get('Title') . '</th>';
	        foreach($users as $user)
	        {
	        	$user_id = $user->get_user();     	
	           	$selected_user = $this->get_parent()->get_user($user_id);
	           	$full_user_name = $selected_user->get_firstname() .' '. $selected_user->get_lastname();
	        	$table_header[] = '<th>' . $full_user_name . '</th>';
	        }    
	        if($type == 'take_publication')
	        {    
	        	$table_header[] = '<th class="numeric">' . Translation :: get('Finished') . '</th>';
	        }
	        $table_header[] = '<th></th>';
	        $table_header[] = '</tr>';
	        $table_header[] = '</thead>';
	        $table_header[] = '<tbody>';
	        $this->addElement('html', implode("\n", $table_header));
	        
        
	        $renderer = $this->defaultRenderer();
	        
    		// Prints of a table row with properties foreach indicator
        	foreach($indicators as $indicator)
            {
				unset($group);
            	$group[] = $this->createElement('image', null, Theme :: get_common_image_path() . 'content_object/indicator.png');
                $group[] = $this->createElement('static', null, null, $indicator->get_title());
                
            	$url_result = 'run.php?go=take_publication&application=peer_assessment&peer_assessment_publication=' . $publication_id . '&competence=' .$competence->get_id() . '&indicator=' .$indicator->get_id();
            	$url_feedback = 'run.php?go=view_publication_results&application=peer_assessment&peer_assessment_publication=' . $publication_id . '&competence=' .$competence->get_id() . '&indicator=' .$indicator->get_id() .'&feedback=true';
            	
            	
            	// Retrieve criteria
	            $criteria = $this->get_parent()->get_peer_assessment_page_criterias_via_indicator($this->get_parent()->get_peer_assessment(), $indicator);	            	
	            $criteria_overview = $criteria;
	            	
            	$criteria_scores = array();
            	$criteria_scores[0] = Translation :: get('SelectScore');		            	
            	
            	foreach($criteria as $unserialize)
            	{
            		$criteria_score = $unserialize->get_options();
            		foreach($criteria_score as $score)
            		{
            			$criteria_scores[] = $score->get_score();
            		}
            	}
	            
	            // Take peer assessment
            	if($type == 'take_publication')
            	{
            		// Retrieve results
	            	$results = new PeerAssessmentPublicationResults();
		        	$result = $results->get_data_manager()->retrieve_peer_assessment_publication_result($indicator->get_id());
		        	
		        	foreach($users as $user)
		        	{
		        		$user_id = $user->get_user();
		        						
						$group[] = $this->createElement('select', 'c['.$competence_id.']i['.$indicator_id.']u['.$user_id.']', '', $criteria_score);	
		        	}
		        	
		        }	
		        $group[] = $this->createElement('image', null, Theme :: get_common_image_path() . 'buttons/button_cancel.png');
                $group[] = $this->createElement('image', null, Theme :: get_common_image_path() . 'action_reset.png');

                $this->addGroup($group, 'options_', null, '', false);
            }
 
		    $renderer->setElementTemplate('<tr id="options_">{element}</tr>', 'options_');
		    $renderer->setGroupElementTemplate('<td>{element}</td>', 'options_');
            
            $table_footer[] = '</tbody>';
        	$table_footer[] = '</table>';
        	$this->addElement('html', implode("\n", $table_footer));
		}
        
        
		$html[] = '</tbody>';
        $html[] = '</table>';
        
        $html[] = '<br />';
        $html[] = '</div>'; 
		
        
        
		$html[] = '<div style="float: right;">';
        $html[] = $this->createElement('style_submit_button', $this->getButtonName('submit'), Translation :: get('Submit'), array('class' => 'positive'))->toHtml();
        $html[] = '</div>';
        
		// Echo's the $html array
        echo implode("\n", $html); 
    }
}
?>