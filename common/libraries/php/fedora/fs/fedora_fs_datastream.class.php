<?php
namespace common\libraries;

/**
 * Represent a datastream object.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_fs_datastream extends fedora_fs_base{

	public static function get_system_datastreams(){
		return array('DC', 'CHOR_DC', 'RELS-EXT', 'RELS-INT', 'AUDIT', 'THUMBNAIL');
	}

	protected $pid;
	protected $dsID;
	protected $source;
	protected $mime_type;

	public function __construct($pid, $dsID, $title, $mime_type, $source){
		$this->title = $title;
		$this->pid = $pid;
		$this->dsID = $dsID;
		$this->mime_type = $mime_type;
		$this->source = $source;
	}

	public function get_fsid(){
		return $this->get(__FUNCTION__, "o/{$this->$pid}.{$this->$dsID}");
	}

	public function get_mime_type(){
		return $this->mime_type;
	}

	public function get_extention(){
		return mimetype_to_ext($this->mime_type);
	}

	public function get_thumbnail(){
		$default = self::$resource_base . 'datastream.png';
		return $this->get(__FUNCTION__, $default);
	}

	public function get_title(){
		return $this->get(__FUNCTION__, '');
	}

	/**
	 * @return The html class to be used for this object
	 */
	public function get_class(){
		return $this->get(__FUNCTION__, 'fedora_datastream');
	}

	public function is_system(){
		return $this->is_system_datastream();
	}

	public function is_system_datastream(){
		$ds = $this->dsID;
		$datastreams = self::get_system_datastreams();
		foreach($datastreams as $datastream){
			if(strtolower($datastream) == strtolower($ds)){
				return true;
			}
		}
		return false;
	}

	public function format($path = array()){
		$ext = $this->get_extention();
		$ext = $ext ? '.' . $ext : '';
		$title = $this->get_title() . $ext;
		if($title){
			$result = array(
		        		'title' => $title,
						'shorttitle' => $title,
		        		'date'=> $this->get_date(),
		        		'size'=> $this->get_size(),
		        		'source'=> $this->get_source(),
		        		'thumbnail' => $this->get_thumbnail(),
			);
		}else{
			$result = array();
		}
		return $result;
	}

}

