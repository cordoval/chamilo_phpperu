<?php
/*
 * @author jevdheyd
 */
class StreamingVideoClip extends ContentObject
{
    /*
     * transcoding states
     */
    const STATE_PUBLIC = 0;
    const STATE_QUEUED = 1;
    const STATE_TRANSCODING = 2;
    const STATE_ERRONEOUS = 3;

    const PROPERTY_ASPECT_RATIO = 'aspect_ratio';
    const PROPERTY_DURATION = 'duration';
    const PROPERTY_CONVERSION_STATE = 'conversion_state';

    static function get_additional_property_names()
    {
            return array(self :: PROPERTY_ASPECT_RATIO, self :: PROPERTY_CONVERSION_STATE, self :: PROPERTY_DURATION);
    }

    function get_aspect_ratio ()
    {
            return $this->get_additional_property(self :: PROPERTY_ASPECT_RATIO);
    }

    function set_aspect_ratio ($aspect_ratio)
    {
            return $this->set_additional_property(self :: PROPERTY_ASPECT_RATIO, $aspect_ratio);
    }

    function get_duration ()
    {
            return $this->get_additional_property(self :: PROPERTY_DURATION);
    }

    function set_duration ($duration)
    {
            return $this->set_additional_property(self :: PROPERTY_DURATION, $duration);
    }

    function get_conversion_state ()
    {
            return $this->get_additional_property(self :: PROPERTY_CONVERSION_STATE);
    }

    function set_conversion_state ($state)
    {
            return $this->set_additional_property(self :: PROPERTY_CONVERSION_STATE, $state);
    }

    function is_versionable()
    {
            return false;
    }

    function get_thumbnail_url() {
            //TODO: implement class StreamingVideoUtilities
            return ($this->get_conversion_state() == self :: STATE_PUBLIC
                    ? StreamingVideoUtilities::get_clip_thumbnail_url($this->export())
                    : null);
    }

    /*
    function get_cue_points() {
            $cp = array();
            $cond = new EqualityCondition(
                    VideoClipCuePoint::PROPERTY_PARENT_ID, $this->get_id());
            $children = RepositoryDataManager::get_instance()->retrieve_learning_objects(
                    'video_clip_cue_point',
                    $cond,
                    array(VideoClipCuePoint::PROPERTY_START_TIME),
                    array(SORT_ASC));
            while ($child = $children->next_result()) {
                    $cp[] = $child;
            }
            return $cp;
    }

    function delete() {
            if ($this->get_conversion_state() != Ovis_Clip::STATE_PUBLIC) {
                    $odm = Ovis_Data_Manager::get_instance();
                    $odm->cancel_transcoding($this->get_id());
            }
            parent::delete();
    }

    function export() {
            $id = $this->get_id();
            $state = $this->get_conversion_state();
            $author = $this->get_owner_id();
            $title = $this->get_title();
            $description = $this->get_description();
            $aspect_ratio = $this->get_aspect_ratio();
            $duration = $this->get_duration();
            $created = $this->get_creation_date();
            $modified = $this->get_modification_date();
            return new Ovis_Clip($id, $state, $author, $title, $description,
                    $aspect_ratio, $duration, $created, $modified);
    }

    static function import($ovis_clip) {
            $my_repository = self::get_user_repository_root(
                    $ovis_clip->get_author());
            $lo = new VideoClip();
            $lo->set_owner_id($ovis_clip->get_author());
            $lo->set_title($ovis_clip->get_title());
            $desc = $ovis_clip->get_description();
            $lo->set_description(!empty($desc)
                    ? $desc : htmlspecialchars($lo->get_title()));
            $lo->set_parent_id($my_repository);
            $lo->set_aspect_ratio($ovis_clip->get_aspect_ratio());
            $lo->set_duration($ovis_clip->get_duration());
            $lo->set_conversion_state($ovis_clip->get_state());
            return $lo;
    }

    // TODO: refactor this into some library
    private static function get_user_repository_root($user_id) {
            $dm = RepositoryDataManager :: get_instance();
            $condition = new EqualityCondition(
                    LearningObject :: PROPERTY_OWNER_ID,
                    $user_id);
            $objects = $dm->retrieve_learning_objects(
                    'category',
                    $condition,
                    array(LearningObject :: PROPERTY_PARENT_ID),
                    array(SORT_ASC));
            return $objects->next_result()->get_id();
    }*/
}
?>