<?php
/**
 * $Id: phrases_publication_browser_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component.phrases_publication_browser
 */
require_once dirname(__FILE__) . '/phrases_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../../../tables/phrases_publication_table/default_phrases_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../../phrases_publication.class.php';
require_once dirname(__FILE__) . '/../../../../phrases_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Hans De Bisschop
 * @author
 */

class PhrasesPublicationBrowserTableCellRenderer extends DefaultPhrasesPublicationTableCellRenderer
{
    /**
     * The browser component
     * @var PhrasesManagerPhrasesPublicationsBrowserComponent
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function PhrasesPublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $phrases_publication)
    {
        if ($column === PhrasesPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($phrases_publication);
        }

        return parent :: render_cell($column, $phrases_publication);
    }

    /**
     * Gets the action links to display
     * @param PhrasesPublication $phrases_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($phrases_publication)
    {
        $phrases = $phrases_publication->get_publication_object();

        $toolbar_data = array();
        $toolbar_data[] = array('href' => $this->browser->get_update_phrases_publication_url($phrases_publication), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        $toolbar_data[] = array('href' => $this->browser->get_delete_phrases_publication_url($phrases_publication), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        $toolbar_data[] = array('href' => $this->browser->get_build_phrases_publication_url($phrases_publication), 'label' => Translation :: get('Build'), 'img' => Theme :: get_common_image_path() . 'action_build.png');

        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>