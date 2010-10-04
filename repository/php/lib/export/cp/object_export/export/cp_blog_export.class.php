<?php

/**
 * Export Blog objects as a sub IMS CP package.
 *
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpBlogExport extends CpObjectExport{

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
		return $object instanceof Blog || $object->get_type() == Blog::get_type_name();
	}

	public function export_content_object(){
		$settings = $this->get_settings();
		$object = $settings->get_object();

		//$safe_name = str_safe($object->get_title()). '.blog';
    	$safe_name = $this->get_file_name($object, 'blog');
		$directory = $settings->get_directory(). "$safe_name/";

		$blog_manifest = new ImscpManifestWriter();
		$blog_manifest = $blog_manifest->add_manifest();
		$blog_toc = $blog_manifest->add_organizations()->add_organization();
		$blog_settings = new ObjectExportSettings($object, $directory, $blog_manifest, $blog_toc);

		$children = chamilo::retrieve_children($object);
		while($child = $children->next_result()){
			$child_object = Chamilo::retrieve_content_object($child->get_ref());
			$child_settings = $blog_settings->copy($child_object);
			$path = CpExport::object_factory($child_settings)->export_content_object();
		}
		$blog_manifest->save($directory . ImscpManifestWriter::MANIFEST_NAME);
		$this->add_manifest_entry($object, "$safe_name/");
		return $directory;
	}

}


?>