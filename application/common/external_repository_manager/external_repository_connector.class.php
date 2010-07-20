<?php
/**
 * @author Hans De Bisschop
 */
interface ExternalRepositoryConnector
{

    /**
     * @param string $id
     */
    function retrieve_external_repository_object($id);

    /**
     * @param mixed $condition
     * @param ObjectTableOrder $order_property
     * @param int $offset
     * @param int $count
     */
    function retrieve_external_repository_objects($condition, $order_property, $offset, $count);

    /**
     * @param mixed $condition
     */
    function count_external_repository_objects($condition);

    /**
     * @param string $id
     */
    function delete_external_repository_object($id);

    /**
     * @param string $id
     */
    function export_external_repository_object($id);

    /**
     * @param string $query
     */
    static function translate_search_query($query);
}
?>