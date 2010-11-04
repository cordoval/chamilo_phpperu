<?php
namespace repository\content_object\handbook_topic;

use repository\ContentObjectForm;
use common\libraries\Translation;

require_once dirname(__FILE__) . '/handbook_topic.class.php';
/**
 * This class represents a form to create or update handbook_topics
 */
class HandbookTopicForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new HandbookTopic();
        $object->set_text($this->exportValue(HandbookTopic:: PROPERTY_TEXT));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[HandbookTopic :: PROPERTY_TEXT] = $valuearray[3];
        parent :: set_values($defaults);
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_text($this->exportValue(HandbookTopic :: PROPERTY_TEXT));
        return parent :: update_content_object();
    }

    function build_creation_form()
    {
    	parent :: build_creation_form(array('height' => '50', 'collapse_toolbar' => true));
        $htmleditor_options = array('toolbar' => 'HandbookItem');
        $this->add_html_editor(HandbookTopic :: PROPERTY_TEXT, Translation :: get('HandbookTopicText'), $required, $htmleditor_options);
     }

	function build_editing_form()
    {
        $htmleditor_options = array('toolbar' => 'HandbookItem');
    	parent :: build_editing_form(array('height' => '50', 'collapse_toolbar' => true));
        $this->add_html_editor(HandbookTopic :: PROPERTY_TEXT, Translation :: get('HandbookTopicText'), $required, $htmleditor_options);
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        $defaults[HandbookTopic :: PROPERTY_TEXT] = $object->get_text();
        parent :: setDefaults($defaults);
    }

}
?>