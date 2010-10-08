<?php

require_once  dirname(__FILE__) . '/../ims_id_factory.class.php';

/**
 * Base class for XML writers. Helper class create XML file.
 * Magic methods:
 * 
 * 	- add_tagname()		add an element with tagname
 * 
 * Magic property
 * 
 * 	- attributename		returns/set the attribute value
 * 
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class ImsXmlWriter
{
	public static function format_datetime($timestamp){
		if(empty($timestamp)){
			return $timestamp;
		}
		
		return date('Y-m-d', $timestamp) . 'T' . date('H:i:s', $timestamp);
	}
	
	private $id_factory;
    private $doc; //DOM document
    private $current; //current DOM element
    private $default_prefix = '';
    
    function __construct($item=null, $prefix = ''){
    	if($item instanceof ImsXmlWriter){
    		$this->doc = $item->get_doc();
    		$this->current = $item->get_current();
    		$this->id_factory = $item->get_id_factory();
    		$this->default_prefix = $item->get_default_prefix();
    	}else if($item instanceof DOMDocument){
    		$this->doc = $item;
        	$this->id_factory = new ImsIdFactory();
    	}else if(is_null($item)){
    		$this->doc = $this->create_doc();
        	$this->id_factory = new ImsIdFactory();
    	}else{
    		debug($item);
    		throw new Exception('Unknown type');
    	}
    	if(!empty($prefix)){
    		$this->default_prefix = $prefix;
    	}
    }
    
    protected function create_doc(){
    	return new DOMDocument('1.0', 'UTF-8');
    }
    
    public function get_format_version(){
    	return '';
    }
    
    public function get_format_name(){
    	return '';
    }
    
    public function get_format_full_name(){
    	$result = $this->get_format_name() . ' ' . $this->get_format_version();
    	return $result;
    }
    
    public function get_doc(){
    	return $this->doc;
    }
    
    public function get_root(){
    	return $this->doc->documentElement;
    }
        
    public function get_current(){
    	return $this->current;
    }
    
    public function get_id_factory(){
    	return $this->id_factory;
    }

    protected function create_unique_id($prefix =''){
    	return $this->id_factory->create_unique_id($prefix);
    }

    protected function create_local_id($prefix ='')
    {
    	return $this->id_factory->create_local_id($prefix);
    }
    
    public function get_default_prefix(){
    	return $this->default_prefix;	
    }
    
    public function set_default_prefix($value){
    	$this->default_prefix = $value;
    }
    
    protected function prefix($name){
    	if(empty($this->default_prefix)){
    		return $name;
    	}else if(strpos($name, ':') !== false){
    		return $name;
    	}else{
    		return "$this->default_prefix:$name";
    	}
    }
    
    public function copy($current){
    	$result = clone $this;
    	$result->current = $current;
    	return $result;
    }
    
    protected function copy_node($node, $prefix='', $deep = true){
    	if($node instanceof DOMText){
    		return $this->doc->createTextNode($node->nodeValue); 
    	}
    	$name = empty($prefix) ? $node->nodeName : $prefix.':'.$node->nodeName; 
	    $result=$this->doc->createElement($name);
	           
	    foreach($node->attributes as $a){
	    	$name = empty($prefix) ? $a->nodeName : $prefix.':'.$a->nodeName; 
	    	$result->setAttribute($name, $a->value);
	    }
	            
	    if(!$deep) 
	    	return $result;
	                
	    foreach($node->childNodes as $child) {
	    	if($new_node = $this->copy_node($child, $prefix, $deep)){
	    		$result->appendChild($new_node);
	    	}
	    }
	           
	    return $result;
    }

    /**
     * Add an XSL stylesheet reference.
     * 
     * @param $href
     */
    public function add_stylesheet($href){
    	$i = 'type="text/xsl" href="'. $href .'"';
    	$result = $this->get_doc()->createProcessingInstruction('xml-stylesheet', $i);
    	$this->get_doc()->appendChild($result);
    	return $this->copy($result);
    }
    
    public function add($item, $value = ''){
    	if(is_array($item)){
    		return $this->add_elements($item);
    	}else{
    		return $this->add_element($item, $value);
    	}
    }

    /**
     * @param $tag
     * @param $value
     * @return ImsXmlWriter
     */
    public function add_element($tag, $value = ''){
    	$tag = $this->prefix($tag);
    	$result = $this->doc->createElement($tag, $value);
    	$this->append_child($result);
    	return $this->copy($result);
    }
    
    public function add_text($data){
    	$doc = new DOMDocument();
    	$result = $this->doc->createTextNode($data);
    	$this->append_child($result);
    }
    
    public function add_xml($xml){
    	if(empty($xml)){
    		return $this;
    	}    	
    	
    	$fragment = $this->doc->createDocumentFragment();
    	if($fragment->appendXML("<root>$xml</root>")){  
		    $child = $fragment->firstChild->firstChild;   
		    while($child){   
		    	$next_child = $child->nextSibling; //should be done before append child
		        $this->append_child($child);   
		        $child = $next_child;   
		    }   
    		return $this;
    	}else{
    		
    		//@todo: remove this
    		debug(libxml_get_last_error()); 
    		debug(' _ '.htmlentities("<root>$xml</root>") . ' _ ');
    		debug(' _ '."<root>$xml</root>" . ' _ ');
    		echo DebugUtil2::print_backtrace_html();
    		die;
    		return $this;
    	}
    }
  
    public function add_xhml($xml){
    	if(empty($xml)){
    		return $this;
    	}    	
        //unknown named html entities are rejected by load xml
        //we replace named entities by their digital counterparts
        $xml = str_replace('&nbsp;', '&#160;', $xml);
        $result = $this->add_xml($xml);
         
        return $result;
    }
    
    public function add_elements($tags){
    	$result = $this;
    	foreach($tags as $name=>$value){
    		$result = $this->add_element($name, $value);
    	}
    	return $result;
    }
    
    public function get_attribute($name){
    	return $this->current->getAttribute($name);
    }
    
    public function set_attribute($tag, $value, $write_empty=true){
    	$tag = $this->prefix($tag);
    	if($write_empty || !empty($value)){
        	$this->current->setAttribute($tag, $value);
    	}
    }

    public function save($path){
    	return $this->doc->save($path);
    }
    
    public function saveXML($declaration = true){
    	if($declaration) {
    		$result = $this->doc->saveXML();
    	}else{
    		$doc = $this->doc;
    		$result = $doc->saveXML($doc->documentElement, LIBXML_NOENT );
    	}
    	return $result;
    }

    public function validate(){
    	return $this->doc->validate();
    }
    
    public function schema_validate($filename){
    	return $this->doc->schemaValidate($filename);
    }
    
    public function __call($name, $arguments){
    	$n = explode('_', $name);
    	$action = $n[0];
    	$name = $n[1];
    	$method = array($this, $action);
    	$arguments = array_merge(array($name), $arguments);
    	if($action=='add'){
    		call_user_func_array($method, $arguments);
    	}
    }
    
    public function __get($name){
    	return $this->get_attribute($name);
    }
    
    public function __set($name, $value){
    	$this->set_attribute($name, $value);
    }

    protected function append_child($child){
    	if(empty($this->current)){
    		return $this->doc->appendChild($child);
    	}else{
    		return $this->current->appendChild($child);
    	}
    }
    
}



















?>