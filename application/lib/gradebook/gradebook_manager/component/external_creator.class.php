<?php
require_once dirname(__FILE__).'/../../forms/create_external_item_form.class.php';
require_once dirname(__FILE__) . '/../../data_provider/gradebook_tree_menu_data_provider.class.php';

class GradebookManagerExternalCreatorComponent extends GradebookManager
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_CREATE_EXTERNAL)), Translation :: get('CreatingExternal')));
		
		$form = new CreateExternalItemForm(CreateExternalItemForm :: TYPE_CREATE, $this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_CREATE_EXTERNAL, GradebookTreeMenuDataProvider :: PARAM_ID => Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID))), Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID),$this->get_user());
    
    	if($form->validate())
    	{
    		$success = $form->create_evaluation();
            $this->redirect($success ? Translation :: get('ExternalGradesCreated') : Translation :: get('ExternalGradesNotCreated'), ! $success, array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK));
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