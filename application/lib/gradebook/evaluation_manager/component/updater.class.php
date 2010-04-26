<?php
require_once dirname(__FILE__) . '/../../forms/evaluation_form.class.php';
require_once dirname(__FILE__) . '/../../evaluation.class.php';
require_once dirname(__FILE__) . '/../../grade_evaluation.class.php';

class EvaluationManagerUpdaterComponent extends EvaluationManager
{
	function run()
	{   
	    $publication_id = $this->get_publication_id();
	    $publisher_id = $this->get_publisher_id();
		$evaluation_id = Request :: get(EvaluationManager :: PARAM_EVALUATION_ID);
        $evaluation = $this->retrieve_evaluation($evaluation_id);
        $grade_evaluation = $this->retrieve_grade_evaluation($evaluation_id);
        //$form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $evaluation->get_content_object(), 'edit', 'post', $this->get_url(array(EvaluationManager :: PARAM_EVALUATION => $evaluation->get_id())));
        //if ($form->validate() || Request :: get('validated'))
        //{
         //   if (! Request :: get('validated'))
          //      $success = $form->update_content_object();
            
            $pub_form = new EvaluationForm(EvaluationForm :: TYPE_EDIT, $evaluation, $grade_evaluation, $publication_id, $publisher_id, $this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_UPDATE, EvaluationManager :: PARAM_EVALUATION_ID => $evaluation_id)), $this->get_user());
            if ($pub_form->validate())
            {
                $success = $pub_form->update_evaluation($evaluation->get_id());
                $this->redirect($success ? Translation :: get('EvaluationUpdated') : Translation :: get('EvaluationNotUpdated'), ! $success, array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_BROWSE));
            }
            else
            {
	    		$trail = $this->get_trail();
	    		$trail->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ID => $evaluation_id)), Translation :: get('UpdateEvaluation')));
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