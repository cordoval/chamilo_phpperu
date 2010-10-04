<?php

/**
 * Export Document objects. Write the attached document. Do not export Document's properties.
 *
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpDocumentExport extends CpObjectExport{

	public static function factory($settings){
		$object = $settings->get_object();
		if(self::accept($object)){
			return new self($settings);
		}else{
			return NULL;
		}
	}

	public static function accept($object){
		if(! $object instanceof ContentObject){
			return false;
		}
		return $object instanceof Document || $object->get_type() == Document::get_type_name();
	}

	public function export_content_object(){
		$settings = $this->get_settings();
		$doc = $settings->get_object();
		$full_path = $doc->get_full_path();
		if(empty($full_path)){
			return false;
		}
		$filename = $doc->get_filename();
		$parts = explode('.', $filename);
		$ext = count($parts)>1 ? '.'.end($parts) : '';
		$filename = str_replace($ext, '', $filename);
    	$id = $doc->get_id();
    	$id = '_' . str_pad($id, 8, '0', STR_PAD_LEFT);
		$href = str_safe($filename.$id.$ext);
		$directory = $settings->get_directory();
		if(! Filesystem::copy_file($full_path, $to_path = $directory.$href, true)){
			return false;
		}
		$this->add_manifest_entry($doc, $href);
		return $directory.$href;
	}

}


?>