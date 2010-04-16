<?php 
/**
 * ovis
 */

/**
 * This class describes a Profile data object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */

class TranscodingProfile extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Profile properties
	 */
	const PROPERTY_POSITION = 'position';
	const PROPERTY_NAME = 'name';
	const PROPERTY_AUDIO_QUALITY = 'audio_quality';
	const PROPERTY_VIDEO_QUALITY = 'video_quality';
	const PROPERTY_CHANNELS = 'channels';
	const PROPERTY_WIDTH = 'width';
	const PROPERTY_HEIGHT = 'height';
	const PROPERTY_END_TIME = 'end_time';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_POSITION, self :: PROPERTY_NAME, self :: PROPERTY_AUDIO_QUALITY, self :: PROPERTY_VIDEO_QUALITY, self :: PROPERTY_CHANNELS, self :: PROPERTY_WIDTH, self :: PROPERTY_HEIGHT, self :: PROPERTY_END_TIME);
	}

	function get_data_manager()
	{
		return OvisDataManager :: get_instance();
	}

	/**
	 * Returns the position of this Profile.
	 * @return the position.
	 */
	function get_position()
	{
		return $this->get_default_property(self :: PROPERTY_POSITION);
	}

	/**
	 * Sets the position of this Profile.
	 * @param position
	 */
	function set_position($position)
	{
		$this->set_default_property(self :: PROPERTY_POSITION, $position);
	}

	/**
	 * Returns the name of this Profile.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this Profile.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	/**
	 * Returns the audio_quality of this Profile.
	 * @return the audio_quality.
	 */
	function get_audio_quality()
	{
		return $this->get_default_property(self :: PROPERTY_AUDIO_QUALITY);
	}

	/**
	 * Sets the audio_quality of this Profile.
	 * @param audio_quality
	 */
	function set_audio_quality($audio_quality)
	{
		$this->set_default_property(self :: PROPERTY_AUDIO_QUALITY, $audio_quality);
	}

	/**
	 * Returns the video_quality of this Profile.
	 * @return the video_quality.
	 */
	function get_video_quality()
	{
		return $this->get_default_property(self :: PROPERTY_VIDEO_QUALITY);
	}

	/**
	 * Sets the video_quality of this Profile.
	 * @param video_quality
	 */
	function set_video_quality($video_quality)
	{
		$this->set_default_property(self :: PROPERTY_VIDEO_QUALITY, $video_quality);
	}

	/**
	 * Returns the channels of this Profile.
	 * @return the channels.
	 */
	function get_channels()
	{
		return $this->get_default_property(self :: PROPERTY_CHANNELS);
	}

	/**
	 * Sets the channels of this Profile.
	 * @param channels
	 */
	function set_channels($channels)
	{
		$this->set_default_property(self :: PROPERTY_CHANNELS, $channels);
	}

	/**
	 * Returns the width of this Profile.
	 * @return the width.
	 */
	function get_width()
	{
		return $this->get_default_property(self :: PROPERTY_WIDTH);
	}

	/**
	 * Sets the width of this Profile.
	 * @param width
	 */
	function set_width($width)
	{
		$this->set_default_property(self :: PROPERTY_WIDTH, $width);
	}

	/**
	 * Returns the height of this Profile.
	 * @return the height.
	 */
	function get_height()
	{
		return $this->get_default_property(self :: PROPERTY_HEIGHT);
	}

	/**
	 * Sets the height of this Profile.
	 * @param height
	 */
	function set_height($height)
	{
		$this->set_default_property(self :: PROPERTY_HEIGHT, $height);
	}

	/**
	 * Returns the end_time of this Profile.
	 * @return the end_time.
	 */
	function get_end_time()
	{
		return $this->get_default_property(self :: PROPERTY_END_TIME);
	}

	/**
	 * Sets the end_time of this Profile.
	 * @param end_time
	 */
	function set_end_time($end_time)
	{
		$this->set_default_property(self :: PROPERTY_END_TIME, $end_time);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>