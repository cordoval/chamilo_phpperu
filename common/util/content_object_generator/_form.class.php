<?php
/**
 * This class describes the form for a  object.
 * @package repository.lib.content_object.link
 * @author Hans De Bisschop
 **/

require_once dirname(__FILE__) . '/.class.php';

class Form extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->build_default_form();
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->build_default_form();
        $this->addElement('category');
    }

    private function build_default_form()
    {
		$this->addElement('text',  :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule( :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_ORIGINAL_TITLE, Translation :: get('OriginalTitle'));
		$this->addRule( :: PROPERTY_ORIGINAL_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_ISSUE, Translation :: get('Issue'));
		$this->addRule( :: PROPERTY_ISSUE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_SERIES, Translation :: get('Series'));
		$this->addRule( :: PROPERTY_SERIES, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_ORIGINAL_SERIES, Translation :: get('OriginalSeries'));
		$this->addRule( :: PROPERTY_ORIGINAL_SERIES, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_SUBSERIES, Translation :: get('Subseries'));
		$this->addRule( :: PROPERTY_SUBSERIES, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_ARTIST, Translation :: get('Artist'));
		$this->addRule( :: PROPERTY_ARTIST, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_WRITER, Translation :: get('Writer'));
		$this->addRule( :: PROPERTY_WRITER, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_COLORIST, Translation :: get('Colorist'));
		$this->addRule( :: PROPERTY_COLORIST, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_INKER, Translation :: get('Inker'));
		$this->addRule( :: PROPERTY_INKER, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_EDITOR, Translation :: get('Editor'));
		$this->addRule( :: PROPERTY_EDITOR, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_COLLECTION, Translation :: get('Collection'));
		$this->addRule( :: PROPERTY_COLLECTION, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_COLLECTION_ISSUE, Translation :: get('CollectionIssue'));
		$this->addRule( :: PROPERTY_COLLECTION_ISSUE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_TYPE, Translation :: get('Type'));
		$this->addRule( :: PROPERTY_TYPE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_BINDING, Translation :: get('Binding'));
		$this->addRule( :: PROPERTY_BINDING, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_PAGES, Translation :: get('Pages'));
		$this->addRule( :: PROPERTY_PAGES, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_YEAR, Translation :: get('Year'));
		$this->addRule( :: PROPERTY_YEAR, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_GENRE, Translation :: get('Genre'));
		$this->addRule( :: PROPERTY_GENRE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_LIMITED, Translation :: get('Limited'));
		$this->addRule( :: PROPERTY_LIMITED, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_SIGNED, Translation :: get('Signed'));
		$this->addRule( :: PROPERTY_SIGNED, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_LANGUAGE, Translation :: get('Language'));
		$this->addRule( :: PROPERTY_LANGUAGE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_COLOUR, Translation :: get('Colour'));
		$this->addRule( :: PROPERTY_COLOUR, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_WEIGHT, Translation :: get('Weight'));
		$this->addRule( :: PROPERTY_WEIGHT, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_PRICE, Translation :: get('Price'));
		$this->addRule( :: PROPERTY_PRICE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_CURRENCY, Translation :: get('Currency'));
		$this->addRule( :: PROPERTY_CURRENCY, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_SYNOPSIS, Translation :: get('Synopsis'));
		$this->addRule( :: PROPERTY_SYNOPSIS, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text',  :: PROPERTY_REVIEW, Translation :: get('Review'));
		$this->addRule( :: PROPERTY_REVIEW, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function setDefaults($defaults = array ())
    {
        $content_object = $this->get_content_object();
        if (isset($content_object))
        {
        	$defaults[ :: PROPERTY_ID] = $content_object->get_id();
        	$defaults[ :: PROPERTY_ORIGINAL_TITLE] = $content_object->get_original_title();
        	$defaults[ :: PROPERTY_ISSUE] = $content_object->get_issue();
        	$defaults[ :: PROPERTY_SERIES] = $content_object->get_series();
        	$defaults[ :: PROPERTY_ORIGINAL_SERIES] = $content_object->get_original_series();
        	$defaults[ :: PROPERTY_SUBSERIES] = $content_object->get_subseries();
        	$defaults[ :: PROPERTY_ARTIST] = $content_object->get_artist();
        	$defaults[ :: PROPERTY_WRITER] = $content_object->get_writer();
        	$defaults[ :: PROPERTY_COLORIST] = $content_object->get_colorist();
        	$defaults[ :: PROPERTY_INKER] = $content_object->get_inker();
        	$defaults[ :: PROPERTY_EDITOR] = $content_object->get_editor();
        	$defaults[ :: PROPERTY_COLLECTION] = $content_object->get_collection();
        	$defaults[ :: PROPERTY_COLLECTION_ISSUE] = $content_object->get_collection_issue();
        	$defaults[ :: PROPERTY_TYPE] = $content_object->get_type();
        	$defaults[ :: PROPERTY_BINDING] = $content_object->get_binding();
        	$defaults[ :: PROPERTY_PAGES] = $content_object->get_pages();
        	$defaults[ :: PROPERTY_YEAR] = $content_object->get_year();
        	$defaults[ :: PROPERTY_GENRE] = $content_object->get_genre();
        	$defaults[ :: PROPERTY_LIMITED] = $content_object->get_limited();
        	$defaults[ :: PROPERTY_SIGNED] = $content_object->get_signed();
        	$defaults[ :: PROPERTY_LANGUAGE] = $content_object->get_language();
        	$defaults[ :: PROPERTY_COLOUR] = $content_object->get_colour();
        	$defaults[ :: PROPERTY_WEIGHT] = $content_object->get_weight();
        	$defaults[ :: PROPERTY_PRICE] = $content_object->get_price();
        	$defaults[ :: PROPERTY_CURRENCY] = $content_object->get_currency();
        	$defaults[ :: PROPERTY_SYNOPSIS] = $content_object->get_synopsis();
        	$defaults[ :: PROPERTY_REVIEW] = $content_object->get_review();
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new ();
        $this->fill_properties($object);
        parent :: set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $this->fill_properties($object);
        parent :: set_content_object($object);
        return parent :: update_content_object();
    }

    private function fill_properties($object)
    {
    	$object->set_id($this->exportValue( :: PROPERTY_ID]));
    	$object->set_original_title($this->exportValue( :: PROPERTY_ORIGINAL_TITLE]));
    	$object->set_issue($this->exportValue( :: PROPERTY_ISSUE]));
    	$object->set_series($this->exportValue( :: PROPERTY_SERIES]));
    	$object->set_original_series($this->exportValue( :: PROPERTY_ORIGINAL_SERIES]));
    	$object->set_subseries($this->exportValue( :: PROPERTY_SUBSERIES]));
    	$object->set_artist($this->exportValue( :: PROPERTY_ARTIST]));
    	$object->set_writer($this->exportValue( :: PROPERTY_WRITER]));
    	$object->set_colorist($this->exportValue( :: PROPERTY_COLORIST]));
    	$object->set_inker($this->exportValue( :: PROPERTY_INKER]));
    	$object->set_editor($this->exportValue( :: PROPERTY_EDITOR]));
    	$object->set_collection($this->exportValue( :: PROPERTY_COLLECTION]));
    	$object->set_collection_issue($this->exportValue( :: PROPERTY_COLLECTION_ISSUE]));
    	$object->set_type($this->exportValue( :: PROPERTY_TYPE]));
    	$object->set_binding($this->exportValue( :: PROPERTY_BINDING]));
    	$object->set_pages($this->exportValue( :: PROPERTY_PAGES]));
    	$object->set_year($this->exportValue( :: PROPERTY_YEAR]));
    	$object->set_genre($this->exportValue( :: PROPERTY_GENRE]));
    	$object->set_limited($this->exportValue( :: PROPERTY_LIMITED]));
    	$object->set_signed($this->exportValue( :: PROPERTY_SIGNED]));
    	$object->set_language($this->exportValue( :: PROPERTY_LANGUAGE]));
    	$object->set_colour($this->exportValue( :: PROPERTY_COLOUR]));
    	$object->set_weight($this->exportValue( :: PROPERTY_WEIGHT]));
    	$object->set_price($this->exportValue( :: PROPERTY_PRICE]));
    	$object->set_currency($this->exportValue( :: PROPERTY_CURRENCY]));
    	$object->set_synopsis($this->exportValue( :: PROPERTY_SYNOPSIS]));
    	$object->set_review($this->exportValue( :: PROPERTY_REVIEW]));
    }
}
?>