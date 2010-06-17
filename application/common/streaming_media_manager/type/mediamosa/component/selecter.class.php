<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Selecterclass
 *
 * @author jevdheyd
 */
class MediamosaStreamingMediaManagerSelecterComponent extends MediamosaStreamingMediaManager{

    function run()
    {
        $id = Request :: get(StreamingMediaManager::PARAM_STREAMING_MEDIA_ID);
        $object = $this->retrieve_streaming_media_object($id);

        $this->display_header();

        $html = array();

        $html[] = '<script type="text/javascript">';
        $connector = MediamosaStreamingMediaConnector :: get_instance($this);
        $html[] = 'window.opener.$("input[name=server_id]").val("'.$connector->get_server_id().'");';
        $html[] = 'window.opener.$("input[name=asset_id]").val("'.$object->get_id().'");';
        $html[] = 'window.opener.$("input#title").val("'. addslashes($object->get_title()) .'");';

        $description = preg_replace('/((\\\\n)+)/',"$1\"+\n\"",preg_replace("/(\r\n|\n)/",'\\n',addslashes($object->get_description())));

        $html[] = 'window.opener.$("textarea[name=description]").val("'. $description .'");';

        //$html[] = '    var element, dialog = this.getDialog();';

        //$html[] = '});';
        $html[] = 'window.close();';
        $html[] = '</script>';


        echo(implode("\n", $html));
        $this->display_footer();
    }
}
?>
