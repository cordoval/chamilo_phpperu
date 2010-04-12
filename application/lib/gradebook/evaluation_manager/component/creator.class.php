<?php
require_once dirname(__FILE__) . '/../../forms/evaluation_form.class.php';

class EvaluationManagerCreatorComponent extends EvaluationManagerComponent
{
    function run()
    {
	    $publication = $this->get_publication();
	    $failures = 0;
		$parameters[EvaluationManager :: PARAM_PUBLICATION_ID] = $publication->get_id();
		$parameter_string = base64_encode(serialize($parameters));
	    $form = new EvaluationForm(EvaluationForm :: TYPE_CREATE, $publication, $this->get_url(array(EvaluationManager :: PARAM_ACTION => EvaluationManager :: ACTION_CREATE, EvaluationManager :: PARAM_PARAMETERS => $parameter_string)), $this->get_user());
    	if(!$form->validate())
    	{
	        $trail = new BreadcrumbTrail();
	        $trail->add(new Breadcrumb($this->get_url(array())));
	        $trail->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_ACTION => EvaluationManager :: ACTION_BROWSE, 'publication' => Request :: get('publication'))), Translation :: get('WikiEvaluation')));
	    	$this->display_header($trail);
    		$form->display();
    		$this->display_footer();
    	}
		else
		{
			if(!$form->create_evaluation())
				$failures++;
		    $message = $this->get_result($failures, count($objects), 'EvaluationNotCreated', 'EvaluationsNotCreated', 'EvaluationCreated', 'EvaluationsCreated');
		                   
            $this->redirect($message, $failures, array(EvaluationManager :: PARAM_ACTION => EvaluationManager :: ACTION_BROWSE, EvaluationManager :: PARAM_PARAMETERS => $parameter_string));
		}
    	
//		$this->parameters = $this->get_parameters();
//		$type = $this->parameters['type'];
//        switch ($type)
//        {
//            case $type == 'internal_item' :
//                $this->create_internal_item();
//                break;
//            case $type == 'evaluation' : 
//            	$this->create_evaluation();
//            	break;
//            case self :: ACTION_DELETE :
//                $component = EvaluationManagerComponent :: factory('Deleter', $this);
//                break;
//            case self :: ACTION_UPDATE :
//                $component = EvaluationManagerComponent :: factory('Updater', $this);
//                break; 
//            default :
//                $component = EvaluationManagerComponent :: factory('Browser', $this);
//                break;
//        }
	}
    
    function display_header()
    {
    	$this->get_parent()->display_header();
    }
    
    function display_footer()
    {
    	$this->get_parent()->display_footer();
    }
//	
//	function create_evaluation()
//	{
//       	$evaluation = new Evaluation();
//        $evaluation->set_user_id($this->parameters['user_id']);
//        $evaluation->set_evaluator_id($this->get_user_id());
//        $evaluation->set_format_id($this->parameters['values']['format_list']);
//        $evaluation->set_evaluation_date(mktime(date()));
//        $evaluation->create();
//        echo $evaluation->get_id();
//        exit;
//	}
}
?>