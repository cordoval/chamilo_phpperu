<?php

/**
 * Export Wiki objects as a sub IMS CP package.
 *
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpWikiExport extends CpObjectExport{

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
		return $object instanceof Wiki || $object->get_type() == Wiki::get_type_name();
	}

	public function export_content_object(){
		$settings = $this->get_settings();
		$object = $settings->get_object();

		//$safe_name = str_safe($object->get_title()). '.wiki';
		$safe_name = $this->get_file_name($object, 'wiki');
		$directory = $settings->get_directory(). "$safe_name/";

		$wiki_manifest = new ImscpManifestWriter();
		$wiki_manifest = $wiki_manifest->add_manifest();
		$wiki_toc = $wiki_manifest->add_organizations()->add_organization();
		$wiki_settings = new ObjectExportSettings($object, $directory, $wiki_manifest, $wiki_toc);

		$children = chamilo::retrieve_children($object);
		while($child = $children->next_result()){
			$child_object = Chamilo::retrieve_content_object($child->get_ref());
			$child_settings = $wiki_settings->copy($child_object);
			$path = CpExport::object_factory($child_settings)->export_content_object();
		}
		$wiki_manifest->save($directory . ImscpManifestWriter::MANIFEST_NAME);
		$this->add_manifest_entry($object, "$safe_name/");
		return $directory;
	}

}


?>