<?php
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

class WikiDisplayVersionDeleterComponent extends WikiDisplay
{

    function run()
    {
        $complex_wiki_page_id = Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($complex_wiki_page_id)
        {
            $object_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
            $complex_wiki_page = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_wiki_page_id);
            $wiki_page = $complex_wiki_page->get_ref_object();

            if ($object_id)
            {
                $object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);

                $delete_allowed = RepositoryDataManager :: content_object_deletion_allowed($object, 'version');
                if ($delete_allowed)
                {
                    if ($object->delete_version())
                    {
                        $this->redirect(Translation :: get('WikiPageVersionDeleted'), false, array(
                                Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY,
                                ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
                    }
                    else
                    {
                        $this->redirect(Translation :: get('WikiPageVersionNotDeleted'), false, array(
                                Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY,
                                ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
                    }

                }
                else
                {
                    $this->redirect(Translation :: get('WikiPageVersionNotDeleted'), false, array(
                            Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY,
                            ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
                }
            }
            else
            {
                $this->redirect(Translation :: get('WikiPageVersionNotDeleted'), false, array(
                        Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
            }
        }
        else
        {
            $this->redirect(null, false, array(Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI));
        }
    }
}
?>