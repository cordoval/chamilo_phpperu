<?php
require_once Path :: get_application_path() . 'lib/peer_assessment/data_manager/database.class.php';

class PeerAssessmentPubFeedback extends ContentObject
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_ID = 'id';
    const PROPERTY_PEER_ASSESSMENT_PUBLICATION_ID = 'peer_assessment_publication_id';
    const PROPERTY_CLOI_ID = 'complex_id';
    const PROPERTY_FEEDBACK_ID = 'feedback_id';

    /**
     * Default properties of the content_object_feedback object, stored in an associative
     * array.
     */
    private $defaultProperties;

    function ContentObjectPubFeedback($peer_assessment_publication_id = 0, $cloi_id = 0, $feedback_id = 0, $defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_PEER_ASSESSMENT_PUBLICATION_ID, self :: PROPERTY_CLOI_ID, self :: PROPERTY_FEEDBACK_ID);
    }

    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    function get_peer_assessment_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PEER_ASSESSMENT_PUBLICATION_ID);
    }

    function get_cloi_id()
    {
        return $this->get_default_property(self :: PROPERTY_CLOI_ID);
    }

    function get_feedback_id()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK_ID);
    }

    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    function set_peer_assessment_publication_id($peer_assessment_publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PEER_ASSESSMENT_PUBLICATION_ID, $peer_assessment_publication_id);
    }

    function set_cloi_id($cloi_id)
    {
        return $this->set_default_property(self :: PROPERTY_CLOI_ID, $cloi_id);
    }

    function set_feedback_id($feedback_id)
    {
        return $this->set_default_property(self :: PROPERTY_FEEDBACK_ID, $feedback_id);
    }

    function delete()
    {
        return PeerAssessmentDataManager :: get_instance()->delete_peer_assessment_pub_feedback($this);
    }

    function create()
    {
        $wdm = PeerAssessmentDataManager :: get_instance();

        return $wdm->create_peer_assessment_pub_feedback($this);
    }

    function update()
    {
        $wdm = PeerAssessmentDataManager :: get_instance();
        $success = $wdm->update_peer_assessment_pub_feedback($this);
        if (! $success)
        {
            return false;
        }

        return true;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>
