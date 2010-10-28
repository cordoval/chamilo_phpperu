<?php
namespace repository\content_object\wiki;

use common\libraries\Request;
use common\libraries\Translation;
use repository\ComplexDisplay;
use repository\RepositoryManager;
use repository\RepositoryDataManager;

/**
 * $Id: wiki_item_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This viewer will show the selected wiki_page.
 * You'll be redirected here from the wiki_viewer page by clicking on the name of a wiki_page
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

class WikiDisplayVersionReverterComponent extends WikiDisplay
{

    function run()
    {
        $complex_wiki_page_id = Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($complex_wiki_page_id)
        {
            $version_object_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
            $complex_wiki_page = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_wiki_page_id);
            $wiki_page = $complex_wiki_page->get_ref_object();

            if ($version_object_id)
            {
                $version_object = RepositoryDataManager :: get_instance()->retrieve_content_object($version_object_id);
                if ($version_object && $version_object->get_object_number() == $wiki_page->get_object_number())
                {
                    if ($version_object->version())
                    {
                        $complex_wiki_page->set_ref($version_object->get_latest_version_id());
                        if ($complex_wiki_page->update())
                        {
                            $this->redirect(Translation :: get('WikiPageReverted'), false, array(Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
                        }
                        else
                        {
                            $this->redirect(Translation :: get('WikiPageRevertedPublicationNotUpdated'), false, array(Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
                        }
                    }
                    else
                    {
                        $this->redirect(Translation :: get('WikiPageNotReverted'), false, array(Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
                    }
                }
                else
                {
                    $this->redirect(Translation :: get('WikiPageNotReverted'), false, array(Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
                }
            }
            else
            {
                $this->redirect(Translation :: get('WikiPageNotReverted'), false, array(Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
            }
        }
        else
        {
            $this->redirect(null, false, array(Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI));
        }
    }
}
?>