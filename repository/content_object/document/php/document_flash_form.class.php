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
class DocumentFlashForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $post_max_size = ini_get('upload_max_filesize');
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('file', 'upload', sprintf(Translation :: get('FileName'), $post_max_size));
        $this->addFormRule(array($this, 'check_document_form'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $post_max_size = ini_get('upload_max_filesize');

        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $object = $this->get_content_object();
        $this->addElement('file', 'file', sprintf(Translation :: get('FileName'), $post_max_size));
        $this->addRule('file', Translation :: get('DiskQuotaExceeded'), 'disk_quota');
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $values = $this->exportValues();

        $object = new Document();

        if(StringUtilities :: has_value(($_FILES['upload']['name'])))
        {
            /*
            * Object content is replaced by uploaded file
            */
            $object->set_filename($_FILES['upload']['name']);
            $object->set_temporary_file_path($_FILES['upload']['tmp_name']);
        }

        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
         $document = $this->get_content_object();
         $values = $this->exportValues();

         if(StringUtilities :: has_value($_FILES['upload']['name']))
         {
             /*
              * Object content is replaced by uploaded file
              */
             $document->set_filename($_FILES['upload']['name']);
             $document->set_temporary_file_path($_FILES['upload']['tmp_name']);
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

        if (isset($_FILES['upload']) && isset($_FILES['upload']['error']) && $_FILES['upload']['error'] != 0)
        {
            switch ($_FILES['upload']['error'])
            {
                case 1 : //uploaded file exceeds the upload_max_filesize directive in php.ini
                    $errors['upload'] = Translation :: get('FileTooBig');
                    break;
                case 2 : //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
                    $errors['upload'] = Translation :: get('FileTooBig');
                    break;
                case 3 : //uploaded file was only partially uploaded
                    $errors['upload'] = Translation :: get('UploadIncomplete');
                    break;
                case 4 : //no file was uploaded
                    $errors['upload'] = Translation :: get('NoFileSelected');
                    break;
            }
        }
        elseif (isset($_FILES['upload']) && strlen($_FILES['upload']['name']) > 0)
        {
            $size = $_FILES['upload']['size'];
            $available_disk_space = $quotamanager->get_available_disk_space();

            if ($size > $available_disk_space)
            {
                $errors['upload'] = Translation :: get('DiskQuotaExceeded');
            }

            $array = explode('.', $_FILES['upload']['name']);
            $type = $array[count($array) - 1];

            if (! $this->allow_file_type($type))
            {
                $errors['upload'] = Translation :: get('FileTypeNotAllowed');
            }
        }
        else
        {
            $errors['upload'] = Translation :: get('NoFileSelected');
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
        $flash_types = Document :: get_flash_types();

        $filtering_type = PlatformSetting :: get('type_of_filtering');
        if ($filtering_type == 'blacklist')
        {
            $blacklist = PlatformSetting :: get('blacklist');
            $items = explode(',', $blacklist);
            if (in_array($type, $items) || !in_array($type, $flash_types))
            {
                return false;
            }

            return true;
        }
        else
        {
            $whitelist = PlatformSetting :: get('whitelist');
            $items = explode(',', $whitelist);
            if (in_array($type, $items) && in_array($type, $flash_types))
            {
                return true;
            }

            return false;
        }
    }
}
?>