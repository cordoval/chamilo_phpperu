<?php

/**
 * Import an hotpotatoes file as a hotpotatoes object.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class HotpotatoesCpImport extends CpObjectImportBase{

	public function get_extentions(){
		return array('jqz', 'jmx', 'jcl', 'jmt', 'jcw', 'jms', 'html', 'htm', 'zip');
	}

	public function accept(ObjectImportSettings $settings){
		$ext = $settings->get_extention();
		$type = $settings->get_type();

		return ($type == 'hotpotatoes' && ($ext == 'html' || $ext == 'htm' || $ext == 'zip')) || ($ext == 'jqz' || $ext == 'jmx' || $ext == 'jcl' || $ext == 'jmt' || $ext == 'jcw' || $ext == 'jms');
	}

	protected function process_import(ObjectImportSettings $settings){
		$path = $settings->get_path();
			
		//for whatever reason unzipping replaces __ , ___ , ____ , ... with _
		//@todo: fix unzipping instead of compensating here
		if(!file_exists($path)){
			$path = str_replace('__', '_', $path); //handles __
			$path = str_replace('__', '_', $path); //handles ___
			$path = str_replace('__', '_', $path); //...
			$path = str_replace('__', '_', $path);
		}
			
		$filename = $settings->get_filename();
		$ext = $settings->get_extention();
		$result = new Hotpotatoes();
		$result->set_maximum_attempts(1);
		$result->set_description($filename);
		$href = $this->store_file($settings);
		$result->set_path($href);
		$this->save($settings, $result);
		return $result;
	}


	/**
	 *
	 * Copy the file to the hotpotatoes standard storing location.
	 *
	 * @param ObjectImportSettings $settings
	 */
	protected function store_file(ObjectImportSettings $settings){
		$source = $settings->get_path();
		$ext = $settings->get_extention();
		if($ext == 'zip'){
			$destination = $this->store_zip_file($settings);
		}else{
			$filename = $settings->get_filename();
			$ext = $settings->get_extention();
			$owner = $settings->get_user()->get_id();
			$destination_dir = Path::get(SYS_HOTPOTATOES_PATH).$owner;
			$destination = $destination_dir . '/'. Filesystem::create_unique_name($destination_dir, $filename.'.'.$ext);
			if(Filesystem::move_file($source, $destination)){
				chmod($destination, 0777);
			}else{
				$destination = '';
			}
				
		}
		return $destination;
	}

	/**
	 *
	 * Copy the html file contained in the zip to the hotpotatoes storing location.
	 *
	 * @param ObjectImportSettings $settings
	 */
	protected function store_zip_file(ObjectImportSettings $settings){
		$path = $settings->get_path();
		$dir = $this->extract($path);
		$files = scandir($dir);
		$files = array_diff($files, array('.', '..'));
		$source = $filename = $ext = '' ;
		foreach($files as $file){
			$parts = explode('.', $file);
			$ext = count($parts)>1 ? end($parts) : '';
			if($ext == 'html' || $ext == 'htm'){
				$source = $dir.$file;
				$filename = $file;
				break;
			}
		}
		if($source){
			$owner = $settings->get_user()->get_id();
			$destination_dir = Path::get(SYS_HOTPOTATOES_PATH).$owner;
			$destination = $destination_dir . '/'. Filesystem::create_unique_name($destination_dir, $filename);
			Filesystem::move_file($source, $destination);
			chmod($destination, 0777);
		}else{
			$destination = '';
		}
		Filesystem::remove($dir);
		return $destination;
	}


}







