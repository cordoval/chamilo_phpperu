<?php
/**
 * $Id: document_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.document
 */
require_once dirname(__FILE__) . '/../../category_manager/repository_category.class.php';
require_once dirname(__FILE__) . '/document.class.php';
require_once Path :: get_library_path() . 'html/formvalidator/Rule/DiskQuota.php';
/**
 * A form to create/update a document.
 *
 * A destinction is made between HTML documents and other documents. For HTML
 * documents an online HTML editor is used to edit the contents of the document.
 */
class DocumentForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $post_max_size = ini_get('upload_max_filesize');
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        //$this->addElement('html', '<span style="margin-left: -38px">' . Translation :: get('MaxSize') . ': ' . $post_max_size . '</span>');
        $this->addElement('upload_or_create', 'upload_or_create', sprintf(Translation :: get('FileName'), $post_max_size));
        //$this->addElement('checkbox','uncompress',Translation :: get('Uncompress'), '', array('id' => 'uncompress'));
        $this->addFormRule(array($this, 'check_document_form'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $post_max_size = ini_get('upload_max_filesize');

        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        //$this->addElement('html', '<span style="margin-left: -40px">' . Translation :: get('MaxSize') . ': ' . $post_max_size . '</span>');
        $object = $this->get_content_object();
        if (Utilities :: is_html_document($object->get_filename()))
        {
            $this->add_html_editor('html_content', Translation :: get('EditDocument'), false);
            $this->addRule('html_content', Translation :: get('DiskQuotaExceeded'), 'disk_quota');
        }
        else
        {
            $this->addElement('file', 'file', sprintf(Translation :: get('FileName'), $post_max_size));
            $this->addRule('file', Translation :: get('DiskQuotaExceeded'), 'disk_quota');
        }
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if (isset($object) && Utilities :: is_html_document($object->get_filename()))
        {
            $defaults['html_content'] = file_get_contents($this->get_upload_path() . $object->get_path());
        }
        $defaults['choice'] = 0;
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $values = $this->exportValues();

        $object = new Document();

        if (StringUtilities :: has_value(($values['html_content'])))
        {
            /*
            * Object content is replaced by HTML content
            */
            $object->set_filename($values[Document :: PROPERTY_TITLE] . '.html');
            $object->set_in_memory_file($values['html_content']);
        }
        elseif(StringUtilities :: has_value(($_FILES['file']['name'])))
        {
            /*
            * Object content is replaced by uploaded file
            */
            $object->set_filename($_FILES['file']['name']);
            $object->set_temporary_file_path($_FILES['file']['tmp_name']);
        }

        $this->set_content_object($object);
        $document = parent :: create_content_object();

//        $owner = $this->get_owner_id();
//        $values = $this->exportValues();
//        $owner_path = $this->get_upload_path() . $owner;
//        Filesystem :: create_dir($owner_path);
//        if ($values['choice'])
//        {
//            $filename = $values[Document :: PROPERTY_TITLE] . '.html';
//            $hash = md5($filename);
//
//            $path = $owner . '/' . Text :: char_at($hash, 0);
//            $full_path = $this->get_upload_path() . $path;
//
//            Filesystem :: create_dir($full_path);
//            $hash = Filesystem :: create_unique_name($full_path, $hash);
//
//            $path = $path . '/' . $hash;
//            $full_path = $full_path . '/' . $hash;
//
//            Filesystem :: write_to_file($full_path, $values['html_content']);
//        }
//        else
//        {
//            $filename = $_FILES['file']['name'];
//            $hash = md5($_FILES['file']['name']);
//
//            $path = $owner . '/' . Text :: char_at($hash, 0);
//            $full_path = $this->get_upload_path() . $path;
//
//            Filesystem :: create_dir($full_path);
//            $hash = Filesystem :: create_unique_name($full_path, $hash);
//
//            $path = $path . '/' . $hash;
//            $full_path = $full_path . '/' . $hash;
//
//            move_uploaded_file($_FILES['file']['tmp_name'], $full_path) or die('Failed to create "' . $full_path . '"');
//        }
//
//        $permissions_new_files = PlatformSetting :: get('permissions_new_files');
//        Filesystem :: chmod($full_path, $permissions_new_files);
//
//        $object = new Document();
//        $object->set_path($path);
//        $object->set_filename($filename);
//        $object->set_hash($hash);
//        $object->set_filesize(Filesystem :: get_disk_space($full_path));
//        $this->set_content_object($object);
//        $document = parent :: create_content_object();

        if ($values['uncompress'] && ! $values['choice'])
        {
            $documents = array();
            $filecompression = Filecompression :: factory();
            $dir = $filecompression->extract_file($document->get_full_path());
            $entries = Filesystem :: get_directory_content($dir);
            $wdm = RepositoryDataManager :: get_instance();
            $created_directories = array();
            foreach ($entries as $entry)
            {
                $url = str_replace(realpath($dir), '', realpath($entry));
                if (is_dir($entry))
                {
                    //Check for existing category
                    $condition = new EqualityCondition(RepositoryCategory :: PROPERTY_NAME, basename($url));
                    $categories = $wdm->retrieve_categories($condition);
                    $category = $categories->next_result();
                    if ($category == null)
                    {
                        $category = new RepositoryCategory();
                        $category->set_name(basename($url));
                        if (isset($created_directories[dirname($url)]))
                        {
                            $category->set_parent($created_directories[dirname($url)]);
                        }
                        $category->set_user_id($owner);
                        $category->create();
                    }
                    $created_directories[$url] = $category->get_id();
                }
                elseif (is_file($entry))
                {
                    //Create a document in the repository
                    $hash = md5(basename($entry));
                    $path = $owner . '/' . Text :: char_at($hash, 0);
                    $full_path = $this->get_upload_path() . $path;
                    Filesystem :: create_dir($full_path);

                    $hash = Filesystem :: create_unique_name($owner_path, $hash);
                    $new_path = $path . '/' . $hash;
                    $full_path = $full_path . '/' . $hash;

                    Filesystem :: copy_file($entry, $full_path);
                    Filesystem :: chmod($full_path, $permissions_new_files);

                    $object = new Document();
                    $object->set_path($new_path);
                    $object->set_filename(basename($entry));
                    $object->set_filesize(Filesystem :: get_disk_space($full_path));
                    $object->set_hash($hash);

                    $this->set_content_object($object);
                    $object = parent :: create_content_object();
                    $object->set_title(basename($url));
                    if (isset($created_directories[dirname($url)]))
                    {
                        $object->set_parent_id($created_directories[dirname($url)]);
                    }
                    $object->update();
                    $documents[] = $object;
                }
            }
            Filesystem :: remove($dir);
            return $documents;
        }
        else
        {
            return $document;
        }
    }

    function update_content_object()
    {
         $document = $this->get_content_object();
         $values = $this->exportValues();

         if (StringUtilities :: has_value(($values['html_content'])))
         {
             /*
              * Object content is replaced by HTML content
              */
             $document->set_filename($document->get_title() . '.html');
             $document->set_in_memory_file($values['html_content']);
         }
         elseif(StringUtilities :: has_value($_FILES['file']['name']))
         {
             /*
              * Object content is replaced by uploaded file
              */
             $document->set_filename($_FILES['file']['name']);
             $document->set_temporary_file_path($_FILES['file']['tmp_name']);
         }

         if ((isset($values['version']) && $values['version'] == 0) || ! isset($values['version']))
         {
             $document->set_save_as_new_version(false);
         }
         else
         {
             $document->set_save_as_new_version(true);
         }

         return parent :: update_content_object();
    }

//    function update_content_object()
//    {
//        $object = $this->get_content_object();
//        $values = $this->exportValues();
//
//        $owner = $object->get_owner_id();
//        $owner_path = $this->get_upload_path() . $owner;
//        Filesystem :: create_dir($owner_path);
//        if (isset($values['html_content']))
//        {
//            if ((isset($values['version']) && $values['version'] == 0) || ! isset($values['version']))
//            {
//                Filesystem :: remove($this->get_upload_path() . $object->get_path());
//            }
//
//            $filename = $object->get_title() . '.html';
//            $hash = md5($filename);
//            $path = $owner . '/' . Text :: char_at($hash, 0);
//            $full_path = $this->get_upload_path() . $path;
//            Filesystem :: create_dir($full_path);
//
//            $hash = Filesystem :: create_unique_name($full_path, $hash);
//            $path .= '/' . $hash;
//            $full_path .= '/' . $hash;
//
//            Filesystem :: write_to_file($full_path, $values['html_content']);
//        }
//        elseif (strlen($_FILES['file']['name']) > 0)
//        {
//            if ((isset($values['version']) && $values['version'] == 0) || ! isset($values['version']))
//            {
//                Filesystem :: remove($this->get_upload_path() . $object->get_path());
//            }
//
//            $filename = $_FILES['file']['name'];
//            $hash = md5($filename);
//            $path = $owner . '/' . Text :: char_at($hash, 0);
//            $full_path = $this->get_upload_path() . $path;
//            Filesystem :: create_dir($full_path);
//
//            $hash = Filesystem :: create_unique_name($full_path, $hash);
//            $path .= '/' . $hash;
//            $full_path .= '/' . $hash;
//
//            move_uploaded_file($_FILES['file']['tmp_name'], $full_path) or die('Failed to create "' . $full_path . '"');
//            Filesystem :: chmod($full_path, PlatformSetting :: get('permissions_new_files'));
//        }
//
//        if(isset($path))
//        {
//            $object->set_path($path);
//        }
//        if(isset($filename))
//        {
//            $object->set_filename($filename);
//        }
//        if(isset($full_path))
//        {
//            $object->set_filesize(Filesystem :: get_disk_space($full_path));
//        }
//        if(isset($hash))
//        {
//            $object->set_hash($hash);
//        }
//
//        return parent :: update_content_object();
//    }

    /**
     *
     */
    protected function check_document_form($fields)
    {
        // TODO: Do the errors need htmlentities()?
        $errors = array();

        $owner_id = $this->get_owner_id();
        $udm = UserDataManager :: get_instance();

        $owner = $udm->retrieve_user($owner_id);

        $quotamanager = new QuotaManager($owner);

        if (! $fields['choice'])
        {
            if (isset($_FILES['file']) && isset($_FILES['file']['error']) && $_FILES['file']['error'] != 0)
            {
                switch ($_FILES['file']['error'])
                {
                    case 1 : //uploaded file exceeds the upload_max_filesize directive in php.ini
                        $errors['upload_or_create'] = Translation :: get('FileTooBig');
                        break;
                    case 2 : //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
                        $errors['upload_or_create'] = Translation :: get('FileTooBig');
                        break;
                    case 3 : //uploaded file was only partially uploaded
                        $errors['upload_or_create'] = Translation :: get('UploadIncomplete');
                        break;
                    case 4 : //no file was uploaded
                        $errors['upload_or_create'] = Translation :: get('NoFileSelected');
                        break;
                }
            }
            elseif (isset($_FILES['file']) && strlen($_FILES['file']['name']) > 0)
            {
                $size = $_FILES['file']['size'];
                $available_disk_space = $quotamanager->get_available_disk_space();

                if ($size > $available_disk_space)
                {
                    $errors['upload_or_create'] = Translation :: get('DiskQuotaExceeded');
                }

                /*$filecompression = Filecompression::factory(); dump($_FILES); exit();
				if( $fields['uncompress'] && !$filecompression->is_supported_mimetype($_FILES['file']['type']))
				{
					$errors['upload_or_create'] = Translation :: get('UncompressNotAvailableForThisFile');
				}*/

                $array = explode('.', $_FILES['file']['name']);
                $type = $array[count($array) - 1];

                if (isset($fields['uncompress']) && $type != 'zip')
                {
                    $errors['upload_or_create'] = Translation :: get('UncompressNotAvailableForThisFile');
                }

                if (! $fields['uncompress'] && ! $this->allow_file_type($type))
                {
                    if (PlatformSetting :: get('rename_instead_of_disallow') == 1)
                    {
                        $name = $_FILES['file']['name'];
                        $_FILES['file']['name'] = $name . '.' . PlatformSetting :: get('replacement_extension');
                    }
                    else
                    {
                        $errors['upload_or_create'] = Translation :: get('FileTypeNotAllowed');
                    }
                }
            }
            else
            {
                $errors['upload_or_create'] = Translation :: get('NoFileSelected');
            }
        }
        else
        {
            // Create an HTML-document
            $file['size'] = Filesystem :: guess_disk_space($fields['html_content']);
            $available_disk_space = $quotamanager->get_available_disk_space();
            if ($file['size'] > $available_disk_space)
            {
                $errors['upload_or_create'] = Translation :: get('DiskQuotaExceeded');
            }
            else
            {
                if (! HTML_QuickForm_Rule_Required :: validate($fields['html_content']))
                {
                    $errors['upload_or_create'] = Translation :: get('NoFileCreated');
                }
            }
        }
        if (count($errors) == 0)
        {
            return true;
        }
        return $errors;
    }

    private static function get_upload_path()
    {
        return Path :: get(SYS_REPO_PATH);
    }

    private function allow_file_type($type)
    {
        $filtering_type = PlatformSetting :: get('type_of_filtering');
        if ($filtering_type == 'blacklist')
        {
            $blacklist = PlatformSetting :: get('blacklist');
            $items = explode(',', $blacklist);
            if (in_array($type, $items))
            {
                return false;
            }

            return true;
        }
        else
        {
            $whitelist = PlatformSetting :: get('whitelist');
            $items = explode(',', $whitelist);
            if (in_array($type, $items))
            {
                return true;
            }

            return false;
        }
    }
}
?>