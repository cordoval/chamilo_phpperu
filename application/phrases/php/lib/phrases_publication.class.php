<?php
namespace application\phrases;

use common\libraries\DataClass;
use common\libraries\EqualityCondition;
use repository\RepositoryDataManager;
use user\UserDataManager;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication';

    /**
     * PhrasesPublication properties
     */
    const PROPERTY_CONTENT_OBJECT = 'content_object_id';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_CATEGORY = 'category_id';
    const PROPERTY_LANGUAGE = 'language';

    private $target_groups;
    private $target_users;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(
                self :: PROPERTY_CONTENT_OBJECT,
                self :: PROPERTY_FROM_DATE,
                self :: PROPERTY_TO_DATE,
                self :: PROPERTY_HIDDEN,
                self :: PROPERTY_PUBLISHER,
                self :: PROPERTY_PUBLISHED,
                self :: PROPERTY_CATEGORY,
                self :: PROPERTY_LANGUAGE));
    }

    function get_data_manager()
    {
        return PhrasesDataManager :: get_instance();
    }

    /**
     * Returns the content_object of this PhrasesPublication.
     * @return the content_object.
     */
    function get_content_object()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT);
    }

    /**
     * Sets the content_object of this PhrasesPublication.
     * @param content_object
     */
    function set_content_object($content_object)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT, $content_object);
    }

    /**
     * Returns the from_date of this PhrasesPublication.
     * @return the from_date.
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    /**
     * Sets the from_date of this PhrasesPublication.
     * @param from_date
     */
    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    /**
     * Returns the to_date of this PhrasesPublication.
     * @return the to_date.
     */
    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Sets the to_date of this PhrasesPublication.
     * @param to_date
     */
    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    /**
     * Returns the hidden of this PhrasesPublication.
     * @return the hidden.
     */
    function get_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    /**
     * Sets the hidden of this PhrasesPublication.
     * @param hidden
     */
    function set_hidden($hidden)
    {
        $this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
    }

    /**
     * Returns the publisher of this PhrasesPublication.
     * @return the publisher.
     */
    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Sets the publisher of this PhrasesPublication.
     * @param publisher
     */
    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * Returns the published of this PhrasesPublication.
     * @return the published.
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the published of this PhrasesPublication.
     * @param published
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    /**
     * Returns the category of this PhrasesPublication.
     * @return the category.
     */
    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    /**
     * Sets the category of this PhrasesPublication.
     * @param category
     */
    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    /**
     * Returns the language of this PhrasesPublication.
     * @return string
     */
    function get_language()
    {
        return $this->get_default_property(self :: PROPERTY_LANGUAGE);
    }

    /**
     * Sets the language of this PhrasesPublication.
     * @param string $language
     */
    function set_language($language)
    {
        $this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
    }

    function set_target_groups($target_groups)
    {
        $this->target_groups = $target_groups;
    }

    function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }

    function toggle_visibility()
    {
        $this->set_hidden(! $this->get_hidden());
    }

    /**
     * Determines whether this publication is hidden or not
     * @return boolean True if the publication is hidden.
     */
    function is_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    function get_target_groups()
    {
        if (! $this->target_groups)
        {
            $condition = new EqualityCondition(PhrasesPublicationGroup :: PROPERTY_PUBLICATION, $this->get_id());
            $groups = $this->get_data_manager()->retrieve_phrases_publication_groups($condition);

            while ($group = $groups->next_result())
            {
                $this->target_groups[] = $group->get_group_id();
            }
        }

        return $this->target_groups;
    }

    function get_target_users()
    {
        if (! $this->target_users)
        {
            $condition = new EqualityCondition(PhrasesPublicationUser :: PROPERTY_PUBLICATION, $this->get_id());
            $users = $this->get_data_manager()->retrieve_phrases_publication_users($condition);

            while ($user = $users->next_result())
            {
                $this->target_users[] = $user->get_user();
            }
        }

        return $this->target_users;
    }

    function is_visible_for_target_user($user)
    {
        if ($user->is_platform_admin() || $user->get_id() == $this->get_publisher())
        {
            return true;
        }

        if ($this->get_target_groups() || $this->get_target_users())
        {
            $allowed = false;

            if (in_array($user->get_id(), $this->get_target_users()))
            {
                $allowed = true;
            }

            if (! $allowed)
            {
                $user_groups = $user->get_groups();

                while ($user_group = $user_groups->next_result())
                {
                    if (in_array($user_group->get_id(), $this->get_target_groups()))
                    {
                        $allowed = true;
                        break;
                    }
                }
            }

            if (! $allowed)
            {
                return false;
            }
        }

        if ($this->get_hidden())
        {
            return false;
        }

        $time = time();

        if ($time < $this->get_from_date() || $time > $this->get_to_date() && ! $this->is_forever())
        {
            return false;
        }

        return true;
    }

    function is_forever()
    {
        return ($this->get_from_date() == 0 && $this->get_to_date() == 0);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    function get_publication_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($this->get_content_object());
    }

    function get_publication_publisher()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_publisher());
    }

    function create()
    {
        $succes = parent :: create();
        if (! $succes)
        {
            return false;
        }

        $parent = $this->get_category();
        if ($parent)
        {
            $parent_location = PhrasesRights :: get_location_id_by_identifier_from_phrasess_subtree($this->get_category(), PhrasesRights :: TYPE_CATEGORY);
        }
        else
        {
            $parent_location = PhrasesRights :: get_phrasess_subtree_root_id();
        }

        return PhrasesRights :: create_location_in_phrasess_subtree($this->get_content_object(), $this->get_id(), $parent_location, PhrasesRights :: TYPE_PUBLICATION);
    }

    function delete()
    {
        $location = PhrasesRights :: get_location_by_identifier_from_phrasess_subtree($this->get_id(), PhrasesRights :: TYPE_PUBLICATION);
        if ($location)
        {
            if (! $location->remove())
            {
                return false;
            }
        }

        return parent :: delete();
    }
}

?>