<?php

require_once  dirname(__FILE__) .'/xml_writer_base.class.php';

/**
 * Utility class used to generate FOXML XML schemas.
 * Basic implementation.
 *
 * http://fedora-commons.org/definitions/1/0/foxml1-1.xsd
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class FoxmlWriter extends XmlWriterBase{

	function __construct($writer=null, $prefix = ''){
		parent::__construct($writer, $prefix);
	}

	public function get_format_name(){
		return 'FOXML';
	}

	public function get_format_version(){
		return 'FOXML 1.1';
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_digitalObject($pid=0, $version = '1.1'){
		$result = $this->add_element('foxml:digitalObject');
		$result->set_attribute('xmlns:foxml', 'info:fedora/fedora-system:def/foxml#');
		$result->set_attribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$result->set_attribute('xsi:schemaLocation', 'info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd');
		$result->set_attribute('VERSION', $version, false);
		$result->set_attribute('PID', $pid, false);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_objectProperties(){
		$result = $this->add_element('foxml:objectProperties');
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_property($name, $value, $write_empty = true){
		if($write_empty == false && empty($value)){
			return;
		}
		$result = $this->add_element('foxml:property');
		$result->set_attribute('NAME', $name);
		$result->set_attribute('VALUE', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_label($value){
		return $this->add_property('info:fedora/fedora-system:def/model#label', $value);
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_state($value){
		return $this->add_property('info:fedora/fedora-system:def/model#state', $value);
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_ownerId($value){
		return $this->add_property('info:fedora/fedora-system:def/model#ownerId', $value);
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_createdDate($value){
		return $this->add_property('info:fedora/fedora-system:def/model#createdDate', self::format_datetime($value));
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_lastModifiedDate($value){
		return $this->add_property('info:fedora/fedora-system:def/view#lastModifiedDate', self::format_datetime($value));
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_datastream($id, $state = 'A', $control_group = 'X', $versionable = false){
		$result = $this->add_element('foxml:datastream');
		$result->set_attribute('ID', $id);
		$result->set_attribute('STATE', $state);
		$result->set_attribute('CONTROL_GROUP', $control_group);
		$result->set_attribute('VERSIONABLE', $versionable?'true':'false');
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_datastreamVersion($id, $label, $created, $mimetype, $format_uri='', $size=0){
		$result = $this->add_element('foxml:datastreamVersion');
		$result->set_attribute('ID', $id);
		$result->set_attribute('LABEL', $label);
		$result->set_attribute('CREATED', self::format_datetime($created));
		$result->set_attribute('MIMETYPE', $mimetype);
		$result->set_attribute('FORMAT_URI', $format_uri, false);
		$result->set_attribute('SIZE', $size, false);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_xmlContent(){
		$result = $this->add_element('foxml:xmlContent');
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc(){
		$result = $this->add_element('oai_dc:dc');
		$result->set_attribute('xmlns:oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
		$result->set_attribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
		$result->set_attribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$result->set_attribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd');
		return $result;
	}

	// -- SWITCH DC -- SWITCH DC -- SWITCH DC -- SWITCH DC -- SWITCH DC --

	/**
	 * @return FoxmlWriter
	 */
	public function add_switch_dc($add_xml_content=true){
		if($add_xml_content){
			$result = $this->add_xmlContent();
			$result = $result->add_element('metadata');
		}else{
			$result = $this->add_element('metadata');
		}
		$result->set_attribute('xmlns:dcterms', 'http://purl.org/dc/terms/');
		$result->set_attribute('xmlns:chor_dc', 'http://purl.org/switch/chor/');
		$result->set_attribute('xmlns:chor_dcterms', 'http://purl.org/switch/terms/');
		$result->set_attribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$result->set_attribute('xsi:schemaLocation', 'http://collection.switch.ch/ http://collection.switch.ch/spec/2008/chor_dcterms.xsd');
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_term($name, $value){
		$result = $this->add_element('dcterms:' . $name, $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_title($value){
		if(empty($value)){
			return;
		}
		return $this->add_element('dcterms:title', $value);
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_aaid($value){
		if(empty($value)){
			return;
		}
		return $this->add_element('chor_dcterms:aaiid', $value);
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_creator($value){
		if(empty($value)){
			return;
		}
		if(is_array($value)){
			foreach($value as $item){
				$this->add_dc_creator($item);
			}
			return;
		}

		return $this->add_element('dcterms:creator', $value);
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_aaiid($value){
		if(empty($value)){
			return;
		}
		return $this->add_element('chor_dcterms:aaiid', $value);
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_accessRights($access, $value = ''){
		$value = $value ? $value : $access;
		$result = $this->add_element('dcterms:accessRights', $value);
		$result->set_attribute('chor_dcterms:access', $access);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_rights($access, $value = ''){
		$value = $value ? $value : $access;
		$result = $this->add_element('dcterms:rights', $value);
		$result->set_attribute('chor_dcterms:access', $access);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_license($value){
		if(empty($value)){
			return;
		}
		$result = $this->add_element('dcterms:license', $value);
		$result->set_attribute('xsi:type', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_discipline($value){
		if(empty($value)){
			return;
		}
		if(is_array($value)){
			foreach($value as $item){
				call_user_func(array($this, __FUNCTION__), $item);
			}
			return;
		}

		$result = $this->add_element('dcterms:subject', $value);
		$result->set_attribute('chor_dcterms:discipline', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_contributor($value){
		if(empty($value)){
			return;
		}
		if(is_array($value)){
			foreach($value as $item){
				call_user_func(array($this, __FUNCTION__), $item);
			}
			return;
		}

		return $this->add_element('dcterms:contributor', $value);
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_publisher($value){
		if(empty($value)){
			return;
		}
		if(is_array($value)){
			foreach($value as $item){
				call_user_func(array($this, __FUNCTION__), $item);
			}
			return;
		}

		return $this->add_element('dcterms:publisher', $value);
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_rightsHolder($value){
		if(empty($value)){
			return;
		}
		return $this->add_element('dcterms:rightsHolder', $value);
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_tableOfContents($value){
		if(empty($value)){
			return;
		}
		return $this->add_element('dcterms:tableOfContents', $value);
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_abstract($value){
		if(empty($value)){
			return;
		}
		$result = $this->add_element('dcterms:abstract', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_subject($value){
		if(empty($value)){
			return;
		}
		if(is_array($value)){
			foreach($value as $item){
				call_user_func(array($this, __FUNCTION__), $item);
			}
			return;
		}

		$result = $this->add_element('dcterms:subject', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_description($value){
		if(empty($value)){
			return;
		}
		if(is_array($value)){
			foreach($value as $item){
				call_user_func(array($this, __FUNCTION__), $item);
			}
			return;
		}

		$result = $this->add_element('dcterms:description', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_language($value){
		if(empty($value)){
			return;
		}
		if(is_array($value)){
			foreach($value as $item){
				call_user_func(array($this, __FUNCTION__), $item);
			}
			return;
		}

		$result = $this->add_element('dcterms:language');
		$result->set_attribute('xsi:type', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_educationLevel($value){
		if(empty($value)){
			return;
		}
		if(is_array($value)){
			foreach($value as $item){
				call_user_func(array($this, __FUNCTION__), $item);
			}
			return;
		}

		$result = $this->add_element('dcterms:educationLevel', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_instructionalMethod($value){
		if(empty($value)){
			return;
		}
		if(is_array($value)){
			foreach($value as $item){
				call_user_func(array($this, __FUNCTION__), $item);
			}
			return;
		}

		$result = $this->add_element('dcterms:instructionalMethod', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_issued($value){
		if(empty($value)){
			return;
		}
		$result = $this->add_element('dcterms:issued', $value);
		$result->set_attribute('xsi:type', 'dcterms:W3CDTF');
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_alternative($value){
		if(empty($value)){
			return;
		}
		if(is_array($value)){
			foreach($value as $item){
				call_user_func(array($this, __FUNCTION__), $item);
			}
			return;
		}

		$result = $this->add_element('dcterms:alternative', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_type($value){
		if(empty($value)){
			return;
		}
		$result = $this->add_element('dcterms:type', $value);
		$result->set_attribute('xsi:type', 'dcterms:DCMIType');
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_extent($value){
		if(empty($value)){
			return;
		}
		if(is_array($value)){
			foreach($value as $item){
				call_user_func(array($this, __FUNCTION__), $item);
			}
			return;
		}

		$result = $this->add_element('dcterms:extent', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_source($value){
		if(empty($value)){
			return;
		}
		$result = $this->add_element('dcterms:source', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_dc_identifier($value){
		if(empty($value)){
			return;
		}
		$result = $this->add_element('dcterms:identifier', $value);
		return $result;
	}

	// -- RELS-EXT/INT -- RELS-EXT/INT -- RELS-EXT/INT -- RELS-EXT/INT -- RELS-EXT/INT -- RELS-EXT/INT --

	/**
	 * @return FoxmlWriter
	 */
	public function add_rels_ext($add_xml_content = true){
		if($add_xml_content){
			$result = $this->add_xmlContent();
			$result = $result->add_element('rdf:RDF');
		}else{
			$result = $this->add_element('rdf:RDF');
		}
		$result->set_attribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
		$result->set_attribute('xmlns:rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
		$result->set_attribute('xmlns:rel', 'info:fedora/fedora-system:def/relations-external#');
		$result->set_attribute('xmlns:fedora-model', 'info:fedora/fedora-system:def/model#');
		$result->set_attribute('xmlns:oai', 'http://www.openarchives.org/OAI/2.0/');
		$result->set_attribute('xmlns:dcterms', 'http://purl.org/dc/terms/');
		$result->set_attribute('xmlns:chor_dc', 'http://purl.org/switch/chor/');
		$result->set_attribute('xmlns:chor_dcterms', 'http://purl.org/switch/terms/');
		$result->set_attribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_rels_int(){
		$result = $this->add_xmlContent();
		$result = $result->add_element('rdf:RDF');
		$result->set_attribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
		$result->set_attribute('xmlns:rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
		$result->set_attribute('xmlns:rel', 'info:fedora/fedora-system:def/relations-external#');
		$result->set_attribute('xmlns:fedora-model', 'info:fedora/fedora-system:def/model#');
		$result->set_attribute('xmlns:oai', 'http://www.openarchives.org/OAI/2.0/');
		$result->set_attribute('xmlns:dcterms', 'http://purl.org/dc/terms/');
		$result->set_attribute('xmlns:chor_dc', 'http://purl.org/switch/chor/');
		$result->set_attribute('xmlns:chor_dcterms', 'http://purl.org/switch/terms/');
		$result->set_attribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		return $result;
	}


	/**
	 * @return FoxmlWriter
	 */
	public function add_rel_description($id){
		$result = $this->add_element('rdf:Description');
		$result->set_attribute('rdf:about', $id, false);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_rel_isMemberOfCollection($id){
		$result = $this->add_element('rel:isMemberOfCollection');
		$result->set_attribute('rdf:resource', $id);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_rel_isCollection($value){
		if($value){
			$result = $this->add_element('rel:isCollection', 'true');
		}else{
			$result = $this;
		}
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_rel_conformsTo($id){
		//@todo:check if this is correct
		$result = $this->add_element('rel:conformsTo');
		$result->set_attribute('rdf:resource', $id);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_hasModel($resource){
		$result = $this->add_element('fedora-model:hasModel');
		$result->set_attribute('rdf:resource', $resource);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_oai_itemID($resource){
		$result = $this->add_element('oai:itemID', $resource);
		return $result;
	}

	// -- RELS-EXT -- RELS-EXT -- RELS-EXT -- RELS-EXT -- RELS-EXT -- RELS-EXT --

	/**
	 * @return FoxmlWriter
	 */
	public function add_identifier($value){
		$result = $this->add_element('dc:identifier', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_auditTrail(){
		$result = $this->add_element('audit:auditTrail');
		$result->set_attribute('xmlns:audit', 'info:fedora/fedora-system:def/audit#');
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_record($id){
		$result = $this->add_element('audit:record');
		$result->set_attribute('ID', $id);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_process($type){
		$result = $this->add_element('audit:process');
		$result->set_attribute('TYPE', $type);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_action($value){
		$result = $this->add_element('audit:action', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_componentID($value){
		$result = $this->add_element('audit:componentID', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_responsibility($value){
		$result = $this->add_element('audit:responsibility', $value);
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_date($value){
		$result = $this->add_element('audit:date', self::format_datetime($value));
		return $result;
	}

	/**
	 * @return FoxmlWriter
	 */
	public function add_binaryContent($value){
		$result = $this->add_element('foxml:binaryContent', base64_encode($value));
		return $result;
	}
}






















?>