<?php
class MatterhornExternalRepositoryObjectTrack
{
    private $id;
    private $ref;
    private $type;
    private $mimetype;
    private $tags;
    private $url;
    private $checksum;
    private $duration;
    private $audio;
    private $video;

    /**
     * @return the $mimetype
     */
    /**
     * @return the $id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @return the $ref
     */
    public function get_ref()
    {
        return $this->ref;
    }

    /**
     * @return the $type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * @param $id the $id to set
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * @param $ref the $ref to set
     */
    public function set_ref($ref)
    {
        $this->ref = $ref;
    }

    /**
     * @param $type the $type to set
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    public function get_mimetype()
    {
        return $this->mimetype;
    }

    /**
     * @return the $tags
     */
    public function get_tags()
    {
        return $this->tags;
    }

    /**
     * @return the $url
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     * @return the $checksum
     */
    public function get_checksum()
    {
        return $this->checksum;
    }

    /**
     * @return the $duration
     */
    public function get_duration()
    {
        return $this->duration;
    }

    /**
     * @return the $audio
     */
    public function get_audio()
    {
        return $this->audio;
    }

    /**
     * @return the $video
     */
    public function get_video()
    {
        return $this->video;
    }

    /**
     * @param $mimetype the $mimetype to set
     */
    public function set_mimetype($mimetype)
    {
        $this->mimetype = $mimetype;
    }

    /**
     * @param $tags the $tags to set
     */
    public function set_tags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param $url the $url to set
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    /**
     * @param $checksum the $checksum to set
     */
    public function set_checksum($checksum)
    {
        $this->checksum = $checksum;
    }

    /**
     * @param $duration the $duration to set
     */
    public function set_duration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @param $audio the $audio to set
     */
    public function set_audio($audio)
    {
        $this->audio = $audio;
    }

    /**
     * @param $video the $video to set
     */
    public function set_video($video)
    {
        $this->video = $video;
    }

    public function as_string()
    {
        $html = array();

        $html[] = '<table class="no_border"><tr><td style="width: 22px;">';
        $html[] = Utilities :: mimetype_to_image($this->get_mimetype());
        $html[] = '</td><td>';
        if ($this->get_video())
        {
            $html[] = '<b>' . Translation :: get('Video') . ':</b> ' . $this->get_video()->as_string();
            $html[] = '<br/>';
        }
        if ($this->get_audio())
        {
            $html[] = '<b>' . Translation :: get('Audio') . ':</b> ' . $this->get_audio()->as_string();
        }
        $html[] = '</td></tr></table>';
        return implode("", $html);
    }

}
