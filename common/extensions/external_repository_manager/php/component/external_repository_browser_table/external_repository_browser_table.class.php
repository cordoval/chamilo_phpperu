<?php
class ExternalRepositoryBrowserTable extends ObjectTable
{
    static function factory($type, $browser, $parameters, $condition)
    {
        $class = Utilities :: underscores_to_camelcase($type) . 'ExternalRepositoryTable';
        require_once Path :: get_common_extensions_path() . 'external_repository_manager/implementation/' . $type . '/php/component/' . $type . '_external_repository_table/' . $type . '_external_repository_table.class.php';
        return new $class($browser, $parameters, $condition);
    }
}
?>