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
        //$this->add_textfield(ComicBook :: PROPERTY_ORIGINAL_TITLE, Translation :: get('OriginalTitle'), true, array('size' => '255', 'style' => 'width: 300px;'));
        $this->add_textfield(ComicBook :: PROPERTY_ISSUE, Translation :: get('Issue'), true, array('size' => '5', 'style' => 'width: 50px;'));
        $this->add_textfield(ComicBook :: PROPERTY_SERIES, Translation :: get('Series'), true, array('size' => '255', 'style' => 'width: 300px;'));
        //$this->add_textfield(ComicBook :: PROPERTY_ORIGINAL_SERIES, Translation :: get('OriginalSeries'), true, array('size' => '255', 'style' => 'width: 300px;'));
        //$this->add_textfield(ComicBook :: PROPERTY_SUBSERIES, Translation :: get('Subseries'), true, array('size' => '255', 'style' => 'width: 300px;'));
        $this->add_textfield(ComicBook :: PROPERTY_COLLECTION, Translation :: get('Collection'), true, array('size' => '255', 'style' => 'width: 300px;'));
        //$this->add_textfield(ComicBook :: PROPERTY_COLLECTION_ISSUE, Translation :: get('CollectionIssue'), true, array('size' => '5', 'style' => 'width: 50px;'));
        $this->addElement('category');
        
        $this->addElement('category', Translation :: get('Authors'));
        $this->add_textfield(ComicBook :: PROPERTY_ARTIST, Translation :: get('Artist'), true, array('size' => '255', 'style' => 'width: 300px;'));
        $this->add_textfield(ComicBook :: PROPERTY_WRITER, Translation :: get('Writer'), true, array('size' => '255', 'style' => 'width: 300px;'));
        $this->add_textfield(ComicBook :: PROPERTY_COLORIST, Translation :: get('Colorist'), true, array('size' => '255', 'style' => 'width: 300px;'));
        $this->add_textfield(ComicBook :: PROPERTY_INKER, Translation :: get('Inker'), true, array('size' => '255', 'style' => 'width: 300px;'));
        $this->add_textfield(ComicBook :: PROPERTY_EDITOR, Translation :: get('Editor'), true, array('size' => '255', 'style' => 'width: 300px;'));
        $this->addElement('category');
        
        $this->addElement('category', Translation :: get('Edition'));
        
        $types = array();
        $types['Single'] = Translation :: get('Single');
        $types['Coffret'] = Translation :: get('Coffret');
        $types['HorsSerie'] = Translation :: get('HorsSerie');
        $types['Complete'] = Translation :: get('Complete');
        $types['Luxury'] = Translation :: get('LuxuryEdition');
        $types['TPB'] = Translation :: get('TradePaperback');
        $this->addElement('select', ComicBook :: PROPERTY_BOOK_TYPE, Translation :: get('Type'), $types);
        
        $bindings = array('HC' => 'HC', 'SC' => 'SC', 'HC & SC' => 'HC &amp; SC');
        $this->addElement('select', ComicBook :: PROPERTY_BINDING, Translation :: get('Binding'), $bindings);
        $this->add_textfield(ComicBook :: PROPERTY_PAGES, Translation :: get('Pages'), true, array('size' => '5', 'style' => 'width: 50px;'));
        $this->add_textfield(ComicBook :: PROPERTY_YEAR, Translation :: get('Year'), true, array('size' => '5', 'style' => 'width: 50px;'));
        
        //$this->addElement('text', ComicBook :: PROPERTY_GENRE, Translation :: get('Genre'));
        //$this->addRule(ComicBook :: PROPERTY_GENRE, Translation :: get('ThisFieldIsRequired'), 'required');
        //$this->addElement('checkbox', ComicBook :: PROPERTY_LIMITED, Translation :: get('Limited'));
        //$this->addElement('checkbox', ComicBook :: PROPERTY_SIGNED, Translation :: get('Signed'));
        

        $this->addElement('select', ComicBook :: PROPERTY_LANGUAGE, Translation :: get('Language'), AdminDataManager :: get_languages());
        $this->addElement('checkbox', ComicBook :: PROPERTY_COLOUR, Translation :: get('Colour'));
        
        //$this->addElement('text', ComicBook :: PROPERTY_WEIGHT, Translation :: get('Weight'));
        //$this->addRule(ComicBook :: PROPERTY_WEIGHT, Translation :: get('ThisFieldIsRequired'), 'required');
        //$this->addElement('text', ComicBook :: PROPERTY_PRICE, Translation :: get('Price'));
        //$this->addRule(ComicBook :: PROPERTY_PRICE, Translation :: get('ThisFieldIsRequired'), 'required');
        //$this->addElement('text', ComicBook :: PROPERTY_CURRENCY, Translation :: get('Currency'));
        //$this->addRule(ComicBook :: PROPERTY_CURRENCY, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('category');
        
        $this->addElement('category', Translation :: get('Content'));
        
        $html_editor_options = array();
        $html_editor_options['width'] = '500';
        $html_editor_options['height'] = '100';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
        
        $this->add_html_editor(ComicBook :: PROPERTY_SYNOPSIS, Translation :: get('Synopsis'), false, $html_editor_options);
        $this->add_html_editor(ComicBook :: PROPERTY_FACTS, Translation :: get('Facts'), false, $html_editor_options);
        
        $this->addElement('textarea', ComicBook :: PROPERTY_TAGS, Translation :: get('Tags'), array('cols' => '60', 'rows' => '2'));
        $this->addRule(ComicBook :: PROPERTY_TAGS, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('category');
        
        $this->addElement('category', Translation :: get('Links'));
        
        $locale = array();
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        // Comic book covers
        $url = $this->get_path(WEB_PATH) . 'repository/xml_feeds/xml_image_feed.php';
        $locale['Display'] = Translation :: get('SelectCovers');
        $covers = Utilities :: content_objects_for_element_finder($this->get_content_object()->get_covers());
        $cover = $this->addElement('element_finder', ComicBook :: ATTACHMENT_COVER, Translation :: get('Covers'), $url, $locale, $covers);
        $cover->setHeight('100');
        
        // Comic book extract
        $locale['Display'] = Translation :: get('SelectExtract');
        $extract = $this->addElement('image_selecter', ComicBook :: ATTACHMENT_EXTRACT, Translation :: get('Extract'), $url, $locale);
        $extract->setHeight('100');
        
        // Encyclopedia items related to the comic book
        $url = $this->get_path(WEB_PATH) . 'repository/lib/content_object/encyclopedia_item/xml_feeds/xml_encyclopedia_item_feed.php';
        $locale['Display'] = Translation :: get('SelectEncyclopediaItems');
        $encyclopedia_items = Utilities :: content_objects_for_element_finder($this->get_content_object()->get_encyclopedia_items());
        $encyclopedia_item = $this->addElement('element_finder', ComicBook :: ATTACHMENT_ENCYCLOPEDIA_ITEM, Translation :: get('EncyclopediaItems'), $url, $locale, $encyclopedia_items);
        $encyclopedia_item->setHeight('100');
    }

    function setDefaults($defaults = array ())
    {
        $content_object = $this->get_content_object();
        if (isset($content_object))
        {
            //$defaults[ComicBook :: PROPERTY_ORIGINAL_TITLE] = $content_object->get_original_title();
            $defaults[ComicBook :: PROPERTY_ISSUE] = $content_object->get_issue();
            $defaults[ComicBook :: PROPERTY_SERIES] = $content_object->get_series();
            //$defaults[ComicBook :: PROPERTY_ORIGINAL_SERIES] = $content_object->get_original_series();
            //$defaults[ComicBook :: PROPERTY_SUBSERIES] = $content_object->get_subseries();
            $defaults[ComicBook :: PROPERTY_ARTIST] = $content_object->get_artist();
            $defaults[ComicBook :: PROPERTY_WRITER] = $content_object->get_writer();
            $defaults[ComicBook :: PROPERTY_COLORIST] = $content_object->get_colorist();
            $defaults[ComicBook :: PROPERTY_INKER] = $content_object->get_inker();
            $defaults[ComicBook :: PROPERTY_EDITOR] = $content_object->get_editor();
            $defaults[ComicBook :: PROPERTY_COLLECTION] = $content_object->get_collection();
            //$defaults[ComicBook :: PROPERTY_COLLECTION_ISSUE] = $content_object->get_collection_issue();
            $defaults[ComicBook :: PROPERTY_BOOK_TYPE] = $content_object->get_book_type();
            $defaults[ComicBook :: PROPERTY_BINDING] = $content_object->get_binding();
            $defaults[ComicBook :: PROPERTY_PAGES] = $content_object->get_pages();
            $defaults[ComicBook :: PROPERTY_YEAR] = $content_object->get_year();
            //$defaults[ComicBook :: PROPERTY_GENRE] = $content_object->get_genre();
            //$defaults[ComicBook :: PROPERTY_LIMITED] = $content_object->get_limited();
            //$defaults[ComicBook :: PROPERTY_SIGNED] = $content_object->get_signed();
            $defaults[ComicBook :: PROPERTY_LANGUAGE] = $content_object->get_language();
            $defaults[ComicBook :: PROPERTY_COLOUR] = $content_object->get_colour();
            //$defaults[ComicBook :: PROPERTY_WEIGHT] = $content_object->get_weight();
            //$defaults[ComicBook :: PROPERTY_PRICE] = $content_object->get_price();
            //$defaults[ComicBook :: PROPERTY_CURRENCY] = $content_object->get_currency();
            $defaults[ComicBook :: PROPERTY_SYNOPSIS] = $content_object->get_synopsis();
            $defaults[ComicBook :: PROPERTY_FACTS] = $content_object->get_facts();
            
            $defaults[ComicBook :: ATTACHMENT_EXTRACT] = $content_object->get_extract(true);
            $defaults[ComicBook :: PROPERTY_TAGS] = $content_object->get_tags();
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new ComicBook();
        $this->fill_properties($object);
        parent :: set_content_object($object);
        $object = parent :: create_content_object();
        $this->process_attachments($object);
        return $object;
    }

    function update_content_object()
    {        
        $object = $this->get_content_object();
        $this->fill_properties($object);
        parent :: set_content_object($object);
        parent :: update_content_object();
        $this->process_attachments($object);
        return true;
    }
    
    private function process_attachments(ContentObject $object)
    {
        $covers = $this->exportValue(ComicBook :: ATTACHMENT_COVER);
        $object->set_covers($covers['lo']);
        
        $encyclopedia_items = $this->exportValue(ComicBook :: ATTACHMENT_ENCYCLOPEDIA_ITEM);
        $encyclopedia_items['lo'] = !isset($encyclopedia_items['lo']) ? array() : $encyclopedia_items['lo'];
        $object->set_encyclopedia_items($encyclopedia_items['lo']);
        
        $object->set_extracts($this->exportValue(ComicBook :: ATTACHMENT_EXTRACT));
    }

    private function fill_properties($object)
    {
        //$object->set_original_title($this->exportValue(ComicBook :: PROPERTY_ORIGINAL_TITLE));
        $object->set_issue($this->exportValue(ComicBook :: PROPERTY_ISSUE));
        $object->set_series($this->exportValue(ComicBook :: PROPERTY_SERIES));
        //$object->set_original_series($this->exportValue(ComicBook :: PROPERTY_ORIGINAL_SERIES));
        //$object->set_subseries($this->exportValue(ComicBook :: PROPERTY_SUBSERIES));
        $object->set_artist($this->exportValue(ComicBook :: PROPERTY_ARTIST));
        $object->set_writer($this->exportValue(ComicBook :: PROPERTY_WRITER));
        $object->set_colorist($this->exportValue(ComicBook :: PROPERTY_COLORIST));
        $object->set_inker($this->exportValue(ComicBook :: PROPERTY_INKER));
        $object->set_editor($this->exportValue(ComicBook :: PROPERTY_EDITOR));
        $object->set_collection($this->exportValue(ComicBook :: PROPERTY_COLLECTION));
        //$object->set_collection_issue($this->exportValue(ComicBook :: PROPERTY_COLLECTION_ISSUE));
        $object->set_book_type($this->exportValue(ComicBook :: PROPERTY_BOOK_TYPE));
        $object->set_binding($this->exportValue(ComicBook :: PROPERTY_BINDING));
        $object->set_pages($this->exportValue(ComicBook :: PROPERTY_PAGES));
        $object->set_year($this->exportValue(ComicBook :: PROPERTY_YEAR));
        //$object->set_genre($this->exportValue(ComicBook :: PROPERTY_GENRE));
        //$object->set_limited($this->exportValue(ComicBook :: PROPERTY_LIMITED));
        //$object->set_signed($this->exportValue(ComicBook :: PROPERTY_SIGNED));
        $object->set_language($this->exportValue(ComicBook :: PROPERTY_LANGUAGE));
        $object->set_colour($this->exportValue(ComicBook :: PROPERTY_COLOUR));
        //$object->set_weight($this->exportValue(ComicBook :: PROPERTY_WEIGHT));
        //$object->set_price($this->exportValue(ComicBook :: PROPERTY_PRICE));
        //$object->set_currency($this->exportValue(ComicBook :: PROPERTY_CURRENCY));
        $object->set_synopsis($this->exportValue(ComicBook :: PROPERTY_SYNOPSIS));
        $object->set_facts($this->exportValue(ComicBook :: PROPERTY_FACTS));
        $object->set_tags($this->exportValue(ComicBook :: PROPERTY_TAGS));
    }
}
?>