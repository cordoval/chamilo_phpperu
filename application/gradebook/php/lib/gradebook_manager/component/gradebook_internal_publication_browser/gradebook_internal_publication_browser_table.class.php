<?php

namespace application\gradebook;

use common\libraries\WebApplication;
use common\libraries\ObjectTable;

require_once WebApplication :: get_application_class_lib_path('gradebook') . 'gradebook_manager/component/gradebook_internal_publication_browser/gradebook_internal_publication_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'gradebook_manager/component/gradebook_internal_publication_browser/gradebook_internal_publication_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'gradebook_manager/component/gradebook_internal_publication_browser/gradebook_internal_publication_browser_table_cell_renderer.class.php';

class GradebookInternalPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'gradebook_publication_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters)
    {
        $model = new GradebookInternalPublicationBrowserTableColumnModel($browser);
        $renderer = new GradebookInternalPublicationBrowserTableCellRenderer($browser);
        $data_provider = new GradebookInternalPublicationBrowserTableDataProvider($browser);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);

        $this->set_default_row_count(20);
    }

}

?>