<?php
namespace repository\content_object\assessment;

use common\libraries\Utilities;
use common\libraries\AjaxManager;
use common\libraries\JsonAjaxResult;
use common\libraries\Request;
use common\libraries\Path;

use repository\RepositoryDataManager;

/**
 * @package repository.content_object.assessment;
 */

class AssessmentAjaxHint extends AjaxManager
{
    const PARAM_HINT_IDENTIFIER = 'hint_identifier';

    const PROPERTY_HINT = 'hint';
    const PROPERTY_ELEMENT_NAME = 'element_name';

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::required_parameters()
     */
    function required_parameters()
    {
        return array(self :: PARAM_HINT_IDENTIFIER);
    }

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        $identifiers = explode('_', $this->get_parameter(self :: PARAM_HINT_IDENTIFIER));
        $complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($identifiers[0]);
        self :: factory($complex_content_object_item)->run();
    }

    function factory($complex_content_object_item)
    {
        $type = get_class($complex_content_object_item->get_ref_object());
        $context = Utilities :: get_namespace_from_classname($type);

        $file = Path :: get(SYS_PATH) . Path :: namespace_to_path($context) . '/php/ajax/hint.class.php';

        if (! file_exists($file) || ! is_file($file))
        {
            JsonAjaxResult :: bad_request();
        }

        require_once $file;

        $class = $type . 'AjaxHint';
        $component = new $class($this->get_user());
        $component->set_complex_content_object_item($complex_content_object_item);
        return $component;
    }
}
?>