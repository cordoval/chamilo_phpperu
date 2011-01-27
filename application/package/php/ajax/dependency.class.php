<?php
namespace application\package;

use common\libraries;

use common\libraries\AjaxManager;
use common\libraries\JsonAjaxResult;
use common\libraries\Request;

/**
 * @author Hans De Bisschop
 * @dependency repository.content_object.assessment_multiple_choice_question;
 */
class PackageAjaxDependency extends AjaxManager
{
    const PARAM_DEPENDENCY_IDENTIFIER = 'dependency_identifier';
    
    const PROPERTY_DEPENDENCY = 'dependency';

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::required_parameters()
     */
    function required_parameters()
    {
        return array(self :: PARAM_DEPENDENCY_IDENTIFIER);
    }

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        
        $identifier = explode('_', $this->get_parameter(self :: PARAM_DEPENDENCY_IDENTIFIER));
        $dependency = PackageDataManager :: get_instance()->retrieve_dependency($identifier[1]);
        if ($dependency instanceof Dependency)
        {
            $result = new JsonAjaxResult(200);
            $result->set_property(self :: PROPERTY_DEPENDENCY, $dependency->get_default_properties());
            $result->display();
        }
        else
        {
            JsonAjaxResult :: not_found();
        
        }
    
    }
}
?>