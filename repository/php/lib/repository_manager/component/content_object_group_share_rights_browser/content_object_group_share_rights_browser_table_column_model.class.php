<?php

require_once dirname (__FILE__) . '/../content_object_user_share_rights_browser/action_column.php';

/**
 * Table column model for the content object share rights browser table
 * @author Pieterjan Broekaert
 */
class ContentObjectGroupShareRightsBrowserTableColumnModel extends ObjectTableColumnModel
{
    
    function ContentObjectGroupShareRightsBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->add_column(new ObjectTableColumn(Group :: PROPERTY_NAME));
        $this->set_default_order_column(1);
        $this->add_column(new StaticTableColumn(Translation :: get('Users')));
        $this->add_column(new StaticTableColumn(Translation :: get('Subgroups')));
        $this->add_rights_columns();
        $this->add_column(new ActionColumn());

        //$this->set_columns(array_splice($this->get_columns(), 1));
    }

    /**
     * adds a column for every right available to a share
     */

    private function add_rights_columns()
    {
        $rights = ContentObjectShare :: get_rights();

        foreach ($rights as $right_id => $right_name)
        {
            $column_name = Utilities :: underscores_to_camelcase(strtolower($right_name));
            $column = new ShareRightColumn(Translation :: get($column_name),$right_id);
            $this->add_column($column);
        }
    }
}
?>