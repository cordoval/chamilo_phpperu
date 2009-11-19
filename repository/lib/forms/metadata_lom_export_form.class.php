<?php
/**
 * $Id: metadata_lom_export_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.forms
 */
class MetadataLomExportForm extends FormValidator
{
    private $content_object;
    private $ieee_lom_mapper;

    public function MetadataLomExportForm($content_object, $ieee_lom_mapper)
    {
        $this->content_object = $content_object;
        $this->ieee_lom_mapper = $ieee_lom_mapper;
    }

    function display_metadata($format_for_html_page = false)
    {
        if ($format_for_html_page)
        {
            echo '<div class="metadata" style="background-image: url(' . Theme :: get_common_image_path() . 'place_metadata.png);">';
            echo '<div class="title">' . $this->content_object->get_title() . '</div>';
            echo '<pre>';
        }
        else
        {
            header('Content-Type: text/xml');
        }
        
        $this->ieee_lom_mapper->export_metadata($format_for_html_page);
        
        if ($format_for_html_page)
        {
            echo '</pre>';
            echo '</div>';
        }
    }

}
?>