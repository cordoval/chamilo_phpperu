<?php
require_once dirname(__FILE__) . '/../../../tables/peer_assessment_publication_table/default_peer_assessment_publication_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../peer_assessment_publication.class.php';

/**
 * Table column model for the peer_assessment_publication browser table
 * @author Nick Van Loocke
 */

class PeerAssessmentPublicationBrowserTableColumnModel extends DefaultPeerAssessmentPublicationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function PeerAssessmentPublicationBrowserTableColumnModel()
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