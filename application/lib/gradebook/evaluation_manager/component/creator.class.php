<?php
require_once dirname(__FILE__) . '/../../forms/evaluation_form.class.php';
require_once dirname(__FILE__) . '/../../evaluation.class.php';
require_once dirname(__FILE__) . '/../../grade_evaluation.class.php';

class EvaluationManagerCreatorComponent extends EvaluationManager
{
    function run()
    {
	    $publication_id = $this->get_publication_id();
	    $publisher_id = $this->get_publisher_id();
	    $failures = 0;
		$evaluation = new Evaluation();
		$grade_evaluation = new GradeEvaluation();
    	$form = new EvaluationForm(EvaluationForm :: TYPE_CREATE, $evaluation, $grade_evaluation, $publication_id, $publisher_id, $this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_CREATE)), $this->get_user());
    
    	if($form->validate())
    	{
    		$success = $form->create_evaluation();
            $this->redirect($success ? Translation :: get('EvaluationCreated') : Translation :: get('EvaluationNotCreated'), ! $success, array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_BROWSE));
    	}
    	else
    	{
    		$trail = $this->get_trail();
	    	$trail->add(new Breadcrumb($this->get_url(array()), Translation :: get('CreateEvaluation')));
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