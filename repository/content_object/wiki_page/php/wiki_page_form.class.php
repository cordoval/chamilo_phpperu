<?php
namespace repository\content_object\wiki_page;

use common\libraries\Request;

/**
 * $Id: wiki_page_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.wiki_page
 */
require_once dirname(__FILE__) . '/wiki_page.class.php';

class WikiPageForm extends ContentObjectForm
{

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new WikiPage();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function setDefaults($defaults = array ())
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = Request :: get(ContentObject :: PROPERTY_TITLE) == null ? NULL : Request :: get(ContentObject :: PROPERTY_TITLE);

        parent :: setDefaults($defaults);
    }

    function build_creation_form()
    {
    	parent :: build_creation_form(array('toolbar' => 'WikiPage'));
    }

	function build_editing_form()
    {
    	parent :: build_editing_form(array('toolbar' => 'WikiPage'));
    }
}
?>