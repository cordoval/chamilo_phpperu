<?php
namespace application\phrases;

use common\extensions\category_manager\PlatformCategory;
use common\libraries\Path;
use common\libraries\Utilities;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesPublicationCategory extends PlatformCategory
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_category';

    function create()
    {
        $adm = PhrasesDataManager :: get_instance();
        $this->set_display_order($adm->select_next_phrases_publication_category_display_order($this->get_parent()));
        $succes = $adm->create_phrases_publication_category($this);

        if (! $succes)
        {
            return false;
        }

        $parent = $this->get_parent();
        if ($parent == 0)
        {
            $parent_id = PhrasesRights :: get_phrasess_subtree_root_id();
        }
        else
        {
            $parent_id = PhrasesRights :: get_location_id_by_identifier_from_phrasess_subtree($this->get_parent(), PhrasesRights :: TYPE_CATEGORY);
        }

        return PhrasesRights :: create_location_in_phrasess_subtree($this->get_name(), $this->get_id(), $parent_id, PhrasesRights :: TYPE_CATEGORY);
    }

    function update($move = false)
    {
        $succes = PhrasesDataManager :: get_instance()->update_phrases_publication_category($this);
        if (! $succes)
        {
            return false;
        }

        if ($move)
        {
            if ($this->get_parent())
            {
                $new_parent_id = PhrasesRights :: get_location_id_by_identifier_from_phrasess_subtree($this->get_parent(), PhrasesRights :: TYPE_CATEGORY);
            }
            else
            {
                $new_parent_id = PhrasesRights :: get_phrasess_subtree_root_id();
            }

            $location = PhrasesRights :: get_location_by_identifier_from_phrasess_subtree($this->get_id(), PhrasesRights :: TYPE_CATEGORY);
            return $location->move($new_parent_id);
        }

        return true;

    }

    function delete()
    {
        $location = PhrasesRights :: get_location_by_identifier_from_phrasess_subtree($this->get_id(), PhrasesRights :: TYPE_CATEGORY);
        if ($location)
        {
            if (! $location->remove())
            {
                return false;
            }
        }

        return PhrasesDataManager :: get_instance()->delete_phrases_publication_category($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}