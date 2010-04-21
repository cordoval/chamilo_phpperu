<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of streaming_video_clip_formclass
 *
 * @author jevdheyd
 */
class OvisStreamingVideoClipForm extends StreamingVideoClipForm{

    protected function __construct($streaming_video_clip, $form_name, $method ='post', $action = null, $extra = null, $additional_elements, $allow_new_version = true)
    {
        $this->FormValidator($form_name, $method, $action);
        
        $this->set_content_object($content_object);
        $this->set_owner_id($content_object->get_owner_id());
        $this->extra = $extra;
        $this->additional_elements = $additional_elements;
        $this->allow_new_version = $allow_new_version;
    }

    function build_creation_form()
    {
        parent :: build_creation_form();
        $this->add_footer();
    }

}
?>
