<?php
namespace repository;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\SubManager;
use common\libraries\Display;
use common\libraries\Translation;

use Exception;

abstract class ComplexDisplayPreview extends SubManager
{

    /**
     * @param Application|SubManager $parent
     */
    function ComplexDisplayPreview($parent)
    {
        parent :: __construct($parent);
    }

    /**
     * Pseudo-submanager without components, so get_application_component_path
     * has an empty implementation.
     */
    function get_application_component_path()
    {
    }

    /**
     * @param string $type
     * @param Application $application
     */
    static function launch($type, $application)
    {
        $file = Path :: get_repository_content_object_path() . $type . '/php/display/' . $type . '_complex_display_preview.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ComplexDisplayPreviewTypeDoesNotExist', array(
                    'type' => $type)));
        }

        require_once $file;

        $class = ContentObject :: get_content_object_type_namespace($type) . '\\' . Utilities :: underscores_to_camelcase($type) . 'ComplexDisplayPreview';
        $preview = new $class($application);
        $preview->run();
    }

    /**
     * Abstract method implementened by every extension.
     * Displays the actual complex display.
     */
    abstract function run();

    /**
     * Method always has to be implemented for a class
     * implementing the ComplexDisplay
     *
     * @return ContentObject
     */
    function get_root_content_object()
    {
        return $this->get_parent()->get_root_content_object();
    }

    /**
     * Inform the user that the requested functionality
     * is not available in preview mode
     *
     * @param string $message
     */
    function not_available($message)
    {
        $this->display_header();
        Display :: normal_message('ImpossibleInPreviewMode');
        $this->display_footer();
        exit();
    }
}
?>