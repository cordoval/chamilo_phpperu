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

//require_once dirname(__FILE__) . '/../wiki_tool.class.php';
//require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__) . '/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_plugin_path() . 'wiki/mediawiki_parser.class.php';

class WikiDisplayWikiItemViewerComponent extends WikiDisplay
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
                    $display_wiki_page = $version_object;
                }
                else
                {
                    $display_wiki_page = $wiki_page;
                }

                if ($wiki_page->get_id() == $version_object_id)
                {
                    Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, null);
                }
            }
            else
            {
                $display_wiki_page = $wiki_page;
            }

            $this->display_header($complex_wiki_page);

            $html[] = '<div class="wiki-pane-content-title">' . $display_wiki_page->get_title() . '</div>';
            $html[] = '<div class="wiki-pane-content-subtitle">' . Translation :: get('From') . ' ' . $this->get_root_content_object()->get_title() . '</div>';

            if ($version_object_id && $wiki_page->get_id() != $version_object_id)
            {
                $html[] = '<div class="wiki-pane-content-version">' . Translation :: get('WikiOldVersion') . '</div>';
            }

            //                $parser = new WikiParser($this, $this->get_root_content_object()->get_id(), $wiki_page->get_description(), $complex_wiki_page->get_id());
            $parser = new MediawikiParser($this, $display_wiki_page);

            //                $html[] = $parser->parse_wiki_text();
            //                $html[] = $parser->get_wiki_text();
            $html[] = '<div class="wiki-pane-content-body">';
            $html[] = $parser->parse();
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';

            echo implode("\n", $html);

            $this->display_footer();
        }
        else
        {
            $this->redirect(null, false, array(Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI));
        }
    }
}
?>