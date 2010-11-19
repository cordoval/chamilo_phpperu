<?php
namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Path;

use common\extensions\external_repository_manager\DefaultExternalRepositoryObjectTableDataProvider;

require_once Path :: get_common_extensions_path() . 'external_repository_manager/php/table/default_external_repository_object_table_data_provider.class.php';

class FedoraExternalRepositoryTableDataProvider extends DefaultExternalRepositoryObjectTableDataProvider
{
}
?>