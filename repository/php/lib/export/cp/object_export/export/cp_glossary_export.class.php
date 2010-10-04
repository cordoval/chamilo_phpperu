<?php

/**
 * Export Glossary objects as a sub IMS CP package.
 *
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpGlossaryExport extends CpObjectExport{

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
		return $object instanceof Glossary || $object->get_type() == Glossary::get_type_name();
	}

	public function export_content_object(){
		$settings = $this->get_settings();
		$object = $settings->get_object();

		//$safe_name = str_safe($object->get_title()). '.glossary';
    	$safe_name = $this->get_file_name($object, 'glossary');
		$directory = $settings->get_directory(). "$safe_name/";

		$glossary_manifest = new ImscpManifestWriter();
		$glossary_manifest = $glossary_manifest->add_manifest();
		$glossary_toc = $glossary_manifest->add_organizations()->add_organization();
		$glossary_settings = new ObjectExportSettings($object, $directory, $glossary_manifest, $glossary_toc);

		$children = chamilo::retrieve_children($object);
		while($child = $children->next_result()){
			$child_object = Chamilo::retrieve_content_object($child->get_ref());
			$child_settings = $glossary_settings->copy($child_object);
			$path = CpExport::object_factory($child_settings)->export_content_object();
		}
    	$glossary_manifest->save($directory . ImscpManifestWriter::MANIFEST_NAME);
		$this->add_manifest_entry($object, "$safe_name/");
		return $directory;
	}

}


?>