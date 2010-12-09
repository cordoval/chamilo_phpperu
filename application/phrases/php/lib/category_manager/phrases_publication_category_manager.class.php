<?php
namespace application\phrases;

use common\extensions\category_manager\CategoryManager;
/**
 * $Id: phrases_publication_category_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.phrases.category_manager
 */
require_once dirname(__FILE__) . '/../phrases_data_manager.class.php';
require_once dirname(__FILE__) . '/phrases_publication_category.class.php';

class PhrasesPublicationCategoryManager extends CategoryManager
{

    function __construct($parent, $trail)
    {
        parent :: __construct($parent, $trail);
    }

    function get_category()
    {
        return new PhrasesPublicationCategory();
    }

    function count_categories($condition)
    {
        $adm = PhrasesDataManager :: get_instance();
        return $adm->count_phrases_publication_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        $adm = PhrasesDataManager :: get_instance();
        return $adm->retrieve_phrases_publication_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $adm = PhrasesDataManager :: get_instance();
        return $adm->select_next_phrases_publication_category_display_order($parent_id);
    }
}
?>