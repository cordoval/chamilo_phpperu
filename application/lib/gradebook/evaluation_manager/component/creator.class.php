<?php
require_once dirname(__FILE__) . '/../../forms/evaluation_form.class.php';
require_once dirname(__FILE__) . '/../../evaluation.class.php';
require_once dirname(__FILE__) . '/../../grade_evaluation.class.php';

class EvaluationManagerCreatorComponent extends EvaluationManagerComponent
{
    function run()
    {
	    $publication_id = $this->get_parent()->get_parameter(EvaluationManager :: PARAM_PUBLICATION_ID);
	    $publisher_id = $this->get_parent()->get_parameter(EvaluationManager :: PARAM_PUBLISHER_ID);
	    $failures = 0;
	    $parameters[EvaluationManager :: PARAM_PUBLICATION_ID] = $this->get_parent()->get_parameter(EvaluationManager :: PARAM_PUBLICATION_ID);
		$parameter_string = base64_encode(serialize($parameters));
		$evaluation = new Evaluation();
		$grade_evaluation = new GradeEvaluation();
    	$form = new EvaluationForm(EvaluationForm :: TYPE_CREATE, $evaluation, $grade_evaluation, $publication_id, $publisher_id, $this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_CREATE, EvaluationManager :: PARAM_PARAMETERS => $parameter_string)), $this->get_user());
    
    	if($form->validate())
    	{
			if(!$form->create_evaluation())
				$failures++;
				
	    	$message = $this->get_result($failures, count($objects), 'EvaluationNotCreated', 'EvaluationsNotCreated', 'EvaluationCreated', 'EvaluationsCreated');
	    
            $this->redirect($message, $failures, array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_BROWSE, EvaluationManager :: PARAM_PARAMETERS => $parameter_string));
    	}
    	else
    	{
    		$trail = $this->get_parent()->get_trail();
	    	$trail->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_PARAMETERS => $parameter_string)), Translation :: get('CreateEvaluation')));
	    	$this->display_header($trail);
	    	$form->display();
    		$this->display_footer();
    	}
    	$values = $form->getSubmitValues();
		if (!empty($values))
		{
			$form->set_allow_creation(true);
		}
		else
		{
			$form->set_allow_creation(false);
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