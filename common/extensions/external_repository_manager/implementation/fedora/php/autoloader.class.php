<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Utilities;

/**
 * $Id$
 * @author systho
 */
class Autoloader {

    static function load($classname) {
        $prefix = dirname(__FILE__) . '/';
        $class_hash = array(
            'fedora_external_repository_manager_course_exporter_component' => 'component/course_exporter.class.php',
            'fedora_external_repository_manager_editor_component' => 'component/editor.class.php',
            'fedora_external_repository_manager_exporter_component' => 'component/exporter.class.php',
            'fedora_external_repository_manager_uploader_component' => 'component/uploader.class.php');
        return Utilities :: load_custom_class($class_hash, $classname, $prefix);
    }

}

?>
