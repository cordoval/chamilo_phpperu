<?php
/**
 * $Id: announcement_distribution.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute
 */
/**
 * This class describes a DistributePublication data object
 *
 * @author Hans De Bisschop
 */
class AnnouncementDistribution extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * DistributePublication properties
     */
    const PROPERTY_ANNOUNCEMENT = 'announcement_id';
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_STATUS = 'status';
    
    const STATUS_PENDING = 1;
    const STATUS_VERIFIED = 2;
    const STATUS_REFUSED = 3;
    const STATUS_SENDING = 4;
    const STATUS_SENT = 5;
    
    private $target_groups;
    private $target_users;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ANNOUNCEMENT, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED, self :: PROPERTY_STATUS));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return DistributeDataManager :: get_instance();
    }

    /**
     * Returns the announcement of this DistributePublication.
     * @return the announcement.
     */
    function get_announcement()
    {
        return $this->get_default_property(self :: PROPERTY_ANNOUNCEMENT);
    }

    /**
     * Sets the announcement of this DistributePublication.
     * @param announcement
     */
    function set_announcement($announcement)
    {
        $this->set_default_property(self :: PROPERTY_ANNOUNCEMENT, $announcement);
    }

    /**
     * Returns the publisher of this DistributePublication.
     * @return the publisher.
     */
    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Sets the publisher of this DistributePublication.
     * @param publisher
     */
    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * Returns the published of this DistributePublication.
     * @return the published.
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the published of this DistributePublication.
     * @param published
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    /**
     * Returns the status of this DistributePublication.
     * @return the status.
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the status of this DistributePublication.
     * @param status
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function get_distribution_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($this->get_announcement());
    }

    function get_distribution_publisher()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_publisher());
    }

    function get_target_users()
    {
        if (! isset($this->target_users))
        {
            $ddm = DistributeDataManager :: get_instance();
            $this->target_users = $ddm->retrieve_announcement_distribution_target_users($this);
        }
        
        return $this->target_users;
    }

    function get_target_groups()
    {
        if (! isset($this->target_groups))
        {
            $ddm = DistributeDataManager :: get_instance();
            $this->target_groups = $ddm->retrieve_announcement_distribution_target_groups($this);
        }
        
        return $this->target_groups;
    }

    function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }

    function set_target_groups($target_groups)
    {
        $this->target_groups = $target_groups;
    }

    function get_status_icon()
    {
        $status = $this->get_status();
        
        switch ($status)
        {
            case STATUS_PENDING :
                $status = array('icon' => 'pending', 'description' => 'Pending');
                break;
            case STATUS_VERIFIED :
                $status = array('icon' => 'verified', 'description' => 'Accepted');
                break;
            case STATUS_REFUSED :
                $status = array('icon' => 'refused', 'description' => 'Refused');
                break;
            case STATUS_SENDING :
                $status = array('icon' => 'sending', 'description' => 'BeingSent');
                break;
            case STATUS_SENT :
                $status = array('icon' => 'sent', 'description' => 'Sent');
                break;
            default :
                $status = array('icon' => 'unknown', 'description' => 'StatusUnknown');
                break;
        }
        
        return Theme :: get_image('status_' . $status['icon'], 'png', Translation :: get('Message' . $status['description']), null, ToolbarItem :: DISPLAY_ICON);
    }
}

?>