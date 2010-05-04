<?php
require_once dirname(__FILE__) . '/../../../../peer_assessment/component/viewer/wizard/inc/peer_assessment_question_display.class.php';

class IndicatorsPeerAssessmentViewerWizardPage extends PeerAssessmentViewerWizardPage
{
    private $page_number;
    private $questions;

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
	    
        		
        // Header table
        $html[] = '<h3>' . Translation :: get('Indicator') . '</h3>';            
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>'. Translation :: get('Type') .'</th>';
        $html[] = '<th>' . Translation :: get('Title') . '</th>';
        foreach($users as $user)
        {
        	$user_id = $user->get_user();     	
           	$selected_user = $this->get_parent()->get_user($user_id);
           	$full_user_name = $selected_user->get_firstname() .' '. $selected_user->get_lastname();
        	$html[] = '<th>' . $full_user_name . '</th>';
        }    
        if($type == 'take_publication')
        {    
        	$html[] = '<th class="numeric">' . Translation :: get('Finished') . '</th>';
        }
        $html[] = '<th></th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        $html[] = '</tbody>';
        $html[] = '</table>'; 
        
        
        
        // Competence object
        $competence = RepositoryDataManager :: get_instance()->retrieve_content_object($competence_id);
        // Form	
        $form = new FormValidator('peer_assessment_publication_mover', 'post');
	        

		
        
		$html[] = '<div style="float: right; margin-top: 15px">';
        $html[] = $this->createElement('style_submit_button', $this->getButtonName('submit'), Translation :: get('Submit'), array('class' => 'positive'))->toHtml();
        $html[] = '</div>';
        
		// Echo's the $html array
        echo implode("\n", $html); 
    }
}
?>