<?php
namespace common\extensions\external_repository_manager;

use common\libraries\Utilities;

abstract class ExternalRepositoryObjectDisplay
{
    /**
     * @var ExternalRepositoryObject
     */
    private $object;

    /**
     * @param ExternalRepositoryObject $object
     */
    function ExternalRepositoryObjectDisplay($object)
    {
        $this->object = $object;
    }

    /**
     * @param ExternalRepositoryObject $object
     * @return ExternalRepositoryObjectDisplay
     */
    static function factory($object)
    {
        $type = $object->get_object_type();
        $class = 'common\extensions\external_repository_manager\implementation\\' . $type . '\\' . Utilities :: underscores_to_camelcase($type) . 'ExternalRepositoryObjectDisplay';
        require_once dirname(__FILE__) . '/../implementation/' . $type . '/php/' . $type . '_external_repository_object_display.class.php';
        return new $class($object);
    }

    /**
     * @return ExternalRepositoryObject
     */
    function get_object()
    {
        return $this->object;
    }

    /**
     * @return string
     */
    function as_html()
    {
        $html = array();
        $html[] = $this->get_title();
        $html[] = $this->get_preview() . '<br/>';
        $html[] = $this->get_properties_table();

        return implode("\n", $html);
    }

    /**
     * @return string
     */
    function get_properties_table()
    {
        $object = $this->get_object();
        $properties = $this->get_display_properties();

        $table = new PropertiesTable($properties);
        $table->setAttribute('style', 'margin-top: 1em; margin-bottom: 0;');
        return $table->toHtml();
    }

    /**
     * @return array
     */
    function get_display_properties()
    {
        $object = $this->get_object();

        $properties = array();
        $properties[Translation :: get('Title')] = $object->get_title();

        if ($object->get_description())
        {
            $properties[Translation :: get('Description')] = $object->get_description();
        }

        $properties[Translation :: get('UploadedOn')] = DatetimeUtilities :: format_locale_date(null, $object->get_created());
        if ($object->get_created() != $object->get_modified())
        {
            $properties[Translation :: get('ModifiedOn')] = DatetimeUtilities :: format_locale_date(null, $object->get_modified());
        }
        $properties[Translation :: get('Owner')] = $object->get_owner_id();

        return $properties;
    }

    /**
     * @return string
     */
    function get_title()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_title() . '</h3>';
    }

    /**
     * @param boolean $is_thumbnail
     * @return string
     */
    function get_preview($is_thumbnail = false)
    {
        if ($is_thumbnail)
        {
            $class = 'no_thumbnail';
            $image = Theme :: get_common_image('thumbnail');
            $text = '<h3>' . Translation :: get('NoThumbnailAvailable') . '</h3>';
        }
        else
        {
            $class = 'no_preview';
            $image = Theme :: get_common_image('preview');
            $text = '<h1>' . Translation :: get('NoPreviewAvailable') . '</h1>';
        }

        $html = array();
        $html[] = '<div class="' . $class . '">';
        $html[] = '<div class="background">';
        $html[] = $image;
        $html[] = $text;
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        return implode("\n", $html);
    }
}
?>