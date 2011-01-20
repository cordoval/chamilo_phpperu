<?php
namespace application\package;

use common\libraries;

use common\libraries\AjaxManager;
use common\libraries\JsonAjaxResult;
use common\libraries\Request;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.assessment_multiple_choice_question;
 */
class PackageAjaxPackage extends AjaxManager
{
    const PARAM_PACKAGE_IDENTIFIER = 'package_identifier';
    
    const PROPERTY_PACKAGE = 'package';

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::required_parameters()
     */
    function required_parameters()
    {
        return array(self :: PARAM_PACKAGE_IDENTIFIER);
    }

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        $identifier = explode('_', $this->get_parameter(self :: PARAM_PACKAGE_IDENTIFIER));
        $package = PackageDataManager :: get_instance()->retrieve_package($identifier[1]);
        if ($package instanceof Package)
        {
            $result = new JsonAjaxResult(200);
            $result->set_property(self :: PROPERTY_PACKAGE, $package->get_default_properties());
            $result->display();
        }
        else
        {
            JsonAjaxResult :: not_found();
        
        }
    
    }
}
?>