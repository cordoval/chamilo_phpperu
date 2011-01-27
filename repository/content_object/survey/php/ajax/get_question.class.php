<?php
namespace repository\content_object\survey;

use common\libraries\Utilities;
use common\libraries\AjaxManager;
use common\libraries\JsonAjaxResult;
use common\libraries\Request;
use common\libraries\Path;
use repository\content_object\survey_page\SurveyPage;

use repository\RepositoryDataManager;

/**
 * @package repository.content_object.assessment;
 */

class SurveyAjaxGetQuestion extends AjaxManager
{
    
    const PARAM_COMPLEX_QUESTION_ID = 'complex_question_id';
    const PARAM_USER_ID = 'user_id';
    const PARAM_CONTEXT_PATH = 'context_path';
    const PARAM_QUESTION_TITLE = 'title';
    const PARAM_QUESTION_TYPE = 'type';

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::required_parameters()
     */
    function required_parameters()
    {
        return array(self :: PARAM_COMPLEX_QUESTION_ID, self :: PARAM_CONTEXT_PATH, self :: PARAM_USER_ID);
    }

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        
        $complex_question_id = $this->get_parameter(self :: PARAM_COMPLEX_QUESTION_ID);
        $complex_question = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_question_id);
        $question = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_question->get_ref());
		$user_id = $this->get_parameter(self :: PARAM_USER_ID);
        $context_path = $this->get_parameter(self :: PARAM_CONTEXT_PATH);
		$title = Survey :: parse($user_id, $context_path, $question->get_title());
       	$type = $question->get_type_name();
        
        $result = new JsonAjaxResult(200);
        $result->set_property(self :: PARAM_QUESTION_TITLE, $title);
        $result->set_property(self :: PARAM_QUESTION_TYPE, $type);
        
        $result->display();
    }

}
?>