<?php

namespace application\gradebook;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Request;

class GradebookInternalItemForm extends FormValidator
{

    private $calculated_applications = array('assessment', 'learning_path');

    function __construct()
    {

    }

    function build_evaluation_question($form)
    {
        $form->addElement('checkbox', 'evaluation', Translation :: get('CreateEvaluation', null, Utilities::COMMON_LIBRARIES));
    }

    function create_internal_item($publication_id, $calculated = 0, $category = null)
    {
        $internal_item = new InternalItem();
        $internal_item->set_application(Request :: get('application'));
        $internal_item->set_publication_id($publication_id);
        $internal_item->set_calculated($calculated);
        $internal_item->set_category($category);
        $internal_item->create();
    }

    function is_application_result_calculated($application)
    {
        return in_array($application, $this->calculated_applications);
    }

}

?>