<?php
/**
 * $Id: quota_manager.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
/**
 * This class provides some functionality to manage user quotas. There are two
 * different quota types. One is the disk space used by the user. The other is
 * the database space used by the user.
 *
 * @author Bart Mollet
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class QuotaManager
{
    /**
     * The owner
     */
    private $owner;
    /**
     * The used disk space
     */
    private $used_disk_space;
    /**
     * The used database space
     */
    private $used_database_space;
    /**
     * The max disk space
     */
    private $max_disk_space;
    /**
     * The max database space
     */
    private $max_database_space;
    /**
     * The max versions
     */
    private $max_versions;

    /**
     * Create a new QuotaManager
     * @param User $owner The user of which the quota should be calculated
     */
    public function QuotaManager($owner)
    {
        $this->owner = $owner;
        $this->used_disk_space = null;
        $this->used_database_space = null;
        $this->max_disk_space = null;
        $this->max_database_space = null;
    }

    /**
     * Get the used disk space
     * @return int The number of bytes used
     */
    public function get_used_disk_space()
    {
        if (is_null($this->used_disk_space))
        {
            $datamanager = RepositoryDataManager :: get_instance();
            $this->used_disk_space = $datamanager->get_used_disk_space($this->owner->get_id());
        }
        return $this->used_disk_space;
    }

    /**
     * Get the used disk space
     * @return float The percentage of disk space used (0 <= value <= 100)
     */
    public function get_used_disk_space_percent()
    {
        return 100 * $this->get_used_disk_space() / $this->get_max_disk_space();
    }

    /**
     * Get the available disk space
     * @return int The number of bytes available on disk
     */
    public function get_available_disk_space()
    {
        return $this->get_max_disk_space() - $this->get_used_disk_space();
    }

    /**
     * Get the used database space
     * @return int The number of learning objects in the repository of the
     * owner
     */
    public function get_used_database_space()
    {
        if (is_null($this->used_database_space))
        {
            $datamanager = RepositoryDatamanager :: get_instance();
            $condition = new AndCondition(new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->owner->get_id()), 
            							 new NotCondition(new InCondition(ContentObject :: PROPERTY_TYPE, array(LearningPathItem :: get_type_name(), PortfolioItem :: get_type_name()))));
            $this->used_database_space = $datamanager->count_content_objects($condition);
        }
        return $this->used_database_space;
    }

    /**
     * Get the used database space
     * @return float The percentage of database space used (0 <= value <= 100)
     */
    public function get_used_database_space_percent()
    {
        return 100 * $this->get_used_database_space() / $this->get_max_database_space();
    }

    /**
     * Get the available database space
     * @return int The number learning objects available in the database
     */
    public function get_available_database_space()
    {
        return $this->get_max_database_space() - $this->get_used_database_space();
    }

    /**
     * Get the maximum allowed disk space
     * @return int The number of bytes the user is allowed to use
     */
    public function get_max_disk_space()
    {
        if (is_null($this->max_disk_space))
        {
            $this->max_disk_space = $this->owner->get_disk_quota();
        }
        return $this->max_disk_space;
    }

    /**
     * Get the maximum allowed database space
     * @return int The number of learning objects the user is allowed to have
     */
    public function get_max_database_space()
    {
        if (is_null($this->max_database_space))
        {
            $this->max_database_space = $this->owner->get_database_quota();
        }
        return $this->max_database_space;
    }

    /**
     * Get the maximum allowed versions of an object (per object)
     * @return int The number of learning object versions the user is allowed to have
     */
    public function get_max_versions($type)
    {
        if (is_null($this->max_versions))
        {
            $owner = $this->owner;
            $version_quota = $owner->get_version_type_quota($type);

            if (isset($version_quota))
            {
                $this->max_versions = $version_quota;
            }
            else
            {
                $this->max_versions = $owner->get_version_quota();
            }
        }
        return $this->max_versions;
    }
}
?>