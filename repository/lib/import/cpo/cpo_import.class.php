<?php
/**
 * $Id: cpo_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.cpo
 */

/**
 * Exports learning object to the chamilo learning object format (xml)
 */
class CpoImport extends ContentObjectImport
{
    /**
     * @var RepositoryDataManager
     */
    private $rdm;
    
    /**
     * The imported xml file
     * @var DomDOCUMENT
     */
    private $doc;
    
    /**
     * Array of files that are created (hash + path)
     * @var Array 
     */
    private $files;
    
    /*
	 * Array of hp files that are created
	 */
    private $hp_files;
    
    /*
	 * Array of scorm files that are created
	 */
    private $scorm_files;
    
    /**
     * The reference to store the file id's of each content_object and the new content_object
     * @var Array of INT
     */
    private $content_object_reference;
    
    /**
     * The array that has the reference for all the old ids of the wrappers => all the new ids of the wrappers
     *
     * @var Array of INT
     */
    private $wrapper_reference;
    
    /**
     * The array where the subitems are stored untill all the learning objects are created
     * With this array the wrappers will then be created
     * 
     * Example:
     * 
     * $lo_subitems['object0'] = array(0 => array(id => 'object1', properties => array()));
     */
    private $lo_subitems;
    
    /**
     * The array where the attachments are stored untill all the learning objects are created
     * With this array the attachment links will be created
     * 
     * Example:
     * 
     * $lo_attachments['object0'] = array(0 => 'object1', 1 => 'object2'));
     */
    private $lo_attachments;
    
    /**
     * The array where the includes are stored untill all the learning objects are created
     * With this array the include links will be created
     * 
     * Example:
     * 
     * $lo_includes['object0'] = array(0 => 'object1', 1 => 'object2'));
     */
    private $lo_includes;
    
    /**
     * Used to determine which references need to be changed (used in learning path item, portfolio item)
     *
     * @var $references[$content_object] = id;
     */
    private $references;
    
    /**
     * In this array we store the learning path item wrappers because we need to change the prerequisites after all the wrappers have been
     * created
     *
     * @var $learning_path_item_wrappers[] = $wrapper_object;
     */
    private $learning_path_item_wrappers;

    /**
     * Enter description here...
     *
     * @param unknown_type $content_object_file
     * @param unknown_type $user
     * @param unknown_type $category
     * @return CpoImport
     */
    
    function CpoImport($content_object_file, $user, $category)
    {
        $this->rdm = RepositoryDataManager :: get_instance();
        parent :: __construct($content_object_file, $user, $category);
    }

    public function import_content_object()
    {
        $zip = Filecompression :: factory();
        $temp = $zip->extract_file($this->get_content_object_file_property('tmp_name'));
        $dir = $temp . '/';
        
        $path = $dir . 'content_object.xml';
        $this->import_files($dir);
        
        $doc = $this->doc;
        $doc = new DOMDocument();
        
        $doc->load($path);
        $content_objects = $doc->getElementsByTagname('content_object');
        
        foreach ($content_objects as $lo)
        {
            $this->create_content_object($lo);
        }
        
        $this->create_complex_wrappers();
        $this->create_attachments();
        $this->create_includes();
        $this->update_references();
        $this->update_learning_path_prerequisites();
        
        if ($temp)
        {
            Filesystem :: remove($temp);
        }
        
        return true;
    }

    private function import_files($dir)
    {
        $user = $this->get_user();
        
        $lo_data_dir = $dir . 'data/';
        if (file_exists($lo_data_dir))
        {
            $files = Filesystem :: get_directory_content($lo_data_dir, Filesystem :: LIST_FILES_AND_DIRECTORIES, false);
            $repdir = Path :: get(SYS_REPO_PATH);
            
            foreach ($files as $f)
            {
                $usr_path = $user->get_id() . '/' . Text :: char_at($f, 0);
                $full_path = $repdir . $usr_path;
                
                $hash = Filesystem :: create_unique_name($full_path, $f);
                
                Filesystem :: copy_file($dir . 'data/' . $f, $full_path . '/' . $hash, false);
                $this->files[$f] = array('hash' => $hash, 'path' => $usr_path . '/' . $hash);
            }
        }
        
        $hp_dir = $dir . 'hotpotatoes/';
        if (file_exists($hp_dir))
        {
            $files = Filesystem :: get_directory_content($hp_dir, Filesystem :: LIST_FILES_AND_DIRECTORIES, false);
            $new_dir = Path :: get(SYS_HOTPOTATOES_PATH) . $user->get_id() . '/';
            
            foreach ($files as $f)
            {
                $dirname = Filesystem :: create_unique_name($new_dir, $f);
                Filesystem :: recurse_copy($dir . 'hotpotatoes/' . $f, $new_dir . $dirname, false);
                $this->hp_files[$f] = $dirname;
            }
        }
        
        $scorm_dir = $dir . 'scorm/';
        if (file_exists($scorm_dir))
        {
            $files = Filesystem :: get_directory_content($scorm_dir, Filesystem :: LIST_FILES_AND_DIRECTORIES, false);
            $new_dir = Path :: get(SYS_SCORM_PATH) . $user->get_id() . '/';
            
            foreach ($files as $f)
            {
                $dirname = Filesystem :: create_unique_name($new_dir, $f);
                Filesystem :: recurse_copy($dir . 'scorm/' . $f, $new_dir . $dirname, false);
                $this->scorm_files[$f] = $dirname;
            }
        }
    }

    public function import_extra_properties($type, $additionalProperties, $lo)
    {
        if ($type == 'document')
        {
            $hash = $additionalProperties['hash'];
            
            $additionalProperties['hash'] = $this->files[$hash]['hash'];
            $additionalProperties['path'] = $this->files[$hash]['path']; 
        }
        
        if ($type == 'hotpotatoes')
        {
            $path = $additionalProperties['path'];
            foreach ($this->hp_files as $folder => $new_folder)
            {
                if (strpos(dirname($path), $folder) !== false)
                {
                    $additionalProperties['path'] = str_replace($folder, $new_folder, $path);
                    break;
                }
            }
        }
        
        if ($type == 'learning_path')
        {
            $path = $additionalProperties['path'];
            foreach ($this->scorm_files as $folder => $new_folder)
            {
                if ($path == $folder)
                {
                    $additionalProperties['path'] = $new_folder;
                    break;
                }
            }
        }
        
        if ($type == 'scorm_item')
        {
            $path = $additionalProperties['path'];
            foreach ($this->scorm_files as $folder => $new_folder)
            {
                if (strpos($path, $folder) !== false)
                {
                    $additionalProperties['path'] = str_replace($folder, $new_folder, $path);
                    break;
                }
            }
        }
        
        return $additionalProperties;
    }

    public function create_content_object($content_object)
    {
        $id = $content_object->getAttribute('id');
        if (isset($this->content_object_reference[$id]))
            return;
        
        if ($content_object->hasChildNodes())
        {
            $general = $content_object->getElementsByTagName('general')->item(0);
            $type = $general->getElementsByTagName('type')->item(0)->nodeValue;
            $title = $general->getElementsByTagName('title')->item(0)->nodeValue;
            $description = $general->getElementsByTagName('description')->item(0)->nodeValue;
            $comment = $general->getElementsByTagName('comment')->item(0)->nodeValue;
            $created = $general->getElementsByTagName('created')->item(0)->nodeValue;
            $modified = $general->getElementsByTagName('modified')->item(0)->nodeValue;
            
            $lo = ContentObject :: factory($type);
            $lo->set_title($title);
            $lo->set_description($description);
            $lo->set_comment($comment);
            $lo->set_creation_date($created);
            $lo->set_modification_date($modified);
            $lo->set_owner_id($this->get_user()->get_id());
            $lo->set_parent_id($this->get_category());
            
            $extended = $content_object->getElementsByTagName('extended')->item(0);
            
            if ($extended->hasChildNodes())
            {
                $nodes = $extended->childNodes;
                $additionalProperties = array();
                
                foreach ($nodes as $node)
                {
                    if ($node->nodeName == "#text" || $node->nodeName == 'id')
                        continue;
                    $additionalProperties[$node->nodeName] = convert_uudecode($node->nodeValue);
                }
                
                $additionalProperties = $this->import_extra_properties($type, $additionalProperties, $lo);
                
                $lo->set_additional_properties($additionalProperties);
            }
        
            $lo->create_all();
            
            if ($type == 'learning_path_item' || $type == 'portfolio_item')
            {
                $this->references[$lo->get_id()] = $additionalProperties['reference_id'];
            }
            
            $this->content_object_reference[$id] = $lo->get_id();
            
            // Complex children
            $subitems = $content_object->getElementsByTagName('sub_items')->item(0);
            $children = $subitems->childNodes;
            for($i = 0; $i < $children->length; $i ++)
            {
                $subitem = $children->item($i);
                if ($subitem->nodeName == "#text")
                    continue;
                
                if ($subitem->hasAttributes())
                {
                    $properties = array();
                    
                    foreach ($subitem->attributes as $attrName => $attrNode)
                    {
                        if ($attrName == 'idref')
                        {
                            $idref = $attrNode->value;
                        }
                        elseif ($attrName == 'id')
                        {
                            $my_id = $attrNode->value;
                        }
                        else
                        {
                            $properties[$attrName] = $attrNode->value;
                        }
                    }
                }
                
                $this->lo_subitems[$id][] = array('id' => $my_id, 'idref' => $idref, 'properties' => $properties);
            }
           
            // Attachments
            $attachments = $content_object->getElementsByTagName('attachments')->item(0);
            $children = $attachments->childNodes;
            for($i = 0; $i < $children->length; $i ++)
            {
                $attachment = $children->item($i);
                if ($attachment->nodeName == "#text")
                    continue;
                
                $idref = $attachment->getAttribute('idref');
                $this->lo_attachments[$id][] = $idref;
            
            }
             
            // Includes
            $includes = $content_object->getElementsByTagName('includes')->item(0);
            $children = $includes->childNodes;
            
            //if($children->length > 0)
            $this->fix_links($lo);
            
            for($i = 0; $i < $children->length; $i ++)
            {
                $include = $children->item($i);
                if ($include->nodeName == "#text")
                    continue;
                
                $idref = $include->getAttribute('idref');
                $this->lo_includes[$id][] = $idref;
            
            } 
        }
    }

    /**
     * Method to fix the embedded links in html editor fields
     *
     * @param ContentObject $co
     */
    private function fix_links($co)
    {
        $fields = $co->get_html_editors();
        
        $pattern = '/http:\/\/.*\/files\/repository\/[1-9]*\/[^\"]*/';
        foreach ($fields as $field)
        {
            $value = $co->get_default_property($field);
            $value = preg_replace_callback($pattern, array($this, 'fix_link_matches'), $value);
            $co->set_default_property($field, $value);
        }
        
        $co->update();
    }

    private function fix_link_matches($matches)
    {
        $base_path = Path :: get(WEB_REPO_PATH);
        
        foreach ($this->files as $hash => $file)
        {
            if (strpos($matches[0], $hash) !== false)
            {
                return $base_path . $file['path'];
            }
        }
    }

    function create_complex_wrappers()
    {
        foreach ($this->lo_subitems as $parent_id => $children)
        {
            $real_parent_id = $this->content_object_reference[$parent_id];
            foreach ($children as $child)
            {
                $real_child_id = $this->content_object_reference[$child['idref']];
                
                $childlo = $this->rdm->retrieve_content_object($real_child_id);
                
                $cloi = ComplexContentObjectItem :: factory($childlo->get_type());
                
                $cloi->set_ref($childlo->get_id());
                $cloi->set_user_id($this->get_user()->get_id());
                $cloi->set_parent($real_parent_id);
                $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($real_parent_id));
                $cloi->set_additional_properties($child['properties']);
                $cloi->create();
                
                if ($childlo->get_type() == 'learning_path_item')
                {
                    $this->learning_path_item_wrappers[] = $cloi;
                }
                
                $this->wrapper_reference[$child['id']] = $cloi->get_id();
            }
        }
    }

    function create_attachments()
    {
        foreach ($this->lo_attachments as $lo_id => $children)
        {
            $real_lo_id = $this->content_object_reference[$lo_id];
            $lo = $this->rdm->retrieve_content_object($real_lo_id);
            
            foreach ($children as $child)
            {
                if ($this->content_object_reference[$child])
                    $lo->attach_content_object($this->content_object_reference[$child]);
            }
        }
    }

    function create_includes()
    {
        foreach ($this->lo_includes as $lo_id => $children)
        {
            $real_lo_id = $this->content_object_reference[$lo_id];
            $lo = $this->rdm->retrieve_content_object($real_lo_id);
            
            foreach ($children as $child)
            {
                if ($this->content_object_reference[$child])
                    $lo->include_content_object($this->content_object_reference[$child]);
            }
        }
    }

    function update_references()
    {
        foreach ($this->references as $lo_id => $reference)
        {
            $real_reference = $this->content_object_reference[$reference];
            $lo = $this->rdm->retrieve_content_object($lo_id);
            $lo->set_reference($real_reference);
            $lo->update();
        }
    }

    function update_learning_path_prerequisites()
    {
        foreach ($this->learning_path_item_wrappers as $lp_wrapper)
        {
            $ref = $this->rdm->retrieve_content_object($lp_wrapper->get_ref());
            $reference = $this->rdm->retrieve_content_object($ref->get_reference());
            if ($reference->get_type() != 'scorm_item')
            {
                if ($prereq = $lp_wrapper->get_prerequisites())
                {
                    $pattern = '/[^()&|~]+/';
                    $prereq = preg_replace_callback($pattern, array($this, 'test_matches'), $prereq);
                    $lp_wrapper->set_prerequisites($prereq);
                    $lp_wrapper->update();
                }
            }
        
        }
    }

    function test_matches($matches)
    {
        return $this->wrapper_reference[$matches[0]];
    }
}
?>