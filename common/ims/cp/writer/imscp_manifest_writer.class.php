<?php

/**
 * Utility class used to generate IMS CP 1.1.4 Manifest XML schemas.
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class ImscpManifestWriter extends ImsXmlWriter{
	
	const MANIFEST_NAME = 'imsmanifest.xml';

	private $resources = null;
	private $organizations = null;
	
    function __construct($doc=null){
    	parent::__construct($doc);
    }
    
    public function get_format_name(){
    	return 'IMS CP';
    } 
    
    public function get_format_version(){
    	return '1.1.4';
    }
    
    /**
     * @return ImscpManifestWriter
     */
    public function get_manifest(){
    	$result = $this->get_root();
    	if(empty($result)){
    		$result = $this->add_manifest();
    	}else{
    		return $this->copy($result);
    	}
    	return $result;
    }
    
    /**
     * @return ImscpManifestWriter
     */
    public function get_resources(){
    	if(empty($this->resources)){
    		$this->resources = $this->get_manifest()->add_resources();
    	}
    	return $this->resources;
    }
    
    /**
     * @return ImscpManifestWriter
     */
   public function get_organizations(){
    	if(empty($this->organizations)){
    		$this->organizations = $this->add_organizations();
    	}
    	return $this->organizations;
    	
    }

    /**
     * @return string
     */
    public function get_identifier($item=null){
    	if(is_null($item))
    		return $this->get_identifier($this->get_current());
    		
    	if(is_string($item))
    		return $item;
    		
    	if($item instanceof DOMElement)
    		return $item->getAttribute('identifier');
    	
    	return $item->get_identifier();
    		
    }
        
    /**
     * The first, top-level <manifest> element in the Manifest encloses all the reference data. Subsequent occurrences of the <manifest> elements inside the top-level <manifest> are used to compartmentalize files, meta-data, and organization structure for aggregation, disaggregation, and reuse. The best-practice use of the IMS Content Packaging specification will result in each "learning object" or "atomic unit of learning" being placed within its own <manifest> element.
     * The top-level <manifest> occurs once and only once within the IMS Manifest file.
     * @param $identifier An identifier, provided by an author or authoring tool, that is unique within the Manifest. Data type = string;
     * @param $version Identifies the version of the Manifest. Is used to distinguish between manifests with the same identifier. Data type = string;
     * @param $base This provides a relative path offset for the content file(s). The usage of this element is defined in the XML Base Working Draft from the W3C. Data type = string.
     * @return ImscpManifestWriter
     */
    public function add_manifest($identifier = '', $version = '', $base = ''){
    	$result = $this->add_element('manifest');
    	$result->set_attribute('xmlns', 'http://www.imsglobal.org/xsd/imscp_v1p1');
    	$result->set_attribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    	$result->set_attribute('xmlns:imsmd', 'http://www.imsglobal.org/xsd/imsmd_v1p2');
    	$result->set_attribute('xmlns:imsqti', 'http://www.imsglobal.org/xsd/imsqti_v2p1');
    	$result->set_attribute('xmlns:lom', 'http://ltsc.ieee.org/xsd/LOM');
    	$result->set_attribute('xsi:schemaLocation', 'http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p1.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p4.xsd http://www.imsglobal.org/xsd/imsqti_v2p0 imsqti_v2p0.xsd');
    	$result->set_attribute('identifier', empty($identifier) ? $this->create_unique_id('MANIFEST_') : $identifier);
    	$result->set_attribute('version', $version, false);
    	$result->set_attribute('base', $base, false);
    	return $result;
    }

    /**
     * Describes zero, one, or more structures or organizations (i.e., <organization> elements) for this package.
     * Occurs once within a <manifest> element.
     * @param $default Identifies the default organization to use. Data type = idref.
     * @return ImscpManifestWriter
     */
	public function add_organizations($default = ''){
    	$result = $this->add_element('organizations');
    	$result->set_attribute('default', $this->get_identifier($default), false);
    	$this->organizations = $result;
    	return $result;
    }
    
    /**
     * This element identifies a collection of content files.
     * Occurs once and only once within a <manifest> element.
     * @param $base This provides a relative path offset for the content file(s). The usage of this element is defined in the XML Base Working Draft from the W3C. Data type = string.
     * @return ImscpManifestWriter
     */
	public function add_resources($base=''){
    	$result = $this->add_element('resources');
    	$result->set_attribute('xml:base', $base, false);
    	$this->resources = $result;
    	return $result;
    } 

    /**
     * This element contains meta-data that describes the resource. Implementers are free to choose from any of the meta-data elements defined in the IMS Meta-Data specification or to define their own meta-data schema.
     * Occurs zero or once within <organization>.
     * @param $schema Describes the schema used (e.g., IMS Content). If no schema element is present, it is assumed to be "IMS Content". Data type = string. Occurs zero or once within <metadata>.
     * @param $schema_version Describes version of the above schema (e.g., 1,0, 1.1). If no version is present, it is assumed to be "1.1". Data type = string. Occurs zero or once within <metadata>.
     * @return ImscpManifestWriter
     */
    public function add_metadata($schema = 'IMS Content', $schema_version = '1.1'){
    	$result = $this->add_element('metadata');
    	$result->add_element('schema', $schema, false);
    	$result->add_element('schemaversion', $schema_version, false);
    	
    	if(!empty($data)){
    		$data = $data instanceof DOMDocument ? $data->documentElement : $data;
    	
    		$data_node = $this->copy_node($data, $namespace, true);
    		$result->get_current()->appendChild($data_node);
    	}
    	return $result;
    }
    
    /**
     * This element describes a particular, passive organization of the material.
     * Occurs zero or more times within <organizations>.
     * @param string $structure Assumes a default value of "hierarchical", such as is common with a tree view or structural representation of data. Data type = string.
     * @param string $identifier An identifier, provided by an author or authoring tool, that is unique within the Manifest. Data type = id.
     * @return ImscpManifestWriter
     */
    public function add_organization($structure='hierarchical', $identifier=''){
    	$identifier = empty($identifier) ? $this->create_local_id('ORGANIZATION') : $identifier;
    	
    	$result = $this->add_element('organization');
    	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('structure', $structure, false);
    	return $result;
    }
    
    /**
     * This element describes the title of an <item>.
     * Occurs zero or more times within <item>.
     * @param string $title
     * @return ImscpManifestWriter
     */
    public function add_title($title){
    	return $this->add_element('title', $title);
    }
    
    /**
     * This element describes a node within a structure.
     * Occurs one or more times within <organization> and zero or more times within <item>.
     * @param string $identifierref A reference to a <resource> identifier (within the same package) or a sub-Manifest that is used to resolve the ultimate location of the file. If no identifierref is supplied, it is assumed that there is no content associated with this entry in the organization. Data type = string.
     * @param string $isvisible Indicates whether or not this resource is displayed when the unit of instruction is rendered. If not present, value is assumed to be 'true'. Data type = boolean.
     * @param string $parameters Static parameters to be passed to the content file at launch time. Data type = string.
     * @param string $identifier An identifier that is unique within the Manifest. Data type = id.
     * @return ImscpManifestWriter
     */
    public function add_item($identifierref='', $isvisible='', $parameters='', $identifier=''){
    	$identifier = empty($identifier) ? $this->create_local_id('ITEM') : $identifier;
    	
    	$result = $this->add_element('item');
    	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('identifierref', $this->get_identifier($identifierref), false);
    	$result->set_attribute('isvisible', $isvisible, false);
    	$result->set_attribute('parameters', $parameters, false);
    	return $result;
    }

    /**
     * This element describes a specific content file. 
     * Occurs zero or more times within <resources>.
     * @param string $type A string that identifies the type of resource. This specification defines the type "webcontent" plus reserved terms that are used to denote the packaging of content defined by other IMS specifications, including Learning Design. These labels are defined in Section 7 of the Implementation Handbook titled 'Using IMS Content Packaging to Package Instances of LIP and Other IMS Specifications' [IMSBUND, 01]. An IMS specification may extend the table in section 7 by using the syntax and including a normative statement to that effect in the specification.
     * @param string $href A reference to the "entry point" of this resource. External fully-qualified URIs are also permitted.
     * @param string $base This provides a relative path offset for the content file(s). The usage of this element is defined in the XML Base Working Draft from the W3C. Data type = string.
     * @param string $identifier An identifier, provided by the author or authoring tool, that is unique within the Manifest.
     * @return ImscpManifestWriter
     */
    public function add_resource($type='webcontent', $href='', $identifier='', $base=''){
    	$identifier = empty($identifier) ? $this->create_local_id('RESOURCE') : $identifier;
    	
    	$result = $this->add_element('resource');
    	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('type', $type);
    	$result->set_attribute('href', $href, false);
    	$result->set_attribute('xml:base', $base, false);
    	
    	return $result;
    }
    
    /**
     * Identifies one or more local files that this resource is dependent on. This includes the resource being referenced in the href attribute of <resource>. If the resource references an absolute URL (using href), <file> element(s) are not required.
     * Occurs zero or more times within <resource>.
     * @param string $href URL of the file.
     * @return ImscpManifestWriter
     */
    public function add_file($href){
    	$result = $this->add_element('file');
    	$result->set_attribute('href', $href);
    	return $result;
    }
    
    /**
     * This element identifies a single resource that can act as a container for multiple files that this resource depends upon. 
     * Occurs zero or more times within <resource>.
     * @param string $identifier_ref An identifier for other resources to reference.
     * @return ImscpManifestWriter
     */
    public function add_dependency($identifier_ref){
    	$result = $this->add_element('dependency');
    	$result->set_attribute('identifierref', $this->get_identifier($identifier_ref));
    	return $result;
    }
    
}







?>