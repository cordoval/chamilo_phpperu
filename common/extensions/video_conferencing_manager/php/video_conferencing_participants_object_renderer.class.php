<?php
namespace common\extensions\video_conferencing_manager;
abstract class VideoConferencingParticipantsObjectRenderer
{
    const TYPE_TABLE = 'table';
    const TYPE_GALLERY = 'gallery_table';
    const TYPE_SLIDESHOW = 'slideshow';

    protected $video_conferencing_browser;

    function __construct($video_conferencing_participants_browser)
    {
        $this->video_conferencing_participants_browser = $video_conferencing_participants_browser;
    }

    function get_video_conferencing_participants_browser()
    {
        return $this->video_conferencing_participants_browser;
    }

    static function factory($type, $video_conferencing_participants_browser)
    {
        $file = dirname(__FILE__) . '/renderer/' . $type . '_video_conferencing_participants_object_renderer.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('VideoConferencingParticipantsObjectRendererTypeDoesNotExist', array('type' => $type)));
        }

        require_once $file;

        $class = Utilities :: underscores_to_camelcase($type) . 'VideoConferencingParticipantsObjectRenderer';
        return new $class($video_conferencing_participants_browser);
    }

    abstract function as_html();

    public function get_parameters()
    {
        return $this->get_video_conferencing_participants_browser()->get_parameters();
    }

    public function get_condition()
    {
        return $this->get_video_conferencing_participants_browser()->get_condition();
    }

    function count_video_conferencing_participants_objects($condition)
    {
        return $this->get_video_conferencing_participants_browser()->count_video_conferencing_participants_objects($condition);
    }

    function retrieve_video_conferencing_participants_objects($condition, $order_property, $offset, $count)
    {
        return $this->get_video_conferencing_browser()->retrieve_video_conferencing_participants_objects($condition, $order_property, $offset, $count);
    }

	function get_video_conferencing_participants_object_actions(VideoConferencingObject $object)
	{
	    return $this->get_video_conferencing_browser()->get_video_conferencing_participants_object_actions($object);
	}

    function is_stand_alone()
    {
        return $this->get_video_conferencing_participants_browser()->get_parent()->is_stand_alone();
    }

    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->get_video_conferencing_participants_browser()->get_url($parameters, $filter, $encode_entities);
    }

    function get_video_conferencing_participants_object_viewing_url($object)
    {
        return $this->get_video_conferencing_participants_browser()->get_video_conferencing_participants_object_viewing_url($object);
    }
}
?>