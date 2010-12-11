<?php
namespace repository\content_object\assessment_multiple_choice_question;

use common\libraries\JsonAjaxResult;
use common\libraries\Request;

use repository\RepositoryDataManager;

/**
 * @package repository.content_object.assessment_multiple_choice_question;
 */
require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';

$complex_content_object_item_id = Request :: post('complex_content_object_item_id');

if (! isset($complex_content_object_item_id))
{
    $result = new JsonAjaxResult(500);
    $result->display();
}
else
{
    $complex_content_object_item = RepositoryDataManager::get_instance()->retrieve_complex_content_object_item($complex_content_object_item_id);

    $result = new JsonAjaxResult(200);
    $result->set_property('hint', $complex_content_object_item->get_ref_object()->get_hint());
    $result->display();
}
?>