<?php
require_once dirname(__FILE__) . '/../../external_repository_object_display.class.php';

class GoogleDocsExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function get_display_properties()
    {
        $object = $this->get_object();
        
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('LastViewed')] = DatetimeUtilities :: format_locale_date(null, $object->get_viewed());
        $properties[Translation :: get('LastModifiedBy')] = $object->get_modifier_id();        

        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {
        if ($is_thumbnail)
        {
            return parent :: get_preview($is_thumbnail);
        }
        else
        {
            $object = $this->get_object();
            
            switch ($object->get_type())
            {
                case 'pdf' :
                    $url = 'http://docs.google.com/gview?a=v&pid=explorer&chrome=false&api=true&embedded=true&srcid=' . $object->get_resource_id() . '&hl=en';
                    break;
                case 'document' :
                    $url = 'https://docs.google.com/View?docID='. $object->get_resource_id() .'&revision=_latest&hgd=1&pageview=1';
                    break;
                case 'presentation' :
                    $url = 'https://docs.google.com/present/view?id='. $object->get_resource_id();
                    break;
                case 'spreadsheet' :
                    $url = 'http://spreadsheets.google.com/ccc?key='. $object->get_resource_id() .'&output=html&widget=true';
                    break;
                default :
                    $url = null;
                    break;
            }
            
            if ($url)
            {
                $html = array();
                $html[] = '<iframe class="preview" src="' . $url . '"></iframe>';
                return implode("\n", $html);
            }
            else
            {
                return parent :: get_preview($is_thumbnail);
            }
        }
    }
}
?>