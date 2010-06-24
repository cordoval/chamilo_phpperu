<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */
require_once dirname(__FILE__) . '/../../wiki.class.php';

class WikiDisplayUpdaterComponent extends WikiDisplay
{
    function run()
    {
        $browser = ComplexDisplayComponent :: factory(ComplexDisplayComponent :: UPDATER_COMPONENT, $this);
        $browser->run();
    }
    
    function display_header()
    {
        $complex_wiki_page_id = Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $complex_wiki_page = RepositoryDataManager::get_instance()->retrieve_complex_content_object_item($complex_wiki_page_id);
        
        parent :: display_header($complex_wiki_page);
    }
}

?>