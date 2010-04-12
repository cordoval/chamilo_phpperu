<?php
require_once dirname(__FILE__) . '/../../forms/evaluation_form.class.php';
require_once dirname(__FILE__) . '/../../evaluation.class.php';
require_once dirname(__FILE__) . '/../../grade_evaluation.class.php';

class EvaluationManagerUpdaterComponent extends EvaluationManagerComponent
{
	function run()
	{   
		$parameters = unserialize(base64_decode(Request :: get('parameters')));
		$evaluation_id = $parameters[EvaluationManager :: PARAM_EVALUATION_ID];
        $evaluation = $this->retrieve_evaluation($evaluation_id);
        $grade_evaluation = $this->retrieve_grade_evaluation($evaluation_id);
        $publication = $this->get_publication();
        $parameter_string = base64_encode(serialize($parameters));
        //$form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $evaluation->get_content_object(), 'edit', 'post', $this->get_url(array(EvaluationManager :: PARAM_EVALUATION => $evaluation->get_id())));
        //if ($form->validate() || Request :: get('validated'))
        //{
         //   if (! Request :: get('validated'))
          //      $success = $form->update_content_object();
            
            $pub_form = new EvaluationForm(EvaluationForm :: TYPE_EDIT, $evaluation, $grade_evaluation, $publication, $this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ID => $evaluation->get_id(), EvaluationManager :: PARAM_PARAMETERS => $parameter_string)), $this->get_user());
            if ($pub_form->validate())
            {
                $success = $pub_form->update_evaluation($evaluation->get_id());
                $this->redirect($success ? Translation :: get('EvaluationUpdated') : Translation :: get('EvaluationNotUpdated'), ! $success, array(EvaluationManager :: PARAM_ACTION => EvaluationManager :: ACTION_BROWSE, EvaluationManager :: PARAM_PARAMETERS => $parameter_string));
            }
            else
            {
		        $trail = new BreadcrumbTrail();
		        $trail->add(new Breadcrumb($this->get_url(array())));
		        $trail->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_ACTION => EvaluationManager :: ACTION_BROWSE, 'publication' => Request :: get('publication'))), Translation :: get('WikiEvaluation')));
            	$this->display_header($trail);
                $pub_form->display();
            	$this->display_footer();
            }
        
        /*}
        else
        {
            $form->display();
        }*/
	}
}
?>