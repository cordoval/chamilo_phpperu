<?php

require_once dirname(__FILE__).'/../../forms/gradebook_form.class.php';

class GradebookManagerGradebookCreatorComponent extends GradebookManagerComponent
{
	function run()
	{

		$trail = new BreadcrumbTrail();

		if (!GradebookRights :: is_allowed(GradebookRights :: ADD_RIGHT, GradebookRights :: LOCATION_BROWSER, 'gradebook_component'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}


		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME)), Translation :: get('GradeBook')));
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK)), Translation :: get('BrowseGradeBook')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateGradeBook')));


		$gradebook = new Gradebook();
		$form = new GradebookForm(GradebookForm :: TYPE_CREATE, $gradebook, $this->get_url(), $this->get_user());
			

		if($form->validate())
		{
				
				
			$success = $form->create_gradebook();
			if($success)
			{

				$gradebook = $form->get_gradebook();
				$this->redirect(Translation :: get('GradebookCreated'), (false), array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_GRADEBOOK, GradebookManager :: PARAM_GRADEBOOK_ID => $gradebook->get_id()));
					
			}
			else
			{
				$this->redirect(Translation :: get('GradebookNotCreated'), (true), array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK));
			}
		}
		else
		{
				
			$this->display_header($trail);
			echo '<div style="clear: both;">';
			echo $form->toHtml();
			echo '</div>';
			$this->display_footer();
				
		}
	}
}
?>