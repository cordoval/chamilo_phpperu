<?php
interface PeerAssessmentDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function create_peer_assessment_publication($peer_assessment_publication);

    function update_peer_assessment_publication($peer_assessment_publication);

    function delete_peer_assessment_publication($peer_assessment_publication);

    function count_peer_assessment_publications($conditions = null);

    function retrieve_peer_assessment_publication($id);

    function retrieve_peer_assessment_publications($condition = null, $offset = null, $count = null, $order_property = null);

    function create_peer_assessment_publication_category($peer_assessment_publication);

    function update_peer_assessment_publication_category($peer_assessment_publication);

    function delete_peer_assessment_publication_category($peer_assessment_publication);

    function count_peer_assessment_publication_categories($conditions = null);

    function retrieve_peer_assessment_publication_categories($condition = null, $offset = null, $count = null, $order_property = null);

}
?>