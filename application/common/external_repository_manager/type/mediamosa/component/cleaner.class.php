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
class MediamosaExternalRepositoryManagerCleanerComponent extends MediamosaExternalRepositoryManager {

    function run(){
        $connector = MediamosaExternalRepositoryConnector :: get_instance($this);

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
