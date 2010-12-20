<?php
namespace repository\content_object\bbb_meeting;

use repository\ContentObject;

use common\extensions\video_conferencing_manager\VideoConferencingManagerConnector;
use common\extensions\video_conferencing_manager\implementation\bbb\BbbVideoConferencingObject;
use common\extensions\video_conferencing_manager\implementation\bbb\BbbVideoConferencingManager;
use repository\ExternalRepository;
use repository\RepositoryDataManager;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Path;
use common\libraries\Theme;
use repository\ExternalInstance;
use repository\ContentObjectForm;
use repository\ExternalSync;
use admin\Registration;
use common\libraries\Utilities;

/**
 * $Id: bbb_meeting_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.soundcloud
 */

class BbbMeetingForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $conditions = array();
        $conditions[] = new EqualityCondition(ExternalInstance :: PROPERTY_INSTANCE_TYPE, Registration :: TYPE_VIDEO_CONFERENCING_MANAGER);
        $conditions[] = new EqualityCondition(ExternalInstance :: PROPERTY_TYPE, BbbVideoConferencingManager :: VIDEO_CONFERENCING_TYPE);
        $condtion = new AndCondition($conditions);
        $instances = RepositoryDataManager :: get_instance()->retrieve_external_instances($condition);

        while ($instance = $instances->next_result())
        {
            $option[$instance->get_id()] = $instance->get_title();
        }
        $this->addElement('category', Translation :: get('Properties'));
        $this->addElement('select', ExternalSync :: PROPERTY_EXTERNAL_ID, Translation :: get('Server'), $option);
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_creation_form();
    }

    function setDefaults($defaults = array ())
    {
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $values = $this->exportValues();
        $instance = RepositoryDataManager :: get_instance()->retrieve_external_instance($values[ExternalSync :: PROPERTY_EXTERNAL_ID]);

        $connector = VideoConferencingManagerConnector :: factory($instance);
        $video_conferencing_object = new BbbVideoConferencingObject();
        $video_conferencing_object->set_title($values[ContentObject :: PROPERTY_TITLE]);

        $bbb_meeting = $connector->create_video_conferencing_object($video_conferencing_object);

        if ($bbb_meeting instanceof BbbMeeting)
        {
            $bbb_meeting->set_description($values[ContentObject :: PROPERTY_DESCRIPTION]);
            if (! $bbb_meeting->update())
            {
                return false;
            }
            else
            {
                $this->set_content_object($bbb_meeting);

                return $bbb_meeting;
            }
        }
        else
        {
            return false;
        }
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        return parent :: update_content_object();
    }
}
?>