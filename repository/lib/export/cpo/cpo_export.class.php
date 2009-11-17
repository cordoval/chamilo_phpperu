<?php
/**
 * $Id: cpo_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.cpo
 */
require_once 'XML/Serializer.php';

/**
 * Exports learning object to the chamilo learning object format (xml)
 */
class CpoExport extends ContentObjectExport
{
    /**
     * @var RepositoryDataManager
     */
    private $rdm;
    /**
     * @var DOMDocument
     */
    private $doc;
    
    /*
	 * Array of files to export
	 */
    private $files;
    
    /**
     * Array of hotpotatoes files to export
     */
    private $hotpot_files;
    
    /**
     * Array of scorm files to export
     */
    private $scorm_files;
    
    /*
	 * The <learnings_objects> tag in the xml file
	 */
    private $root;
    
    /**
     * Array of already exported learning objects to prevent doubles
     */
    private $exported_content_objects;

    function CpoExport($content_object)
    {
        $this->rdm = RepositoryDataManager :: get_instance();
        $this->exported_content_objects = array();
        parent :: __construct($content_object);
    }

    public function export_content_object()
    {
        $content_objects = $this->get_content_object();
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->doc->formatOutput = true;
        
        $this->root = $this->doc->createElement('content_objects');
        $this->doc->appendChild($this->root);
        
        $user = null;
        
        foreach ($content_objects as $lo)
        {
            if (! $user)
                $user = $lo->get_owner_id();
            
            $this->render_content_object($lo);
        }
        
        $temp_dir = Path :: get(SYS_TEMP_PATH) . $user . '/export_content_objects/';
        
        if (! is_dir($temp_dir))
        {
            mkdir($temp_dir, 0777, true);
        }
        
        $xml_path = $temp_dir . 'content_object.xml';
        $this->doc->save($xml_path);
        
        $this->add_files($temp_dir);
        
        $zip = Filecompression :: factory();
        //$zip->set_filename('content_objects_export');
        $zippath = $zip->create_archive($temp_dir);
        
        Filesystem :: remove($temp_dir);
        
        return $zippath;
    }

    function export_additional_properties($content_object)
    {
        if ($content_object->get_type() == 'document')
        {
            $this->files[$content_object->get_hash()] = $content_object->get_full_path();
        }
        
        if ($content_object->get_type() == 'hotpotatoes')
        {
            $this->hotpot_files[] = dirname($content_object->get_full_path());
        }
        
        if ($content_object->get_type() == 'learning_path' && $content_object->get_path())
        {
            $this->scorm_files[] = $content_object->get_full_path();
        }
        
        if ($content_object->get_type() == 'learning_path_item' || $content_object->get_type() == 'portfolio_item')
        {
            $id = $content_object->get_reference();
            $this->render_content_object($this->rdm->retrieve_content_object($id));
            $content_object->set_reference('object' . $id);
        }
    }

    function add_files($temp_dir)
    {
        foreach ($this->files as $hash => $path)
        {
            $newfile = $temp_dir . 'data/' . $hash;
            Filesystem :: copy_file($path, $newfile, true);
        }
        foreach ($this->hotpot_files as $hotpot_dir)
        {
            $newfile = $temp_dir . 'hotpotatoes/' . basename(rtrim($hotpot_dir, '/'));
            Filesystem :: recurse_copy($hotpot_dir, $newfile, true);
        }
        
        foreach ($this->scorm_files as $scorm_dir)
        {
            $newfile = $temp_dir . 'scorm/' . basename(rtrim($scorm_dir, '/'));
            Filesystem :: recurse_copy($scorm_dir, $newfile, true);
        }
    }

    function render_content_object($content_object)
    {
        if (in_array($content_object->get_id(), $this->exported_content_objects))
            return;
        
        $this->exported_content_objects[] = $content_object->get_id();
        
        $doc = $this->doc;
        $root = $this->root;
        
        $lo = $doc->createElement('content_object');
        $root->appendChild($lo);
        
        $id = $doc->createAttribute('id');
        $lo->appendChild($id);
        
        $id_value = $doc->createTextNode('object' . $content_object->get_id());
        $id->appendChild($id_value);
        
        $export_prop = array(ContentObject :: PROPERTY_TYPE, ContentObject :: PROPERTY_TITLE, ContentObject :: PROPERTY_DESCRIPTION, ContentObject :: PROPERTY_COMMENT, ContentObject :: PROPERTY_CREATION_DATE, ContentObject :: PROPERTY_MODIFICATION_DATE);
        
        $general = $doc->createElement('general');
        $lo->appendChild($general);
        
        foreach ($export_prop as $prop)
        {
            $property = $doc->createElement($prop);
            $general->appendChild($property);
            
            $text = $doc->createTextNode($content_object->get_default_property($prop));
            $text = $property->appendChild($text);
        }
        
        $this->export_additional_properties($content_object);
        
        $extended = $doc->createElement('extended');
        $lo->appendChild($extended);
        
        foreach ($content_object->get_additional_properties() as $prop => $value)
        {
            $property = $doc->createElement($prop);
            $extended->appendChild($property);
            $value = convert_uuencode($value);
            $text = $doc->createTextNode($value);
            $text = $property->appendChild($text);
        }
        
        $type = $doc->createAttribute('type');
        $lo->appendChild($type);
        
        //Complex children (subitems)
        if ($content_object->is_complex_content_object())
        {
            $text = $doc->createTextNode('complex');
            $type->appendChild($text);
            
            $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $content_object->get_id(), ComplexContentObjectItem :: get_table_name());
            $children = $this->rdm->retrieve_complex_content_object_items($condition);
            
            if ($children->size() > 0)
            {
                $sub_items = $doc->createElement('sub_items');
                $lo->appendChild($sub_items);
            }
            
            while ($child = $children->next_result())
            {
                $sub_item = $doc->createElement('sub_item');
                $sub_items->appendChild($sub_item);
                
                $id_ref = $doc->createAttribute('idref');
                $sub_item->appendChild($id_ref);
                
                $id_ref_value = $doc->createTextNode('object' . $child->get_ref());
                $id_ref->appendChild($id_ref_value);
                
                $id = $doc->createAttribute('id');
                $sub_item->appendChild($id);
                
                $id_value = $doc->createTextNode($child->get_id());
                $id->appendChild($id_value);
                
                foreach ($child->get_additional_properties() as $prop => $value)
                {
                    $property = $doc->createAttribute($prop);
                    $sub_item->appendChild($property);
                    
                    $text = $doc->createTextNode($value);
                    $text = $property->appendChild($text);
                }
                
                $this->render_content_object($this->rdm->retrieve_content_object($child->get_ref()));
            }
        }
        else
        {
            $text = $doc->createTextNode('simple');
            $type->appendChild($text);
        }
        
        //Attachments
        $attachments = $content_object->get_attached_content_objects();
        if (count($attachments) > 0)
        {
            $attachments_element = $doc->createElement('attachments');
            $lo->appendChild($attachments_element);
            
            foreach ($attachments as $attachment)
            {
                $attachment_element = $doc->createElement('attachment');
                $attachments_element->appendChild($attachment_element);
                
                $id_ref = $doc->createAttribute('idref');
                $attachment_element->appendChild($id_ref);
                
                $id_ref_value = $doc->createTextNode('object' . $attachment->get_id());
                $id_ref->appendChild($id_ref_value);
                
                $this->render_content_object($attachment);
            }
        }
        
        //Includes
        $includes = $content_object->get_included_content_objects();
        if (count($includes) > 0)
        {
            $includes_element = $doc->createElement('includes');
            $lo->appendChild($includes_element);
            
            foreach ($includes as $include)
            {
                $include_element = $doc->createElement('include');
                $includes_element->appendChild($include_element);
                
                $id_ref = $doc->createAttribute('idref');
                $include_element->appendChild($id_ref);
                
                $id_ref_value = $doc->createTextNode('object' . $include->get_id());
                $id_ref->appendChild($id_ref_value);
                
                $this->render_content_object($include);
            }
        }
    }
}
?>