<?php
require_once dirname(__FILE__) . '/../../external_repository_object.class.php';

class GoogleDocsExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'google_docs';
    
    const PROPERTY_VIEWED = 'viewed';
    const PROPERTY_CONTENT = 'content';
    const PROPERTY_MODIFIER_ID = 'modifier_id';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_VIEWED, self :: PROPERTY_CONTENT, self :: PROPERTY_MODIFIER_ID));
    }

    function get_viewed()
    {
        return $this->get_default_property(self :: PROPERTY_VIEWED);
    }

    function set_viewed($viewed)
    {
        return $this->set_default_property(self :: PROPERTY_VIEWED, $viewed);
    }

    function get_content()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT);
    }

    function set_content($content)
    {
        return $this->set_default_property(self :: PROPERTY_CONTENT, $content);
    }

    function get_modifier_id()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIER_ID);
    }

    function set_modifier_id($modifier_id)
    {
        return $this->set_default_property(self :: PROPERTY_MODIFIER_ID, $modifier_id);
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

    function get_export_types()
    {
        switch ($this->get_type())
        {
            case 'document' :
                return array('pdf', 'odt', 'doc');
                break;
            case 'presentation' :
                return array('pdf', 'ppt', 'swf');
                break;
            case 'spreadsheet' :
                return array('pdf', 'ods', 'xls');
                break;
            case 'pdf' :
                return array('pdf');
                break;
        }
    }

    /**
     * @return string
     */
    function get_resource_id()
    {
        $id = explode(':', urldecode($this->get_id()));
        return $id[1];
    }

//    /**
//     * @param string $export_format
//     * @return string
//     */
//    function get_export_url($export_format = 'pdf')
//    {
//        if (! in_array($export_format, $this->get_export_types()))
//        {
//            $export_format = 'pdf';
//        }
//        
//        switch ($this->get_type())
//        {
//            case 'document' :
//                return 'http://docs.google.com/feeds/download/'. $this->get_type() .'s/Export?docID='. $this->get_resource_id() .'&exportFormat=' . $export_format;
//                break;
//            case 'presentation' :
//                return 'http://docs.google.com/feeds/download/'. $this->get_type() .'s/Export?docID='. $this->get_resource_id() .'&exportFormat=' . $export_format;
//                break;
//            case 'spreadsheet' :
//                return 'http://spreadsheets.google.com/feeds/download/'. $this->get_type() .'s/Export?key='. $this->get_resource_id() .'&fmcmd=' . $export_format;
//                break;
//            case 'pdf' :
//                // Get the document's content link entry.
//                //return array('pdf');
//                break;
//        }
//    }
}
?>