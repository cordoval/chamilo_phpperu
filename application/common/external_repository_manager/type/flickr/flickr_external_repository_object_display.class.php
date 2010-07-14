<?php
require_once dirname(__FILE__) . '/../../external_repository_object_display.class.php';

class FlickrExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function get_display_properties()
    {
        $object = $this->get_object();

        $properties = array();
        $properties[Translation :: get('Title')] = $object->get_title();

        if ($object->get_description())
        {
            $properties[Translation :: get('Description')] = $object->get_description();
        }

        $properties[Translation :: get('AvailableSizes')] = $object->get_available_sizes_string();
        $properties[Translation :: get('UploadedOn')] = DatetimeUtilities :: format_locale_date(null, $object->get_created());
        $properties[Translation :: get('Owner')] = $object->get_owner_id();

        if ($object->get_tags_string())
        {
            $properties[Translation :: get('Tags')] = $object->get_tags_string();
        }

        $properties[Translation :: get('License')] = $object->get_license_string();

        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $size = ($is_thumbnail ? FlickrExternalRepositoryObject :: SIZE_SQUARE : FlickrExternalRepositoryObject :: SIZE_MEDIUM);
        $class = ($is_thumbnail ? 'thumbnail' : 'with_border');

        $html = array();
        $html[] = '<img class="' . $class . '" src="' . $object->get_url($size) . '" />';
        return implode("\n", $html);
    }
}
?>