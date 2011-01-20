<?php
namespace application\package;

/**
 * @package application.package.package.component
 */

/**
 * package component which allows the user to browse his package_languages
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerPackageInstanceComponent extends PackageManager
{

    function run()
    {
        PackageInstanceManager :: launch($this);
    }
}
?>