<?php
namespace application\package;

use admin;

use common\libraries;

use common\libraries\AjaxManager;
use common\libraries\JsonAjaxResult;
use common\libraries\Request;

/**
 * @author Hans De Bisschop
 * @dependency repository.content_object.assessment_multiple_choice_question;
 */
class PackageAjaxPackageDependency extends AjaxManager
{
    const PARAM_DEPENDENCY_IDENTIFIER = 'dependency_identifier';
    
    const PROPERTY_DEPENDENCY = 'dependency';
    const PROPERTY_TYPE = 'type';

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
        switch($identifier[0])
        {
            case PackageDependency :: get_dependency_type_string(PackageDependency::TYPE_DEPENDENCY) : 
                $dependency = PackageDataManager :: get_instance()->retrieve_dependency($identifier[1]);
                break;
            case PackageDependency :: get_dependency_type_string(PackageDependency::TYPE_PACKAGE) : 
                $dependency = PackageDataManager :: get_instance()->retrieve_package($identifier[1]);
                break;    
        }
        if ($dependency instanceof Dependency || $dependency instanceof Package)
        {
            $result = new JsonAjaxResult(200);
            $result->set_property(self :: PROPERTY_DEPENDENCY, $dependency->get_default_properties());
            $result->set_property(self :: PROPERTY_TYPE, $identifier[0]);
            $result->display();
        }
        else
        {
            JsonAjaxResult :: not_found();
        
        }
    
    }
}
?>