<?php

/**
 * Import zip files. Extract the file and call the root importer on the extracted folder.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class ZipCpImport extends CpObjectImportBase{

	public function get_weight(){
		return 100;
	}

	protected function process_import(ObjectImportSettings $settings){
		$path = $settings->get_path();
		$filename = $settings->get_filename();
		$filename = str_replace('.zip', '', $filename);
		
		$dir = $this->extract($path, true);
		
		$folder_settings = $settings->copy($dir, $filename);
		$result = $this->get_root()->import($folder_settings);
		Filesystem::remove($dir);
		return $result;
	}
}