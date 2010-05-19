<?php
require_once dirname(__FILE__).'/../../forms/external_item_form.class.php';
require_once dirname(__FILE__).'/../../forms/external_grade_evaluation_input_form.class.php';
require_once dirname(__FILE__) . '/../../data_provider/gradebook_tree_menu_data_provider.class.php';

class GradebookManagerExternalCreatorComponent extends GradebookManager
{
	private $evaluation_input_form = false;
	function run()
	{
		$this->evaluation_input_form = Request :: get(GradebookManager :: ACTION_CREATE_EXTERNAL_GRADE);
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_CREATE_EXTERNAL)), Translation :: get('CreatingExternal')));
		if(!$evaluation_input_form)
			$form = new ExternalItemForm(ExternalItemForm :: TYPE_CREATE, $this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_CREATE_EXTERNAL, GradebookTreeMenuDataProvider :: PARAM_ID => Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID))), Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID),$this->get_user());
    	
    	if($form->validate() || $this->evaluation_input_form)
    	{
    		//dump($form->exportValues());exit;
    		$grade_form = new ExternalGradeEvaluationInputForm(ExternalItemForm :: TYPE_CREATE, $this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_CREATE_EXTERNAL, GradebookTreeMenuDataProvider :: PARAM_ID => Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID), GradebookManager :: ACTION_CREATE_EXTERNAL_GRADE => true)), Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID),$this->get_user(), $form->exportValues());
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
    		
//    		$success = $form->create_evaluation();
//            $this->redirect($success ? Translation :: get('ExternalGradesCreated') : Translation :: get('ExternalGradesNotCreated'), ! $success, array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK));
    	}
    	else
    	{
	    	$this->display_header($trail);
	    	$form->display();
    		$this->display_footer();
    	}
	}
}
?>