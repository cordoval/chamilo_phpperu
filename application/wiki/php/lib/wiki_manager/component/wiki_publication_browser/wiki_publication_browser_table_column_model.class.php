<?php
namespace application\wiki;

use common\libraries\ObjectTableColumn;
use common\libraries\StaticTableColumn;
use repository\ContentObject;

/**
 * $Id: wiki_publication_browser_table_column_model.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component.wiki_publication_browser
 */

/**
 * Table column model for the wiki_publication browser table
 * @author Sven Vanpoucke & Stefan Billiet
 */

class WikiPublicationBrowserTableColumnModel extends DefaultWikiPublicationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return ContentObjectTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }

    public function get_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION);
        return $columns;
    }
}
?>