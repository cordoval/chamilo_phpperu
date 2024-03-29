<?php
namespace repository\content_object\dailymotion;

use common\libraries\Translation;
use repository\ContentObjectForm;
use common\libraries\Utilities;

/**
 * $Id: dailymotion_form.class.php 2010-06-08
 * package repository.lib.content_object.dailymotion
 * @author Shoira Mukhsinova
 */
require_once dirname(__FILE__) . '/dailymotion.class.php';

class DailymotionForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));
        $this->add_textfield(Dailymotion :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->add_textfield(Dailymotion :: PROPERTY_WIDTH, Translation :: get('Width', null, Utilities :: COMMON_LIBRARIES), true, array('size' => '5'));
        $this->add_textfield(Dailymotion :: PROPERTY_HEIGHT, Translation :: get('Height', null, Utilities :: COMMON_LIBRARIES), true, array('size' => '5'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Properties'));
        $this->add_textfield(Dailymotion :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->add_textfield(Dailymotion :: PROPERTY_WIDTH, Translation :: get('Width', null, Utilities :: COMMON_LIBRARIES), true, array('size' => '5'));
        $this->add_textfield(Dailymotion :: PROPERTY_HEIGHT, Translation :: get('Height', null, Utilities :: COMMON_LIBRARIES), true, array('size' => '5'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[Dailymotion :: PROPERTY_URL] = $lo->get_url();
            $defaults[Dailymotion :: PROPERTY_HEIGHT] = $lo->get_height();
            $defaults[Dailymotion :: PROPERTY_WIDTH] = $lo->get_width();
        }
        else
        {
            $defaults[Dailymotion :: PROPERTY_URL] = 'http://www.dailymotion.com/video/';
            $defaults[Dailymotion :: PROPERTY_HEIGHT] = '344';
            $defaults[Dailymotion :: PROPERTY_WIDTH] = '425';
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new Dailymotion();
        $object->set_url($this->exportValue(Dailymotion :: PROPERTY_URL));
        $object->set_height($this->exportValue(Dailymotion :: PROPERTY_HEIGHT));
        $object->set_width($this->exportValue(Dailymotion :: PROPERTY_WIDTH));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_url($this->exportValue(Dailymotion :: PROPERTY_URL));
        $object->set_height($this->exportValue(Dailymotion :: PROPERTY_HEIGHT));
        $object->set_width($this->exportValue(Dailymotion :: PROPERTY_WIDTH));
        return parent :: update_content_object();
    }
}
?>
