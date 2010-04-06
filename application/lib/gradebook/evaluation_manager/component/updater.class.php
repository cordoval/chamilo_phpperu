<?php
class EvaluationManagerUpdaterComponent extends SubManagerComponent
{
	function run()
	{        
        $evaluation = $this->retrieve_evaluation(Request :: get(EvaluationManager :: PARAM_EVALUATION));
        
        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $evaluation->get_content_object(), 'edit', 'post', $this->get_url(array(EvaluationManager :: PARAM_EVALUATION => $evaluation->get_id())));
        if ($form->validate() || Request :: get('validated'))
        {
            if (! Request :: get('validated'))
                $success = $form->update_content_object();
            
            $pub_form = new EvaluationForm(WikiPublicationForm :: TYPE_EDIT, $evaluation, $this->get_url(array(EvaluationManager :: PARAM_EVALUATION => $evaluation->get_id(), 'validated' => 1)), $this->get_user());
            if ($pub_form->validate())
            {
                $success = $pub_form->update_evaluation();
                $this->redirect($success ? Translation :: get('EvaluationUpdated') : Translation :: get('EvaluationNotUpdated'), ! $success, array(EvaluationManager :: PARAM_ACTION => EvaluationManager :: ACTION_BROWSE));
            }
            else
            {
                $pub_form->display();
            }
        
        }
        else
        {
            $form->display();
        }
	}
}