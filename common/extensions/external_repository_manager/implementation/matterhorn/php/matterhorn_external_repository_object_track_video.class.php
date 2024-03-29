<?php
namespace common\extensions\external_repository_manager\implementation\matterhorn;

use common\libraries\Translation;

class MatterhornExternalRepositoryObjectTrackVideo
{
	private $id;
	private $device;
	private $encoder;
	private $bitrate;
	private $framerate;
	private $resolution;
	
	
	public function get_id() {
		return $this->device;
	}

	public function set_id($id)
	{
		$this->id = $id;
	}
	
	
	/**
	 * @return the $device
	 */
	public function get_device() {
		return $this->device;
	}

	/**
	 * @return the $encoder
	 */
	public function get_encoder() {
		return $this->encoder;
	}

	/**
	 * @return the $bitrate
	 */
	public function get_bitrate() {
		return $this->bitrate;
	}

	/**
	 * @return the $framerate
	 */
	public function get_framerate() {
		return $this->framerate;
	}

	/**
	 * @return the $resolution
	 */
	public function get_resolution() {
		return $this->resolution;
	}

	/**
	 * @param $device the $device to set
	 */
	public function set_device($device) {
		$this->device = $device;
	}

	/**
	 * @param $encoder the $encoder to set
	 */
	public function set_encoder($encoder) {
		$this->encoder = $encoder;
	}

	/**
	 * @param $bitrate the $bitrate to set
	 */
	public function set_bitrate($bitrate) {
		$this->bitrate = $bitrate;
	}

	/**
	 * @param $framerate the $framerate to set
	 */
	public function set_framerate($framerate) {
		$this->framerate = $framerate;
	}

	/**
	 * @param $resolution the $resolution to set
	 */
	public function set_resolution($resolution) {
		$this->resolution = $resolution;
	}

	public function as_string()
	{
		$html = array();
		if ($this->get_device())
		{
			$html[] = $this->get_device();
		}
		$html[] = $this->get_encoder();
		$html[] = round($this->get_bitrate()/1000) . Translation :: get('kbps');
		$html[] = $this->get_framerate() . Translation :: get('fps');
		$html[] = $this->get_resolution();
		
		return implode (", ", $html); 
	}
	
	
}