<?php
class DropIoExternalRepositoryManagerSelecterComponent extends DropIoExternalRepositoryManager
{

    function run()
    {
        $id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
        $object = $this->retrieve_external_repository_object($id);
        $this->display_header();
        
        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.opener.$("input[name=url]").val("http://www.drop.io/drops/about/assets?api_key=' . addslashes($object->get_id()) . '");';
        $html[] = 'window.opener.$("input#title").val("' . addslashes($object->get_title()) . '");';
        $description = preg_replace('/((\\\\n)+)/', "$1\"+\n\"", preg_replace("/(\r\n|\n)/", '\\n', addslashes($object->get_description())));
        $html[] = 'window.opener.$("textarea[name=description]").val("' . $description . '");';
        $html[] = 'window.close();';
        $html[] = '</script>';
        
        echo (implode("\n", $html));
        $this->display_footer();
    }
}
?>