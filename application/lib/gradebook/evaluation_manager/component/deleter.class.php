<?php
class EvaluationManagerDeleterComponent extends EvaluationManagerComponent
{
	function run()
	{
		$publication = $this->get_publication();
		$parameter_string = Request :: get('parameters');
		$parameters = unserialize(base64_decode($parameter_string));
		$ids = $parameters[EvaluationManager :: PARAM_EVALUATION_ID];
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $evaluation = $this->retrieve_evaluation($id);
                
                if (! $evaluation->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedEvaluationDeleted';
                }
                else
                {
                    $message = 'SelectedEvaluationsDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedEvaluationDeleted';
                }
                else
                {
                    $message = 'SelectedEvaluationsDeleted';
                }
            }
            
		$this->redirect($message, $failures, array(EvaluationManager :: PARAM_ACTION => EvaluationManager :: ACTION_BROWSE,EvaluationManager :: PARAM_PARAMETERS => $parameter_string));        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoEvaluationsSelected')));
        }
	}
}
?>