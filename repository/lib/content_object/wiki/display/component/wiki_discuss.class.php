<?php
/**
 * $Id: wiki_discuss.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the discuss page. Here a user can add feedback to a wiki_page.
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */
require_once Path :: get_plugin_path() . 'wiki/mediawiki_parser.class.php';

class WikiDisplayWikiDiscussComponent extends WikiDisplay
{
    private $wiki_page_id;
    private $complex_id;
    private $feedback_id;
    private $links;
    const TITLE_MARKER = '<!-- /title -->';
    const DESCRIPTION_MARKER = '<!-- /description -->';

    function run()
    {
        $this->action_bar = $this->get_toolbar($this, $this->get_root_content_object_id(), $this->get_root_content_object(), $this->get_selected_complex_content_object_item());

        $this->set_parameter(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, $this->get_selected_complex_content_object_item_id());

        $feedback_manager = new FeedbackManager($this, $this->get_application_name(), $this->get_publication()->get_id(), $this->get_selected_complex_content_object_item_id());
        $feedback_manager->run();
    }

    function add_actionbar_item($item)
    {
        $this->action_bar->add_common_action($item);
    }

    function display_header()
    {
        $complex_wiki_page_id = Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $complex_wiki_page = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_wiki_page_id);
        $wiki_page = $complex_wiki_page->get_ref_object();

        parent :: display_header($complex_wiki_page);

        $parser = new MediawikiParser($wiki_page);

        $html[] = '<div class="wiki-pane-content-title">' . Translation :: get('Discuss') . ' ' . $wiki_page->get_title() . '</div>';
        $html[] = '<div class="wiki-pane-content-subtitle">' . Translation :: get('From') . ' ' . $this->get_root_content_object()->get_title() . '</div>';
        $html[] = '<div class="wiki-pane-content-discuss">';

        //                $html[] = $parser->parse_wiki_text();
        //                $html[] = $parser->get_wiki_text();
        $html[] = $parser->parse();
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $html[] = '<div class="wiki-pane-content-feedback">';

        echo implode("\n", $html);
    }

	function display_footer()
    {
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        echo implode("\n", $html);

    	return parent :: display_footer();
    }


}

?>