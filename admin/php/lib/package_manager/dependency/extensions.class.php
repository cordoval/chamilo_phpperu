<?php
/**
 * $Id: extensions.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer.dependency
 */

class ExtensionsPackageDependency extends PackageDependency
{

    function check()
    {
        $message = Translation :: get('DependencyCheckextension') . ': ' . $this->as_html();
        $this->logger->add_message($message);

        return extension_loaded($this->get_id());
    }

    function as_html()
    {
        return $this->get_id();
    }
}
?>