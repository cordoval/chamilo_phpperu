<?php
/**
 * $Id: hotpotatoes_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.hotpotatoes
 */
require_once dirname(__FILE__) . '/hotpotatoes.class.php';
/**
 * This class represents a form to create or update open questions
 */
class HotpotatoesForm extends ContentObjectForm
{

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Hotpotatoes :: PROPERTY_MAXIMUM_ATTEMPTS] = $valuearray[3];
        
        parent :: set_values($defaults);
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            $defaults[Hotpotatoes :: PROPERTY_MAXIMUM_ATTEMPTS] = $object->get_maximum_attempts();
        }
        else
        {
            $defaults[Hotpotatoes :: PROPERTY_MAXIMUM_ATTEMPTS] = 0;
        }
        
        parent :: setDefaults($defaults);
    }

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_textfield(Assessment :: PROPERTY_MAXIMUM_ATTEMPTS, Translation :: get('MaximumAttempts'));
        $this->addElement('html', Translation :: get('NoMaximumAttemptsFillIn0'));
        $this->addElement('file', 'file', Translation :: get('UploadHotpotatoes'));
        $this->addRule('file', Translation :: get('ThisFileIsRequired'), 'required');
        $this->addElement('category');
    }

    // Inherited
    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_textfield(Hotpotatoes :: PROPERTY_MAXIMUM_ATTEMPTS, Translation :: get('MaximumAttempts'));
        $this->addElement('html', Translation :: get('NoMaximumAttemptsFillIn0'));
        $this->addElement('file', 'file', Translation :: get('ChangeHotpotatoes'));
        $this->addRule('file', Translation :: get('ThisFileIsRequired'), 'required');
        $this->addElement('category');
    }

    // Inherited
    function create_content_object()
    {
        $object = new Hotpotatoes();
        $values = $this->exportValues();
        
        if (! $this->upload_file($object))
            return false;
        
        $att = $values[Hotpotatoes :: PROPERTY_MAXIMUM_ATTEMPTS];
        $object->set_maximum_attempts($att ? $att : 0);
        
        $this->set_content_object($object);
        //$object->add_javascript();
        $succes = parent :: create_content_object();
        
        return $succes;
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        
        if (isset($_FILES['file']) && $_FILES['file']['name'] != '')
        {
            $object->delete_file();
            if (! $this->upload_file($object))
                return false;
        }
        
        $att = $values[Hotpotatoes :: PROPERTY_MAXIMUM_ATTEMPTS];
        $object->set_maximum_attempts($att ? $att : 0);
        
        $this->set_content_object($object);
        
        $succes = parent :: update_content_object();
        
        return $succes;
    }

    function upload_file($object)
    {
        if ($_FILES['file']['error'] == '4')
        {
            return false;
        }
        
        $path = $this->upload();
        
        //dump($_FILES['file']);
        $filename = $_FILES['file']['name'];
        if (substr($filename, - 4) == '.zip')
        {
            $path = $this->manage_zip_file($object, $path);
            $object->set_path($path);
        }
        else
        {
            $object->set_path($path);
        }
        
        return true;
    }

    function upload()
    {
        $owner = $this->get_owner_id();
        $filename = Filesystem :: create_unique_name(Path :: get(SYS_HOTPOTATOES_PATH) . $owner, $_FILES['file']['name']);
        $filename_split = explode('.', $filename);
        unset($filename_split[count($filename_split) - 1]);
        $file = implode('.', $filename_split);
        
        $hotpot_path = Path :: get(SYS_HOTPOTATOES_PATH) . $owner . '/';
        $real_path = $hotpot_path . Filesystem :: create_unique_name($hotpot_path, $file) . '/';
        if (! is_dir($real_path))
            Filesystem :: create_dir($real_path);
        
        $full_path = $real_path . $filename;
        
        move_uploaded_file($_FILES['file']['tmp_name'], $full_path) or die('Failed to create "' . $full_path . '"');
        chmod($full_path, 0777);
        
        return substr($full_path, strlen($hotpot_path));
    }

    function manage_zip_file($object, $path)
    {
        $zip_file_name = basename($path);
        $hotpot_path = Path :: get(SYS_HOTPOTATOES_PATH) . $this->get_owner_id() . '/';
        $full_path = $hotpot_path . dirname($path) . '/';
        
        $filecompression = Filecompression :: factory();
        $dir = $filecompression->extract_file($full_path . $zip_file_name);
        $entries = Filesystem :: get_directory_content($dir);
        
        foreach ($entries as $entry)
        {
        	//$filename = basename($entry); dump($entry);
        	$filename = substr($entry, strlen($dir)); dump($filename);
            $full_new_path = $full_path . $filename;
            $new_path = substr($full_new_path, strlen($hotpot_path));
            
            Filesystem :: move_file($entry, $full_new_path, false);
            if (substr($filename, - 4) == '.htm' || substr($filename, - 5) == '.html')
            {
                $return_path = $new_path;
            }
        }
        Filesystem :: remove($dir);
        Filesystem :: remove($full_path . $zip_file_name);
        
        return $return_path;
    }
}
?>
