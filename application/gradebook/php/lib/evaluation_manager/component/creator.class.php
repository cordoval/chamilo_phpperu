<?php
namespace application\gradebook;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;

class EvaluationManagerCreatorComponent extends EvaluationManager
{

    function run()
    {
        $publication_id = $this->get_publication_id();
        $publisher_id = $this->get_publisher_id();
        $failures = 0;
        $evaluation = new Evaluation();
        $grade_evaluation = new GradeEvaluation();
        $form = new EvaluationForm(EvaluationForm :: TYPE_CREATE, $evaluation, $grade_evaluation, $publication_id, $publisher_id, $this->get_url(array(
                EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_CREATE)), $this->get_user());

        if ($form->validate())
        {
            $success = $form->create_evaluation();
            $this->redirect($success ? Translation :: get('EvaluationCreated', null, Utilities :: COMMON_LIBRARIES) : Translation :: get('EvaluationNotCreated', null, Utilities :: COMMON_LIBRARIES), ! $success, array(
                    EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_BROWSE));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array()), Translation :: get('CreateEvaluation', null, Utilities :: COMMON_LIBRARIES)));
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
        $values = $form->getSubmitValues();
        if (! empty($values))
        {
            $form->set_allow_creation(true);
        }
        else
        {
            $form->set_allow_creation(false);
        }
    }

}

?>