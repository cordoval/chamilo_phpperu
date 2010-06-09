<?php
require_once dirname(__FILE__).'/../../forms/external_grade_evaluation_input_form.class.php';
class GradebookManagerExternalGradeEvaluationInputComponent extends GradebookManager
{
	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_CREATE_EXTERNAL)), Translation :: get('CreatingExternal')));
		
		$grade_form = new ExternalGradeEvaluationInputForm(ExternalGradeEvaluationInputForm :: TYPE_CREATE, $this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_CREATE_EXTERNAL_GRADE, GradebookTreeMenuDataProvider :: PARAM_ID => Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID), 'values' => Request :: get('values'))), Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID),$this->get_user(), Request :: get('values'));
		if($grade_form->validate())
		{
			$success = $grade_form->create_evaluation();
			$this->redirect($success ? Translation :: get('ExternalGradesCreated') : Translation :: get('ExternalGradesNotCreated'), ! $success, array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK));
		}
		else
    	{
	    	$this->display_header($trail);
	    	$grade_form->display();
    		$this->display_footer();
    	}
	}
}
?>