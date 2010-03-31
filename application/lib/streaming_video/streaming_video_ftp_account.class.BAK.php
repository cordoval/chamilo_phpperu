<?php 
/**
 * streaming_video
 */

/**
 * This class describes a StreamingVideoFtpAccount data object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoFtpAccount extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * StreamingVideoFtpAccount properties
	 */

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array ();
	}

	function get_data_manager()
	{
		return StreamingVideoDataManager :: get_instance();
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>