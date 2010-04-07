<?php
class EvaluationManagerDeleteComponent extends EvaluationManagerComponent
{
	function run()
	{
		$ids = $_GET[EvaluationManager :: PARAM_EVALUATION];
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
                    $message = 'SelectedEvaluationDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedEvaluationsDeleted';
                }
                else
                {
                    $message = 'SelectedEvaluationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(EvaluationManager :: PARAM_ACTION => EvaluationManager :: ACTION_BROWSE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoEvaluationsSelected')));
        }
	}
}
?>