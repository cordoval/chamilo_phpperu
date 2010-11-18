<?php
namespace common\extensions\external_repository_manager\implementation\soundcloud;

use repository;

use common\libraries\Request;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use repository\ExternalRepositorySync;

class SoundcloudExternalRepositoryManagerSelecterComponent extends SoundcloudExternalRepositoryManager
{

    function run()
    {
        $id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
        $object = $this->retrieve_external_repository_object($id);
        $this->display_header();

        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.opener.$("input[name=' . ExternalRepositorySync :: PROPERTY_EXTERNAL_REPOSITORY_ID . ']").val("' . $this->get_external_repository()->get_id() . '");';
        $html[] = 'window.opener.$("input[name=' . ExternalRepositorySync :: PROPERTY_EXTERNAL_REPOSITORY_OBJECT_ID . ']").val("' . $object->get_id() . '");';
        $html[] = 'window.opener.$("input#title").val("' . addslashes($object->get_title()) . '");';
        $description = preg_replace('/((\\\\n)+)/', "$1\"+\n\"", preg_replace("/(\r\n|\n)/", '\\n', addslashes(nl2br($object->get_description()))));
        $html[] = 'window.opener.$("textarea[name=description]").val("' . $description . '");';
        $html[] = 'window.close();';
        $html[] = '</script>';

        echo (implode("\n", $html));
        $this->display_footer();
    }
}
?>