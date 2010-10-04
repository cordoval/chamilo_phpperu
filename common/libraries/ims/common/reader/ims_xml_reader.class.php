<?php

//require_once dirname(dirname(__FILE__)) .'/main.php';

/**
 * Helper class used to read an XML file.
 * Provides magic methods:
 * 
 * 	- get_tagname()		returns child reader with tagname
 * 	- list_tagname()	returns all immediate child readers with tagname
 * 	- all_tagname()		returns all (immediate and deep) child readers with tagname
 * 	- first_tagname()	returns the first child reader with tagname
 * 	- has_tagname()		returns true if one of the immediate child has tagname 
 * 	- is_tagname()		returns true if the current node has tagname
 * 
 * Magic properties:
 * 
 *  - attributeName		returns the attribute value or '' if none is present. 
 * 
 * Register 'def' as the default namespace to be used with xpath expressions. 
 * Always calls query with the def namespace even if none is provided, i.e. './def:tagname'
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class ImsXmlReader implements IteratorAggregate
{
    public static function parse_date($text){
    	if(empty($text)){
    		return 0;
    	}
    	$text = strtoupper($text);
    	$text = str_replace('T', '-', $text);
    	$text = str_replace(':', '-', $text);
    	$pieces = explode('-', $text);
    	$year = $pieces[0];
    	$month = $pieces[1];
    	$day = $pieces[2];
    	$hour = isset($pieces[3]) ? $pieces[3] : 0;
    	$minute = isset($pieces[4]) ? $pieces[4] : 0;
    	$second = isset($pieces[5]) ? $pieces[5] : 0;
    	return mktime($hour, $minute, $second, $month, $day, $year);
    }
	
    private static $empty_reader = null;
    public static function get_empty_reader(){
    	if(is_null(self::$empty_reader)){
    		self::$empty_reader = new ImsXmlReaderEmpty();
    	}
    	return self::$empty_reader;
    }
    
	private $doc = null;
    private $xpath = null;
    private $filepath = ''; 
    //private $parent = null; //Parent writer
    
    /**
     * 
     * @var DOMNode
     */
    private $current = null; //current DOM element
    private $return_null = true;
    
    public function __construct($item='', $return_null=false){
    	$this->return_null= $return_null;
    	if(!empty($item)){
    		$this->load($item);
    	}
    }
    
    public function filepath(){
    	return $this->filepath;
    }
    
    public function copy($current){
    	if($current instanceof ImsXmlReader){
    		$current = $current->get_current();
    	}
    	$result = clone $this;
    	$result->current = $current;
    	//$result->parent = $this->parent;
    	return $result;
    }
    
    public function load($item){
    	if(is_string($item)){
    		$this->load_path($item);
    	}else if($item instanceof ImsXmlReader){
    		$this->load_node($item->get_current());
    	}else if(is_string($item)){
    		$this->load_xml($item);
    	}else{
    		throw new Exception('Unknown type '. print_r($item, true));
    	}
    }
    
    public function load_node($node){
        $this->doc = new DOMDocument('1.0', 'UTF-8');
    	$copy = $this->doc->importNode($node, true);
    	$this->doc->appendChild($copy);
        $this->xpath = $this->create_xpath();
    	//$this->parent = $this;
    	$this->current = $this->doc->documentElement;
    	$this->filepath = '';
    }
    
    public function load_path($path){
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->formatOutput = true;
        $this->doc->load($path);
        $this->xpath = $this->create_xpath();
    	//$this->parent = $this;
    	$this->current = $this->doc->documentElement;
    	$this->filepath = $path;
    }
    
    public function load_xml($xml){
        $this->doc = new DOMDocument('1.0', 'UTF-8');
    	$this->doc->loadXML($xml);
        $this->xpath = $this->create_xpath();
    	//$this->parent = $this;
    	$this->current = $this->doc->documentElement;
    	$this->filepath = '';
    }
    
    public function get_default_namespace(){
    	//if(empty($this->doc->documentElement)){
    	//	echo DebugUtil2::print_backtrace_html();
    	//}
    	return $this->doc->documentElement->getAttribute('xmlns');
    }
    
    public function get_default_namespace_prefix(){
    	return 'def';
    }
    
    public function get_root(){
    	return $this->copy($this->doc->documentElement);
    }
    
    public function get_xml(){
    	return $this->get_doc()->saveXML($this->current);
    }

    public function get_inner_xml(){
    	$result = '';
    	$child = $this->current->firstChild;
    	while($child){
    		$result .= $this->get_doc()->saveXML($child);
    		$child = $child->nextSibling;
    	}
    	return $result;
    }
    
    public function get_return_null(){
    	return $this->return_null;
    }
    
    public function set_return_null($value){
    	$this->return_null = $value;
    }
    
    public function get_default_result(){
    	if($this->return_null){
    		return null;
    	}else{
    		return self::get_empty_reader();
    	}
    }
    
    public function get_doc(){
    	return $this->doc;
    }

    /**
     * @return DomNode
     */
    public function get_current(){
    	return $this->current;
    }
    
    public function get_parent(){
    	if(empty($this->current)){
    		return $this->get_default_result();
    	}elseif($parent_node = $this->current->parentNode){
    		return $this->copy($parent_node);
    	}else{
    		debug('Alert');
    		return $this->get_default_result();
    	}
    		
    	return $this->parent;
    }
    
    public function is_empty(){
    	return is_null($this->doc);
    }
    
    public function query($path, $context=null){
    	if(substr($path, 0, 1) != '.'){
    		$context = null;
    	}else if(is_null($context)){
    		$context = $this->current;
    	}else if($context instanceof ImsXmlReader){
    		$context = $context->get_current();
    	}
    	
    	$default = $this->get_default_namespace();
    	if(empty($default)){
    		$path = str_replace('def:', '', $path);
    	}
    	if(empty($context)){
    		$nodes = $this->xpath->query($path);
    	}else{
    		$nodes = $this->xpath->query($path, $context);
    	}
    	$result = array();
    	foreach($nodes as $node){
    		$result[] = $this->copy($node);
    	}
    	return $result;
    }

    public function exist($path, $context=null){
    	$result = $this->query($path, $context);
    	return !empty($result);
    }
   
    public function evaluate($query, $context=null){
    	if(is_null($context)){
    		$context = $this->current;
    	}
    	
    	if(empty($context)){
    		$eval = $this->xpath->evaluate($path);
    	}else{
    		$eval = $this->xpath->evaluate($path, $context);
    	}
    	
    	if($eval instanceof DOMNodeList){
	    	$result = array();
	    	foreach($nodes as $node){
	    		$result[] = $this->copy($node);
	    	}
    	}else{
    		$result = $eval;
    	}
    	
    	return $result;
    }

    public function children($index = null){
    	$result = $this->query('./*');
    	if(is_null($index)){
    		return $result;
    	}else if($index < count($result) && $index >= 0){
    		return $result[$index];
    	}else{
    		return $this->get_default_result();	
    	}
    }

    public function children_head(){
    	return $this->children(0);
    }
    
    public function value($name=''){
    	if(empty($name)){
    		return $this->text();
    	}else if($this->current->hasAttribute($name)){
    		return $this->current->getAttribute($name);
    	}else{
    		$chilren = $this->query("./$name");
    		$result = '';
    		foreach($children as $child){
    			$result .= $child->value();
    		}
    		return $result;
    	}
    }
    
    public function valueof($name=''){
    	return $this->value($name);
    }
    
    public function text(){
    	$current = $this->current;
    	$result = '';
    	if($current->hasChildNodes()){
    		$children = $current->childNodes;
    		for($i = 0, $length = $children->length; $i<$length; $i++){
    			$child = $children->item($i);
    			if($child instanceof DOMText || $child instanceof  DOMCharacterData){
    				$result .= $child->nodeValue;
    			}
    		}
    	}
    	if(empty($result)){
    		$result = $this->current->nodeValue;
    	}
    	return $result;
    } 
    
    public function name(){
    	return $this->current->nodeName;
    }
    
    public function is_scalar(){
    	$node = $this->current;
    	if($node->hasAttributes()){
    		return false;
    	}
    	foreach($node->childNodes as $child){
    		if($child instanceof DOMElement){
    			return false;
    		}
    	}
    	return true;
    }
    
    public function is_leaf(){
    	$node = $this->current;
    	if($node->hasChildNodes()){
	    	foreach($node->childNodes as $child){
	    		if($child instanceof DOMElement){
	    			return false;
	    		}
	    	}
    	}
    	return true;
    }
    
    public function get_attribute($name){
    	if($this->current->hasAttributes() && $this->current instanceof DOMElement){
			return $this->current->getAttribute($name);
    	}else{
    		return '';
    	}
    }
    
    public function attributes(){
    	$result = array();
    	$node = $this->current;
		if($node->hasAttributes()){
			for($i = 0, $length = $node->attributes->length; $i < $length; ++$i) {
	    		$a = $node->attributes->item($i);
	    		$result[$a->name] = $a->value;
			}
		}
    	return $result;
    }
    
    public function is($name){
    	return strtolower($this->current->nodeName) == strtolower($name);
    }
    
    public function has($name){
    	if($this->current->hasAttributes() && !is_null($this->current->attributes->getNamedItem($name))){
    		return true;
    	}
    	if($this->current->getElementsByTagName($name)->length != 0){
    		return true;
    	}
    	return false;
    }
    
    public function get($name){
    	$node = $this->current;
    	if($node->hasAttribute($name)){
    		return $node->getAttribute($name);
    	}else{
    		$children = $this->query("./def:$name");
    		if(count($children) > 0){
    			$child = $children[0];
    			$result = $this->copy($child);
    			//if($result->is_scalar()){
    			//	return $result->value();
    			//}else{
    				return $result;
    			//}
    		}else{
    			return $this->get_default_result();
    		}
    	} 
    } 

    public function all($name){
    		return $this->query('.//def:'.$name);
    }
    
    public function first($path, $context=null){
    	$nodes = $this->query($path, $context);
    	if(count($nodes)){
    		return $nodes[0];
    	}else{
    		return $this->get_default_result();
    	}
    }
    
    public function node_to_object(){
    	$for = $this->current;
    	
    	if($for instanceof DOMText || $for instanceof DOMAttr){
    		return $for->nodeValue;
    	}
    	if($for->attributes->length == 0 && $for->childNodes->length ==0){
    		return $for->nodeValue;
    	}
    	$children = $for->childNodes;
    	$hasChild = $for->attributes->length > 0;
    	foreach($children as $child){
    		if($child instanceof DOMElement){
    			$hasChild=true;
    			break;
    		}
    	}
    	if(!$hasChild){
    		return $for->nodeValue;
    	}
    	$result = new stdClass();
    	foreach($children as $child){
    		$value = $this->node_to_object($child);
    		$name = $child->nodeName;
    		if(!isset($result->$name)){
    			$result->$name = $value;
    		}else if(is_array($result->$name)){
    			$a &= $result->$name;
    			$a[] = $value;
    		}else{
    			$result->$name = array($result->$name, $value);
    		}
    	}
    	
    	$attributes = $for->attributes;
    	foreach($attributes as $attribute){
    		$name = $attribute->nodeName;
    		$value = $attribute->nodeValue;
    		if(!isset($result->$name)){
    			$result->$name = $value;
    		}else if(is_array($result->$name)){
    			$a &= $result->$name;
    			$a[] = $value;
    		}else{
    			$result->$name = array($result->$name, $value);
    		}
    	}
    	return $result;
    }
    
    protected function create_xpath(){
    	$result = new DOMXPath($this->doc);
        $uri = $this->get_default_namespace();
        $prefix = $this->get_default_namespace_prefix();
        if(!empty($uri)){
        	$result->registerNamespace($prefix, $uri);
        }
        return $result;
    }

    public function __call($name, $arguments){
    	$n = explode('_', $name, 2);
    	$action = $n[0];
    	$property_name = $n[1];
    	if($action=='get'){
    		return $this->get($property_name);
    	}else if($action == 'is'){
    		return $this->is($property_name);
    	}else if($action == 'has'){
    		return $this->has($property_name);
    	}else if($action == 'first'){
    		return $this->first('./def:' .$property_name);
    	}else if($action == 'valueof'){
    		return $this->valueof($property_name);
    	}else if($action == 'list'){
    		return $this->query('./def:'.$property_name);
    	}else if($action == 'all'){
    		return $this->all($property_name);
    	}else{
    		throw new Exception('Unknown mehtod: '. $name);
    	}
    }
    
    public function __get($name){
    	return $this->get_attribute($name);
    }

    /**
     * Required definition of interface IteratorAggregate
	 * @return ImsXmlReaderTopDownIterator
     */
    public function getIterator() {
        return new ImsXmlReaderTopDownIterator($this);
    }

    public function debug(){
    	return $this->current;
    }
}

/**
 * Top down - child first - iterator over the DOMNode tree structure.
 * Returns a reader.
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class ImsXmlReaderTopDownIterator implements Iterator
{
	private $reader = null;
	/**
	 * 
	 * @var DOMNode
	 */
    private $start = null;
    
    /**
     * 
     * @var DOMNode
     */
    private $current = null;

    public function __construct(ImsXmlReader $reader){
    	$this->reader = $reader;
    	$this->start = $reader->get_current();
    	$this->current = $this->start;
    }

    public function rewind() {
    	$this->current = $this->start;
    }

    /**
	 * @return ImsXmlReader
     */
    public function current() {
    	if($this->current){
    		return $this->reader->copy($this->current);
    	}else{
    		return false;
    	}
    }

    public function key() {
    	return $this->current()->nodeName; 
    }

    public function next() {
    	return $this->current = $this->get_next($this->current);
    }

    public function valid() {
        return $this->current() !== false;
    }
    
    protected function get_next(DOMNode $node){
    	if($next_down = $this->get_next_down($node)){
    		$result = $next_down;
    	}else{
    		$result = $this->get_next_up($node);
    	}
    	return $result;
    }

    protected function get_next_down(DOMNode $node){
    	if(!empty($node->firstChild)){
    		return $node->firstChild;
    	}else if($node == $this->start){
    		return false;
    	}else if(!empty($node->nextSibling)){
    		return $node->nextSibling;
    	}else{
    		return false;
    	}
    }
    
    protected function get_next_up(DOMNode $node){
     	if(empty($node->parentNode)){
    		$result = false;
    	}else if($node->parentNode == $this->start){
    		$result = false;
    	}else if(!empty($node->parentNode->nextSibling)){
    		$result = $node->parentNode->nextSibling;
    	}else{
    		$result = $this->get_next_up($node->parentNode);
    	}
    	return $result;
    }
}






















?>