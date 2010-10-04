<?php

/**
 * Aggregate importer contained in the /dir/ directory.
 * Delegate importation to the first importer that accept the call.
 *
 * Importer contained in the /dir/ directory are -->directory<-- importer.
 * That is they implement various strategies to import files and sub folders contained in the dirctory pointed to.
 * Iterating over files contained in the directory is performed by the aggregated importer.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class DirCpImport extends CpObjectImportAggregate{

	public function __construct($parent = null){
		parent::__construct($parent);
		$directory = dirname(__FILE__) .'/dir/';
		$files = scandir($directory);
		$files = array_diff($files, array('.', '..'));
		foreach($files as $file){
			$path = $directory.$file;
			if(strpos($file, '.class.php') !== false){
				include_once($path);
				$class = str_replace('.class.php', '', $file);
				$class = Utilities::underscores_to_camelcase($class);
				$importer = new $class($this);
				$this->add($importer);
			}
		}
		$this->sort();
	}

	public function accept($settings){
		return is_dir($settings->get_path());
	}

	public function import(ObjectImportSettings $settings){
		if($this->accept($settings)){
			$items = $this->get_items();
			foreach($items as $item){
				if($result = $item->import($settings)){
					return $result;
				}
			}
			return false;
		}else{
			return false;
		}
	}
}







