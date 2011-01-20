<?php
namespace application\package;

use admin;

use common\libraries;

use common\libraries\AjaxManager;
use common\libraries\JsonAjaxResult;
use common\libraries\Request;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.assessment_multiple_choice_question;
 */
class PackageAjaxPackageSeverityOptions extends AjaxManager
{
    const PROPERTY_SEVERITY = 'severity';

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::required_parameters()
     */
    function required_parameters()
    {
        return array();
    }

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        $result = new JsonAjaxResult(200);
        $result->set_property(self :: PROPERTY_SEVERITY, admin\PackageDependency::get_severity_options());
        $result->display();
    }
}
?>