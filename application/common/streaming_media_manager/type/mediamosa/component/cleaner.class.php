<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cleanerclass
 *
 * @author jevdheyd
 */
class MediamosaStreamingMediaManagerCleanerComponent extends MediamosaStreamingMediaManager {

    function run(){
        $connector = MediamosaStreamingMediaConnector :: get_instance($this);

        $this->display_header();

        if($result = $connector->clean())
        {
            echo 'ok';
        }
        else
        {
            echo var_dump($result);
        }

        $this->display_footer();
    }
}
?>
