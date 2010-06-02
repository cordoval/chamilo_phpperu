<?php

require_once dirname(dirname(__FILE__)) .'/main.php';

/**
 * Base class for QTI renderers. I.e. classes which render the qti file to a specific format.
 * Used to render Qti items to HTML and to Moodle's Cloze format.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiRendererBase{
		
	private $resource_manager = null;
	private $outcomes = array();
	private $map = array();
	private $doc = null;
	private $root = null;
	private $roles = array();
	
	public function __construct($resource_manager){
		$this->resource_manager = $resource_manager;
		$this->map = $this->create_map();
	}
	
	public function get_resource_manager(){
		return $this->resource_manager;
	}

	public function get_ressources(){
		return $this->ressources;
	}
	
	public function get_map(){
		return $this->map;
	}
	
	public function add_mapping($key, $value){
		$this->map[$key] = $value;
	}
	
	public function is_mapped($key){
		return isset($this->map[$key]) && !empty($this->map[$key]);
	}
	
	public function remove_mapping($key){
		unset($this->map[$key]);
	}
	
	public function get_outcomes(){
		return $this->outcomes;
	}
	
	public function set_outcomes(array $items){
		$result = array();
		foreach($items as $key=>$value){
			$result[strtolower($key)] = $value;
		}
		$this->outcomes = $result;
	}
	
	public function reset_outcomes(){
		$this->outcomes = array();
		return $this;
	}

	public function set_outcome($name, $value){
		$this->outcomes[strtolower($name)] = $value;
	}
	
	public function get_roles(){
		return $this->roles;
	}
	
	public function add_role($role){
		$roles = explode(' ', $role);
		$roles = is_array($roles) ? $roles : array($roles);
		foreach($roles as $r){
			$this->roles[$r] = $r;
		}
	}
	
	public function reset_roles(){
		$this->roles = array();
	}
	
	public function init(QtiInterpreter $interpreter){
		$this->reset_outcomes();
		$this->set_outcomes($interpreter->get_outcomes());	
		$this->reset_roles();	
		$roles = $interpreter->get_roles();
		foreach($roles as $role){
			$this->add_role($role);
		}
	}
	
	public function render(ImsXmlReader $item, $role = Qti::VIEW_CANDIDATE){
		$this->add_role($role);
		return $this->to_html($item);
	}
	
	/**
	 * Render $item to HTML
	 * @param ImsQtiReader $item
	 */
	public function to_html(ImsXmlReader $item, $role = Qti::VIEW_CANDIDATE){
		if($item->is_empty()){
			return '';
		}
		$this->add_role($role);
		
		$registered = $this->start_processing($item);
		$result = $this->process($item);
		$result = empty($result) ? '' : $this->doc->saveXML($result);
		$this->end_processing($item, $registered);
		$result = $this->html_cleanup($result);
		return $result;
	}
	
	/**
	 * Render $item to plain text
	 * @param ImsQtiReader $item
	 */
	public function to_text(ImsXmlReader $item, $role = Qti::VIEW_CANDIDATE){
		if($item->is_empty()){
			return '';
		}
		$this->add_role($role);
		
		$registered = $this->start_processing($item);
		$result = $this->process($item);
		$this->end_processing($item, $registered);
		return empty($result) ? '' : $result->textContent;
	}

	protected function start_processing(ImsXmlReader  $item){
		$this->doc = new DOMDocument();
		$this->resource_manager->set_current_path($item->filepath());
		return $this->register($item);
	}
	
	protected function end_processing($item, $unregister){
		$this->doc = null;
		$this->resource_manager->set_current_path('');
		if($unregister){
			$this->unregister($item);
		}
	}
	
	protected function get_doc(){
		return $this->doc;
	}
	
	/**
	 * @result ImsXmlReader
	 */
	protected function get_root(){
		return $this->root;		
	}
	
	protected function create_map(){
		$result = array_merge(Xhtml::get_tags(), MathML::get_tags());
		return $result;
	}
	
	/**
	 * Force to execute $item. Default to a span if not already registered with one of the maps
	 * @param ImsQtiReader $item
	 */
	protected function register(ImsQtiReader $item){
		$this->root = $item;
		if(! $this->is_mapped($item->name())){
			$this->add_mapping($item->name(), 'span');
			return true;
		}else{
			return false;
		}
	}
	
	protected function unregister(ImsQtiReader $item){
		$this->remove_mapping($item->name());
		$this->root = null;
	}	

	/**
	 * Return true if $item is to be processed. False otherwise
	 * @param ImsQtiReader $node
	 * @return boolean
	 */
	protected function accept(ImsQtiReader $item){
		$node = $item->get_current();
		if(!($node instanceof DOMElement ||	$node instanceof DOMAttr || 
			$node instanceof DOMText)){
			return false;
		}else if($node instanceof DOMAttr || $node instanceof DOMText){
			return true;
		}
		$name = $this->translate_tag($node->nodeName);
		if(empty($name)){
			return false;
		}
		if($item->is_feedback() && !$this->display_feeback($item)){
			return false;
		}
		if($item->is_rubricBlock()){
			$view = $item->view;
			$view = explode(' ', $view);
			$view = is_array($view) ? $view : array($view);
			$view = array_combine($view, $view);
			
			$roles = $this->get_roles();
			foreach($roles as $role){
				if(isset($view[$role])){
					return true;
				}
			}
			return false;
			
		}
		return true;
	}

	protected function accept_attribute($name){
		return xhtml::is_attribute($name);
	}
	
	protected function translate_tag($name){
		$map = $this->get_map();
		$name = $this->remove_namespace_prefix($name);
		$result = isset($map[$name]) ? $map[$name] : '';
		return $result;
	}    
	
	protected function remove_namespace_prefix($name){
		if(strpos($name, ':') !== false){
			$parts = explode(':', $name);
			$result = $parts[1];
		}else{
			$result = $name;
		}
		return $result;
	}
	
	/**
	 * Returns true if a QTI feedback node is to be displayed, depending on the $outcomes results. 
	 * False otherwise.
	 * @param ImsQtiReader $item
	 * @return boolean
	 */
	protected function display_feeback(ImsQtiReader $item){
		$id = strtolower($item->outcomeIdentifier);
		$show_hide = strtolower($item->showHide);
		$expected_value = strtolower($item->identifier);
		$default = $show_hide == 'hide';
		if(! isset($this->outcomes[$id])){
			return $default; 
		}else{
			$outcome = $this->outcomes[$id];
			if(is_array($outcome)){
				return isset($outcome[$expected_value]) ? !$default : $default;
			}else{
				$value = strtolower($outcome);
				return ($value == $expected_value) ? !$default : $default;
			} 
		}
	}
		
	protected function process(ImsQtiReader $item, $prefix = '', $deep = true){
		if($item instanceof ImsQtiReader){
			$name = $item->name();
			$name = $this->remove_namespace_prefix($name);
			$f = array($this, "process_$name");
			if(is_callable($f)){
				$result = call_user_func_array($f, func_get_args());
				return $result;
			}
		}
		$result = $this->process_default($item, $prefix, $deep);
		return $result;
	}
	
    protected function process_default(ImsQtiReader $item, $prefix = '', $deep = true){
	    if(!$this->accept($item)){;
	    	return false;
	    }
	    
	    $node = $item->get_current();
	    if($node instanceof DOMText){
	    	return $this->doc->createTextNode($node->value());
	    }
	    
    	$name = $this->translate_tag($item->name());
    	$name = empty($prefix) ? $name : $prefix.':'.$name; 
    	//$value = $item->is_leaf() ? $item->value() : '';
    	$result = $this->doc->createElement($name);
    	
    	$attributes = $item->attributes();
    	foreach($attributes as $name=>$value){
    		if($this->accept_attribute($name)){
	    		$name = empty($prefix) ? $name : "$prefix:$name"; 
	    		$result->setAttribute($name, $value);
    		}
		}
    
    	if($item->is_img()){
    		$this->rewrite_path($result, 'src');
    	}else if($item->is_object()){
    		$this->rewrite_path($result, 'data');
    	}
	            
    	$node = $item->get_current();
    	$children = $node->childNodes;
    	for($i = 0, $length = $children->length; $i<$length; $i++){
    		$child = $children->item($i);
    		if($child instanceof DOMText){
    			$child_copy = $this->doc->createTextNode($child->wholeText);
    		}else if($deep) {
				$child_copy = $this->process($item->copy($child), $prefix, $deep);
			}else{
				$child_copy = false;
			}
			if($child_copy){
				$result->appendChild($child_copy);
			}
    	}
	    return $result;
    }

	protected function process_math(ImsXmlReader  $item, $prefix = '', $deep = true){
		
		$prefix = 'm';
    	$name = $this->translate_tag($item->name());
    	$name = empty($prefix) ? $name : $prefix.':'.$name; 
    	$result = $this->get_doc()->createElement($name);
    	$result->setAttribute('xmlns:m', 'http://www.w3.org/1998/Math/MathML');
    	$node = $item->get_current();
    	$children = $node->childNodes;
    	for($i = 0, $length = $children->length; $i<$length; $i++){
    		$child = $children->item($i);
    		if($child instanceof DOMText || $child instanceof DOMCharacterData){
    			$child_copy = $this->get_doc()->createTextNode($child->nodeValue);
    		}else if($deep) {
				$child_copy = $this->process($item->copy($child), $prefix, $deep);
			}else{
				$child_copy = false;
			}
			if($child_copy){
				$result->appendChild($child_copy);
			}
    	}
    	return $result;
	}
	
    protected function rewrite_path($node, $attribute){
    	if(!$node->hasAttribute($attribute)) return;
    	
    	$path = $node->getAttribute($attribute);
    	$path = $this->resource_manager->translate_path($path);
    	$node->setAttribute($attribute, $path);
    }
    
    protected function html_cleanup($text){
    	$result = $text;
		$result = str_replace('<span/>', '', $result);
		$result = html_trim_tag($result, 'p', 'span', 'div');
		$nbsp = utf8_encode("\xA0");
		$result = str_replace($nbsp, '&nbsp;', $result);
    	return $result;
    }
}