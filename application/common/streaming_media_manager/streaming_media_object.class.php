<?php
class StreamingMediaObject
{
	private $title;
	private $id;
	private $description;
	private $url;
	private $duration;
	private $thumbnail;

	function StreamingMediaObject($id, $title,$description, $url, $duration, $thumbnail)
	{
		$this->id = $id;
		$this->title = $title;
		$this->description = $description;
		$this->url = $url;
		$this->duration = $duration;
		$this->thumbnail = $thumbnail;
	}

	/**
     * @return the $title
     */
    /**
     * @return the $thumbnail
     */
    public function get_thumbnail()
    {
        return $this->thumbnail;
    }

	/**
     * @param $thumbnail the $thumbnail to set
     */
    public function set_thumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

	public function get_title()
    {
        return $this->title;
    }

	/**
     * @return the $id
     */
    public function get_id()
    {
        return $this->id;
    }

	/**
     * @return the $description
     */
    public function get_description()
    {
        return $this->description;
    }

	/**
     * @return the $url
     */
    public function get_url()
    {
        return $this->url;
    }

	/**
     * @return the $duration
     */
    public function get_duration()
    {
        return $this->duration;
    }

	/**
     * @param $title the $title to set
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

	/**
     * @param $id the $id to set
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

	/**
     * @param $description the $description to set
     */
    public function set_description($description)
    {
        $this->description = $description;
    }

	/**
     * @param $url the $url to set
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

	/**
     * @param $duration the $duration to set
     */
    public function set_duration($duration)
    {
        $this->duration = $duration;
    }
}
?>