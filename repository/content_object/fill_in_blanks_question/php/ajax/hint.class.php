<?php
namespace repository\content_object\fill_in_blanks_question;

use common\libraries\AjaxManager;
use common\libraries\JsonAjaxResult;
use common\libraries\Request;

use repository\RepositoryDataManager;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.fill_in_blanks_question;
 */
class FillInBlanksQuestionAjaxHint extends AjaxManager
{
    const PARAM_HINT_IDENTIFIER = 'hint_identifier';

    const PROPERTY_HINT = 'hint';
    const PROPERTY_ELEMENT_NAME = 'element_name';

    /**
     * @var ComplexContentObjectItem
     */
    private $complex_content_object_item;

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::required_parameters()
     */
    function required_parameters()
    {
        return array(self :: PARAM_HINT_IDENTIFIER);
    }

    /**
     * Set a ComplexContentObjectItem
     * @param ComplexContentObjectItem $complex_content_object_item
     */
    function set_complex_content_object_item($complex_content_object_item)
    {
        $this->complex_content_object_item = $complex_content_object_item;
    }

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        $identifiers = explode('_', $this->get_parameter(self :: PARAM_HINT_IDENTIFIER));
        $answer = $this->complex_content_object_item->get_ref_object()->get_hint_for_question($identifiers[1]);

        $result = new JsonAjaxResult(200);
        $result->set_property('hint', $answer);
        $result->display();
    }
}
?>