<?php

/**
 * Description of StreamingVideoClipForm class
 *
 * @author jevdheyd
 */
abstract class StreamingVideoClipForm extends ContentObjectForm {

    abstract protected function build_creation_form();

    abstract function create_content_object();

    abstract protected function build_editing_form();

    abstract function update_content_object();
}
?>