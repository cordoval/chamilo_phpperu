<?php
namespace repository\content_object\forum;

use common\libraries\Translation;
use common\libraries\Utilities;

use repository\ContentObjectForm;

/**
 * $Id: forum_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum
 */
require_once dirname(__FILE__) . '/forum.class.php';

class ForumForm extends ContentObjectForm
{

    function create_content_object()
    {
        $object = new Forum();
        $object->set_locked($this->exportValue(Forum :: PROPERTY_LOCKED));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_locked($this->exportValue(Forum :: PROPERTY_LOCKED));
        //$this->set_content_object($object);
        return parent :: update_content_object();
    }

    function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties', null , Utilities :: COMMON_LIBRARIES));
        $this->addElement('checkbox', 'locked', Translation :: get('ForumLocked'));
        $this->addElement('category');
    }

    function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Properties', null , Utilities :: COMMON_LIBRARIES));
        $this->addElement('checkbox', 'locked', Translation :: get('ForumLocked'));
        $this->addElement('category');
    }

	function setDefaults($defaults = array())
	{
		$object = $this->get_content_object();
		if($object != null)
		{
			$defaults[Forum :: PROPERTY_LOCKED] = $object->get_locked();
		}
		parent :: setDefaults($defaults);
	}

}
?>