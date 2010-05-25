<?php
/**
 * This class describes the form for a ComicBook object.
 * @package repository.lib.content_object.link
 * @author Hans De Bisschop
 **/

require_once dirname(__FILE__) . '/comic_book.class.php';

class ComicBookForm extends ContentObjectForm
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
        $this->addElement('text', ComicBook :: PROPERTY_ORIGINAL_TITLE, Translation :: get('OriginalTitle'));
        $this->addRule(ComicBook :: PROPERTY_ORIGINAL_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_ISSUE, Translation :: get('Issue'));
        $this->addRule(ComicBook :: PROPERTY_ISSUE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_SERIES, Translation :: get('Series'));
        $this->addRule(ComicBook :: PROPERTY_SERIES, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_ORIGINAL_SERIES, Translation :: get('OriginalSeries'));
        $this->addRule(ComicBook :: PROPERTY_ORIGINAL_SERIES, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_SUBSERIES, Translation :: get('Subseries'));
        $this->addRule(ComicBook :: PROPERTY_SUBSERIES, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_ARTIST, Translation :: get('Artist'));
        $this->addRule(ComicBook :: PROPERTY_ARTIST, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_WRITER, Translation :: get('Writer'));
        $this->addRule(ComicBook :: PROPERTY_WRITER, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_COLORIST, Translation :: get('Colorist'));
        $this->addRule(ComicBook :: PROPERTY_COLORIST, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_INKER, Translation :: get('Inker'));
        $this->addRule(ComicBook :: PROPERTY_INKER, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_EDITOR, Translation :: get('Editor'));
        $this->addRule(ComicBook :: PROPERTY_EDITOR, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_COLLECTION, Translation :: get('Collection'));
        $this->addRule(ComicBook :: PROPERTY_COLLECTION, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_COLLECTION_ISSUE, Translation :: get('CollectionIssue'));
        $this->addRule(ComicBook :: PROPERTY_COLLECTION_ISSUE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_TYPE, Translation :: get('Type'));
        $this->addRule(ComicBook :: PROPERTY_TYPE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_BINDING, Translation :: get('Binding'));
        $this->addRule(ComicBook :: PROPERTY_BINDING, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_PAGES, Translation :: get('Pages'));
        $this->addRule(ComicBook :: PROPERTY_PAGES, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_YEAR, Translation :: get('Year'));
        $this->addRule(ComicBook :: PROPERTY_YEAR, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_GENRE, Translation :: get('Genre'));
        $this->addRule(ComicBook :: PROPERTY_GENRE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_LIMITED, Translation :: get('Limited'));
        $this->addRule(ComicBook :: PROPERTY_LIMITED, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_SIGNED, Translation :: get('Signed'));
        $this->addRule(ComicBook :: PROPERTY_SIGNED, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_LANGUAGE, Translation :: get('Language'));
        $this->addRule(ComicBook :: PROPERTY_LANGUAGE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_COLOUR, Translation :: get('Colour'));
        $this->addRule(ComicBook :: PROPERTY_COLOUR, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_WEIGHT, Translation :: get('Weight'));
        $this->addRule(ComicBook :: PROPERTY_WEIGHT, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_PRICE, Translation :: get('Price'));
        $this->addRule(ComicBook :: PROPERTY_PRICE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_CURRENCY, Translation :: get('Currency'));
        $this->addRule(ComicBook :: PROPERTY_CURRENCY, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_SYNOPSIS, Translation :: get('Synopsis'));
        $this->addRule(ComicBook :: PROPERTY_SYNOPSIS, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', ComicBook :: PROPERTY_REVIEW, Translation :: get('Review'));
        $this->addRule(ComicBook :: PROPERTY_REVIEW, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $url = $this->get_path(WEB_PATH) . 'repository/xml_feeds/xml_image_feed.php';
        $locale = array();
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $locale['Display'] = Translation :: get('SelectCovers');
        $this->addElement('element_finder', ComicBook :: PROPERTY_COVERS, Translation :: get('Covers'), $url, $locale, array());
        
        $locale['Display'] = Translation :: get('SelectExtracts');
        $this->addElement('element_finder', ComicBook :: PROPERTY_EXTRACTS, Translation :: get('Extracts'), $url, $locale, array());
        
//        $this->addElement('text', ComicBook :: PROPERTY_COVERS, Translation :: get('Covers'));
//        $this->addRule(ComicBook :: PROPERTY_COVERS, Translation :: get('ThisFieldIsRequired'), 'required');
//        
//        $this->addElement('text', ComicBook :: PROPERTY_EXTRACTS, Translation :: get('Extracts'));
//        $this->addRule(ComicBook :: PROPERTY_EXTRACTS, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('textarea', ComicBook :: PROPERTY_TAGS, Translation :: get('Tags'), array('cols' => '70', 'rows' => '5'));
        $this->addRule(ComicBook :: PROPERTY_TAGS, Translation :: get('ThisFieldIsRequired'), 'required');
    
    }

    function setDefaults($defaults = array ())
    {
        $content_object = $this->get_content_object();
        if (isset($content_object))
        {
            $defaults[ComicBook :: PROPERTY_ORIGINAL_TITLE] = $content_object->get_original_title();
            $defaults[ComicBook :: PROPERTY_ISSUE] = $content_object->get_issue();
            $defaults[ComicBook :: PROPERTY_SERIES] = $content_object->get_series();
            $defaults[ComicBook :: PROPERTY_ORIGINAL_SERIES] = $content_object->get_original_series();
            $defaults[ComicBook :: PROPERTY_SUBSERIES] = $content_object->get_subseries();
            $defaults[ComicBook :: PROPERTY_ARTIST] = $content_object->get_artist();
            $defaults[ComicBook :: PROPERTY_WRITER] = $content_object->get_writer();
            $defaults[ComicBook :: PROPERTY_COLORIST] = $content_object->get_colorist();
            $defaults[ComicBook :: PROPERTY_INKER] = $content_object->get_inker();
            $defaults[ComicBook :: PROPERTY_EDITOR] = $content_object->get_editor();
            $defaults[ComicBook :: PROPERTY_COLLECTION] = $content_object->get_collection();
            $defaults[ComicBook :: PROPERTY_COLLECTION_ISSUE] = $content_object->get_collection_issue();
            $defaults[ComicBook :: PROPERTY_TYPE] = $content_object->get_type();
            $defaults[ComicBook :: PROPERTY_BINDING] = $content_object->get_binding();
            $defaults[ComicBook :: PROPERTY_PAGES] = $content_object->get_pages();
            $defaults[ComicBook :: PROPERTY_YEAR] = $content_object->get_year();
            $defaults[ComicBook :: PROPERTY_GENRE] = $content_object->get_genre();
            $defaults[ComicBook :: PROPERTY_LIMITED] = $content_object->get_limited();
            $defaults[ComicBook :: PROPERTY_SIGNED] = $content_object->get_signed();
            $defaults[ComicBook :: PROPERTY_LANGUAGE] = $content_object->get_language();
            $defaults[ComicBook :: PROPERTY_COLOUR] = $content_object->get_colour();
            $defaults[ComicBook :: PROPERTY_WEIGHT] = $content_object->get_weight();
            $defaults[ComicBook :: PROPERTY_PRICE] = $content_object->get_price();
            $defaults[ComicBook :: PROPERTY_CURRENCY] = $content_object->get_currency();
            $defaults[ComicBook :: PROPERTY_SYNOPSIS] = $content_object->get_synopsis();
            $defaults[ComicBook :: PROPERTY_REVIEW] = $content_object->get_review();
            $defaults[ComicBook :: PROPERTY_COVERS] = $content_object->get_covers();
            $defaults[ComicBook :: PROPERTY_EXTRACTS] = $content_object->get_extracts();
            $defaults[ComicBook :: PROPERTY_TAGS] = $content_object->get_tags();
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new ComicBook();
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
        $object->set_original_title($this->exportValue(ComicBook :: PROPERTY_ORIGINAL_TITLE));
        $object->set_issue($this->exportValue(ComicBook :: PROPERTY_ISSUE));
        $object->set_series($this->exportValue(ComicBook :: PROPERTY_SERIES));
        $object->set_original_series($this->exportValue(ComicBook :: PROPERTY_ORIGINAL_SERIES));
        $object->set_subseries($this->exportValue(ComicBook :: PROPERTY_SUBSERIES));
        $object->set_artist($this->exportValue(ComicBook :: PROPERTY_ARTIST));
        $object->set_writer($this->exportValue(ComicBook :: PROPERTY_WRITER));
        $object->set_colorist($this->exportValue(ComicBook :: PROPERTY_COLORIST));
        $object->set_inker($this->exportValue(ComicBook :: PROPERTY_INKER));
        $object->set_editor($this->exportValue(ComicBook :: PROPERTY_EDITOR));
        $object->set_collection($this->exportValue(ComicBook :: PROPERTY_COLLECTION));
        $object->set_collection_issue($this->exportValue(ComicBook :: PROPERTY_COLLECTION_ISSUE));
        $object->set_type($this->exportValue(ComicBook :: PROPERTY_TYPE));
        $object->set_binding($this->exportValue(ComicBook :: PROPERTY_BINDING));
        $object->set_pages($this->exportValue(ComicBook :: PROPERTY_PAGES));
        $object->set_year($this->exportValue(ComicBook :: PROPERTY_YEAR));
        $object->set_genre($this->exportValue(ComicBook :: PROPERTY_GENRE));
        $object->set_limited($this->exportValue(ComicBook :: PROPERTY_LIMITED));
        $object->set_signed($this->exportValue(ComicBook :: PROPERTY_SIGNED));
        $object->set_language($this->exportValue(ComicBook :: PROPERTY_LANGUAGE));
        $object->set_colour($this->exportValue(ComicBook :: PROPERTY_COLOUR));
        $object->set_weight($this->exportValue(ComicBook :: PROPERTY_WEIGHT));
        $object->set_price($this->exportValue(ComicBook :: PROPERTY_PRICE));
        $object->set_currency($this->exportValue(ComicBook :: PROPERTY_CURRENCY));
        $object->set_synopsis($this->exportValue(ComicBook :: PROPERTY_SYNOPSIS));
        $object->set_review($this->exportValue(ComicBook :: PROPERTY_REVIEW));
        $object->set_covers($this->exportValue(ComicBook :: PROPERTY_COVERS));
        $object->set_extracts($this->exportValue(ComicBook :: PROPERTY_EXTRACTS));
        $object->set_tags($this->exportValue(ComicBook :: PROPERTY_TAGS));
    }
}
?>