<?php

/**
 * Import wiki html files as WikiPage objects.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class WikipageHtmlCpImport extends CpObjectImportBase{

	public function get_extentions(){
		return array('wikipage.html');
	}

	public function accept($settings){
		$path = $settings->get_path();
		$name = basename($path);
		$result = strpos($name, reset($this->get_extentions())) !== false;
		return $result;
	}

	protected function process_import(ObjectImportSettings $settings){
		$result = new WikiPage();
		$result->set_description($this->get_description($settings));
		$this->save($settings, $result);
		return $result;
	}

	protected function get_description(ObjectImportSettings $settings, $default = ''){
		if($doc = $settings->get_dom()){
			$list = $doc->getElementsByTagName('div');
			foreach($list as $div){
				if(strtolower($div->getAttribute('class')) == 'description'){
					$result = $this->get_innerhtml($div);
					return $result;
				}
			}
			$list = $doc->getElementsByTagName('body');
			if($body = $list->length>0 ? $list->item(0) : NULL){
				$body = $doc->saveXML($body);
				$body = str_replace('<body>', '', $body);
				$body = str_replace('</body>', '', $body);
			}else{
				$body = '';
			}
		}
		return $default;
	}
}






?>