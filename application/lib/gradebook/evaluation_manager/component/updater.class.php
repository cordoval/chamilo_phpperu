<?php
require_once dirname(__FILE__) . '/../../forms/evaluation_form.class.php';
class EvaluationManagerUpdaterComponent extends EvaluationManagerComponent
{
	function run()
	{        
        $evaluation = $this->retrieve_evaluation(Request :: get(EvaluationManager :: PARAM_EVALUATION));
        $publication = $this->get_publication();
        //$form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $evaluation->get_content_object(), 'edit', 'post', $this->get_url(array(EvaluationManager :: PARAM_EVALUATION => $evaluation->get_id())));
        //if ($form->validate() || Request :: get('validated'))
        //{
         //   if (! Request :: get('validated'))
          //      $success = $form->update_content_object();
            
            $pub_form = new EvaluationForm(EvaluationForm :: TYPE_EDIT, $publication, $this->get_url(array(EvaluationManager :: PARAM_EVALUATION => $evaluation->get_id(), 'validated' => 1, EvaluationManager :: PARAM_PUBLICATION => $publication->get_id())), $this->get_user());
            if ($pub_form->validate())
            {
                $success = $pub_form->update_evaluation($evaluation->get_id());
                $this->redirect($success ? Translation :: get('EvaluationUpdated') : Translation :: get('EvaluationNotUpdated'), ! $success, array(EvaluationManager :: PARAM_ACTION => EvaluationManager :: ACTION_BROWSE, EvaluationManager :: PARAM_PUBLICATION => $publication->get_id()));
            }
            else
            {
                $pub_form->display();
            }
        
        /*}
        else
        {
            $form->display();
        }*/
	}
}
?>