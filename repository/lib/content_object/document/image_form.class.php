<?php
/**
 * $Id: document_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.document
 */
require_once dirname(__FILE__) . '/../../category_manager/repository_category.class.php';
require_once dirname(__FILE__) . '/document.class.php';
require_once dirname(__FILE__) . '/document_form.class.php';
/**
 * A form to create/update a document.
 *
 * A destinction is made between HTML documents and other documents. For HTML
 * documents an online HTML editor is used to edit the contents of the document.
 */
class ImageForm extends DocumentForm
{

//    protected function build_creation_form()
//    {
//        parent :: build_creation_form();
//    }
//
//    protected function build_editing_form()
//    {
//        parent :: build_editing_form();
//    }

    private function allow_file_type($type)
    {
        return (in_array($type, Document :: get_image_types()));
    }
}
?>