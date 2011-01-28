<?php
namespace application\package;

use common\libraries;
use common\libraries\EqualityCondition;
use common\libraries\WebApplication;

use admin\Registration;

use DOMDocument;
/**
 * @package application.package.package.component
 */

/**
 * package component which allows the user to browse his package_languages
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerXmlComponent extends PackageManager
{

    function run()
    {

       PackageDataManager:: generate_packages_xml();
    }
}
?>