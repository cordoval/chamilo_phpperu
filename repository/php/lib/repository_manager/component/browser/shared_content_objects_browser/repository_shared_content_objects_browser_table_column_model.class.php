<?php
namespace repository;

use common\libraries\ObjectTableColumnModel;
use common\libraries\StaticTableColumn;
use common\libraries\Translation;

/**
 * $Id: repository_shared_content_objects_browser_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser.shared_content_objects_browser
 */
require_once dirname(__FILE__) . '/../../../../content_object_table/default_shared_content_object_table_column_model.class.php';
/**
 * Table column model for the repository browser table
 */
class RepositorySharedContentObjectsBrowserTableColumnModel extends DefaultSharedContentObjectTableColumnModel
{
    /**
     * The tables sharing column
     */
    private static $sharing_column;

    /**
     * The tables rights column
     */
    private static $rights_column;

    private $browser;

    /**
     * Constructor
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->set_default_order_column(0);

        if ($browser->get_view() == RepositoryManager :: SHARED_VIEW_ALL_OBJECTS || $browser->get_view() == RepositoryManager :: SHARED_VIEW_OWN_OBJECTS)
        {
            $this->add_column(self :: get_rights_column());
        }

        if ($browser->get_view() == RepositoryManager :: SHARED_VIEW_ALL_OBJECTS || $browser->get_view() == RepositoryManager :: SHARED_VIEW_OTHERS_OBJECTS)
        {
            $this->add_column(self :: get_sharing_column());
        }
    }

    /**
     * Gets the sharing column
     * @return StaticTableColumn
     */
    static function get_sharing_column()
    {
        if (! isset(self :: $sharing_column))
        {
            self :: $sharing_column = new StaticTableColumn(Translation :: get('GivenRights'));
        }
        return self :: $sharing_column;
    }

    /**
     * Gets the sharing column
     * @return StaticTableColumn
     */
    static function get_rights_column()
    {
        if (! isset(self :: $rights_column))
        {
            self :: $rights_column = new StaticTableColumn(Translation :: get('ManageRights'));
        }
        return self :: $rights_column;
    }
}
?>