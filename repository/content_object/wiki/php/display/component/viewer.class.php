<?php
namespace repository\content_object\wiki;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use repository\ComplexDisplay;
use MediawikiParser;
use MediawikiParserContext;

/**
 * $Id: wiki_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the compenent that allows the user to view all pages of a wiki.
 * If no homepage is set all available pages will be shown, otherwise the homepage will be shown.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__) . '/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_plugin_path() . 'wiki/mediawiki_parser.class.php';
require_once Path :: get_plugin_path() . 'wiki/mediawiki_parser_context.class.php';

class WikiDisplayViewerComponent extends WikiDisplay
{
    private $action_bar;

    function run()
    {
        $this->action_bar = $this->get_toolbar($this, $this->get_root_content_object()->get_id(), $this->get_root_content_object(), null);


        if ($this->get_root_content_object() != null)
        {
            $complex_wiki_homepage = $this->get_wiki_homepage($this->get_root_content_object_id());

            if (! is_null($complex_wiki_homepage))
            {
                Request :: set_get(ComplexDisplay::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, $complex_wiki_homepage->get_id());
            	$this->display_header($complex_wiki_homepage);

                $wiki_homepage = $complex_wiki_homepage->get_ref_object();

                $parser = new MediawikiParser(new MediawikiParserContext($this->get_root_content_object(), $wiki_homepage->get_title(), $wiki_homepage->get_description(), $this->get_parameters()));

                $html[] = '<div class="wiki-pane-content-title">' . $wiki_homepage->get_title() . '</div>';
                $html[] = '<div class="wiki-pane-content-subtitle">' . Translation :: get('From') . ' ' . $this->get_root_content_object()->get_title() . '</div>';

                $html[] = '<div class="wiki-pane-content-body">';
                $html[] = $parser->parse();
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';

                echo implode("\n", $html);
                $this->display_footer();
            }
            else
            {
                $this->redirect(Translation :: get('PleaseConfigureWikiHomepage'), false, array(Complexdisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_BROWSE_WIKI));
            }
        }
    }

    function  add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail = $this->get_breadcrumbtrail();
    }
}
?>