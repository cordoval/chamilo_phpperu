<?php
namespace repository\content_object\fill_in_blanks_question;

use common\libraries\JsonAjaxResult;
use common\libraries\Request;

use repository\RepositoryDataManager;

/**
 * @package repository.content_object.assessment_multiple_choice_question;
 */
require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';

$complex_content_object_item_id = Request :: post('complex_content_object_item_id');
$position = Request :: post('position');

if (! isset($complex_content_object_item_id) || ! isset($position))
{
    $result = new JsonAjaxResult(500);
    $result->display();
}
else
{
    $complex_content_object_item = RepositoryDataManager::get_instance()->retrieve_complex_content_object_item($complex_content_object_item_id);
    $answer = $complex_content_object_item->get_ref_object()->get_hint_for_question($position);

    $result = new JsonAjaxResult(200);
    $result->set_property('hint', $answer);
    $result->display();
}
?>