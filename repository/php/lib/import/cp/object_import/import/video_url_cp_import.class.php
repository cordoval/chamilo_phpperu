<?php

/**
 * Import video URLs as video streaming objects.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class VideoUrlCpImport extends CpObjectImportBase{

	const DEFAULT_SIZE = 200;
	
	public static function factory($url){
		if(strpos($url, 'youtube') !== false){
			$result = new Youtube();
		}else if(strpos($url, 'vimeo') !== false){
			$result = new Vimeo();
		}else if(strpos($url, 'dailymotion')!== false){
			$result = new Dailymotion();
		}else{
			$result = NULL;
		}
		if($result){
			$result->set_height(self::DEFAULT_SIZE);
			$result->set_width(self::DEFAULT_SIZE);
			$result->set_url($url);
		}
		return $result;
	}

	public function get_extentions(){
		return array('video.url');
	}

	public function accept($settings){
		$path = $settings->get_path();
		$name = basename($path);
		$result = strpos($name, reset($this->get_extentions())) !== false;
		return $result;
	}

	protected function process_import(ObjectImportSettings $settings){
		if($content = file_get_contents($settings->get_path())){
			$url = $this->get_url($content);
			$result = self::factory($url);
			$this->save($settings, $result);
			return $result;
		}else{
			return false;
		}
	}

	protected function get_url($content){
		$lines = explode("\n", $content);
		foreach($lines as $line){
			$parts = explode('=', $line);
			if(count($parts)>1 && strtolower($parts[0]) == 'url'){
				return $parts[1];
			}
		}
		return '';
	}

}






?>