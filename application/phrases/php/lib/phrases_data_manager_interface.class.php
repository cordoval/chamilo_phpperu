<?php
namespace application\phrases;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

interface PhrasesDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function create_phrases_publication($phrases_publication);

    function update_phrases_publication($phrases_publication);

    function delete_phrases_publication($phrases_publication);

    function count_phrases_publications($conditions = null);

    function retrieve_phrases_publication($id);

    function retrieve_phrases_publications($condition = null, $offset = null, $count = null, $order_property = null);

    function create_phrases_publication_category($phrases_category);

    function update_phrases_publication_category($phrases_category);

    function delete_phrases_publication_category($phrases_category);

    function count_phrases_publication_categories($conditions = null);

    function retrieve_phrases_publication_category($id);

    function retrieve_phrases_publication_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function select_next_phrases_publication_category_display_order($parent);

    function create_survey_invitation($survey_invitation);

    function update_survey_invitation($survey_invitation);

    function delete_survey_invitation($survey_invitation);

    function count_survey_invitations($conditions = null);

    function retrieve_survey_invitation($id);

    function retrieve_survey_invitations($condition = null, $offset = null, $count = null, $order_property = null);

}
?>