<?php
/**
 * $Id: wiki_homepage_setter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the component that allows the user to make a wiki_page the homepage.
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

class WikiDisplayWikiHomepageSetterComponent extends WikiDisplay
{

    function run()
    {
        $dm = RepositoryDataManager :: get_instance();
        $page = $dm->retrieve_complex_content_object_item(Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID));
        /*
         *  If the wiki_page isn't empy the homepage will be set
         */
        if (! empty($page))
        {
            $page->set_is_homepage(true);
            $page->update();
        }
        $this->redirect(null, '', array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, 'pid' => Request :: get('pid')));
    
    }
}

?>