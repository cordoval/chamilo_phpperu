<?php
class EvaluationManagerDeleterComponent extends EvaluationManager
{
	function run()
	{
		$ids = Request :: get(EvaluationManager :: PARAM_EVALUATION_ID);
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
            
		$this->redirect($message, $failures, array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_BROWSE));        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoEvaluationsSelected')));
        }
	}
}
?>