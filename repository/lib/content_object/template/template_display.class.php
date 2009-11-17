<?php
/**
 * $Id: template_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.template
 */
/**
 * This class can be used to display templates
 */
class TemplateDisplay extends ContentObjectDisplay
{

    function get_full_html()
    {
        $html = parent :: get_full_html();
        $object = $this->get_content_object();
        return str_replace(self :: DESCRIPTION_MARKER, '<div class="template_design" style="margin-top: 1em;">' . $object->get_design() . '</div>' . self :: DESCRIPTION_MARKER, $html);
    }
}
?>