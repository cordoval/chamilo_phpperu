<?php
class GradebookManagerDeleteExternalEvaluationComponent extends GradebookManager
{
	function run()
	{
		$ids = Request :: get(GradebookManager :: PARAM_PUBLICATION_ID);
        $failures = 0;
		if(!empty($ids))
		{
			if(!is_array($ids))
			{
				$ids = array($ids);
			}
			foreach($ids as $id)
			{
				$external_item = $this->retrieve_external_item($id);
				
				if(!$external_item->delete())
				{
	            	$failures ++;
				}
			}
		}
		
		if($failures)
		{
			if (count($ids) == 1)
            {
            	$message = 'SelectedExternalEvaluationNotDeleted';
			}
            else
            {
            	$message = 'SelectedExternalEvaluationsNotDeleted';
			}
		}
		else
		{
			if (count($ids) == 1)
            {
            	$message = 'SelectedExternalEvaluationDeleted';
			}
            else
            {
            	$message = 'SelectedExternalEvaluationsDeleted';
			}
		}
		
	$this->redirect(Translation :: get($message), ($failures ? true : false), array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME, GradebookManager :: PARAM_PUBLICATION_TYPE => Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE), GradebookManager :: PARAM_PUBLICATION_APP => Request :: get(GradebookManager :: PARAM_PUBLICATION_APP)));
	}
}
?>