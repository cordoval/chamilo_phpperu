<?php 
/**
 * streaming_video
 */

/**
 * This class describes a StreamingVideoTranscoding data object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class Transcoding extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * StreamingVideoTranscoding properties
	 */
	const PROPERTY_CLIP_ID = 'clip_id';
	const PROPERTY_SOURCE_FILE = 'source_file';
	const PROPERTY_START_TIME = 'start_time';
	const PROPERTY_END_TIME = 'end_time';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_CLIP_ID, self :: PROPERTY_SOURCE_FILE, self :: PROPERTY_START_TIME, self :: PROPERTY_END_TIME);
	}

	function get_data_manager()
	{
		return StreamingVideoDataManager :: get_instance();
	}

	/**
	 * Returns the clip_id of this StreamingVideoTranscoding.
	 * @return the clip_id.
	 */
	function get_clip_id()
	{
		return $this->get_default_property(self :: PROPERTY_CLIP_ID);
	}

	/**
	 * Sets the clip_id of this StreamingVideoTranscoding.
	 * @param clip_id
	 */
	function set_clip_id($clip_id)
	{
		$this->set_default_property(self :: PROPERTY_CLIP_ID, $clip_id);
	}

	/**
	 * Returns the source_file of this StreamingVideoTranscoding.
	 * @return the source_file.
	 */
	function get_source_file()
	{
		return $this->get_default_property(self :: PROPERTY_SOURCE_FILE);
	}

	/**
	 * Sets the source_file of this StreamingVideoTranscoding.
	 * @param source_file
	 */
	function set_source_file($source_file)
	{
		$this->set_default_property(self :: PROPERTY_SOURCE_FILE, $source_file);
	}

	/**
	 * Returns the start_time of this StreamingVideoTranscoding.
	 * @return the start_time.
	 */
	function get_start_time()
	{
		return $this->get_default_property(self :: PROPERTY_START_TIME);
	}

	/**
	 * Sets the start_time of this StreamingVideoTranscoding.
	 * @param start_time
	 */
	function set_start_time($start_time)
	{
		$this->set_default_property(self :: PROPERTY_START_TIME, $start_time);
	}

	/**
	 * Returns the end_time of this StreamingVideoTranscoding.
	 * @return the end_time.
	 */
	function get_end_time()
	{
		return $this->get_default_property(self :: PROPERTY_END_TIME);
	}

	/**
	 * Sets the end_time of this StreamingVideoTranscoding.
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