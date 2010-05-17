<?php
class GradebookManagerDeleteExternalEvaluationComponent extends GradebookManager
{
	function run()
	{
		$id = Request :: get(GradebookManager :: PARAM_PUBLICATION_ID);
        $failures = 0;
		if($id)
		{
			$external_item = $this->retrieve_external_item($id);
			
			if(!$external_item->delete())
			{
            	$failures ++;
			}
		}
		
		if($failures)
		{
			$message = 'SelectedExternalEvaluationNotDeleted';
		}
		else
		{
			$message = 'SelectedExternalEvaluationDeleted';
		}
		
	$this->redirect(Translation :: get($message), ($failures ? true : false), array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME, GradebookManager :: PARAM_PUBLICATION_TYPE => Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE), GradebookManager :: PARAM_PUBLICATION_APP => Request :: get(GradebookManager :: PARAM_PUBLICATION_APP)));
	}
}
?>