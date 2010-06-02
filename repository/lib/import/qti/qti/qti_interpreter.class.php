<?php

require_once dirname(dirname(__FILE__)) .'/main.php';

/**
 * QTI rule interpreter. Processes template's rules as well as response's rules.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiInterpreter{
	
	private $root = null; //root item, i.e. the assessement item.
	private $responses = array(); //response variables
	private $outcomes = array(); //outcome variables
	private $sessions = array(); //session variables
	private $templates = array(); //template variables
	private $break = false; //true if execution should stop, false otherwise
	private $roles = array(); //roles - teacher, etc - used by the renderer 
	private $correct_responses = array(); //contains the correct response when set by a template rule
	private $default_values = array(); //contains the default value when set by a template rule
	
	public function __construct($roles = ''){
		$this->reset($roles);
	}
	
	public function get_responses(){
		return $this->responses;
	}
	
	public function add_response($key, $value){
		if(is_string($key)){
			$name = $key;
		}else if($key->responseIdentifier){
			$name = $key->responseIdentifier;
		}else{
			$name = $key->identifier;
		}
		
		if(empty($name)){
			debug($key->get_current());
			throw new Exception('Invalid key (empty)');
		}
		
		if($this->is_formula($value)){ 
			$result = $this->execute($value);
		}else if(is_array($value) && count($value) == 1){
			$result = reset($value);
		}else if(is_array($value)){
			//@todo: confirm why this. Clashes with points
			$result = array();
			foreach($value as $val){
				$result[$val] = $val;
			}
		}else{
			$result = $value;
		}
		
		$this->responses[$name] = $result;
		return $result;
	}
	
	public function get_outcomes(){
		return $this->outcomes;
	}

	public function get_outcome($id){
		if(isset($this->outcomes[$id])){
			return $this->outcomes[$id];
		}else{
			return null;
		}	
	}
	
	public function get_sessions(){
		return $this->sessions;
	}
	
	public function get_templates(){
		return $this->templates;		
	}
	
	public function get_template($id){
		return $this->templates[$id];	
	}
	
	public function add_template($name, $value){
		$this->templates[$name] = $result;
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

	public function set_roles($_){
		$this->reset_roles();
		$args = func_get_arg();
		foreach($args as $arg){
			$this->add_role($arg);
		}
	}
	
	public function reset($roles = ''){
		$this->root = null;
		$this->outcomes = array();
		$this->responses = array();
		$this->sessions = array();
		$this->templates = array();
		$this->break = false;
		$this->roles = array();
		$this->correct_responses = array();
		$this->default_values = array();
		if(!empty($roles)){
			$this->add_role($roles);
		}
	}
	
	public function reset_response(){
		$this->root = null;
		$this->responses = array();
		$this->break = false;
	}
	
	public function execute(ImsXmlReader $item){
		$this->break = false;
		$this->root = $item;
		$result = $this->process($item);
		$this->root = null;
		$this->break = false;
		return $result;
	}

	/**
	 * Execute declarations end templates but not response processing
	 * @param ImsXmlReader $assessment_item
	 */
	public function init(ImsXmlReader $assessment_item){
		$this->outcomes = array();
		$this->responses = array();
		$this->sessions = array();
		$this->templates = array();
		$this->correct_responses = array();
		$this->default_values = array();
		$this->break = false;
		
		$this->root = $assessment_item;
		
		$this->process_all($assessment_item->list_responseDeclaration());
		$this->process_all($assessment_item->list_outcomeDeclaration());
		$this->process_all($assessment_item->list_templateDeclaration());
		$this->process($assessment_item->get_templateProcessing());
		
		$this->root = null;
		$this->break = false;
	}
	
	/**
	 * Execute response processing
	 * @param ImsXmlReader $assessment_item
	 */
	public function response(ImsXmlReader $assessment_item){
		$this->break = false;
		$this->root = $assessment_item;
		
		$this->process($assessment_item->get_responseProcessing()); 
		
		$this->root = null;
		$this->break = false;
	}

	public function is_formula($item){
		return $item instanceof ImsQtiReader;
	}
	
	protected function is_responseRule(ImsXmlReader $item){
		return 	$item->is_exitResponse() || 
				$item->is_responseCondition() ||  
				$item->is_setOutcomeValue();
	}

	protected function get_image_size($response_id){
		$path = './/*[@responseIdentifier="'. $response_id .'"]';
		$interactions = $this->root->query($path);
		foreach($interactions as $interaction){
			$items = $interaction->all_object();
			foreach($items as $item){
				$width = $item->width;
				$height = $item->height;
				if(!empty($width) && !empty($height)){
					return array($width, $height);
				}
			}
			$items = $interaction->all_img();
			foreach($items as $item){
				$width = $item->width;
				$height = $item->height;
				if(!empty($width) && !empty($height)){
					return array($width, $height);
				}
			}
		}
		return false;
	}
	
	protected function get_image_rectangle($response_id){
		if($size = $this->get_image_size($response_id)){
			$result = array();
			$result[] = array(0, 0);
			$result[] = $size;
			return $result;
		}else{
			return false;
		}
	}
	
	protected function get_declaration($id){
		return $this->root->get_child_by_id($id);
	}
	
	protected function get_basetype_default($base_type){
		switch($base_type){
			case Qti::BASETYPE_FLOAT:
			case Qti::BASETYPE_INTEGER:
				return 0;
				
			case Qti::BASETYPE_BOOLEAN:
			case Qti::BASETYPE_DIRECTEDPAIR:
			case Qti::BASETYPE_DURATION:
			case Qti::BASETYPE_FILE:
			case Qti::BASETYPE_IDENTIFIER:
			case Qti::BASETYPE_PAIR:
			case Qti::BASETYPE_POINT:
			case Qti::BASETYPE_STRING:
			case Qti::BASETYPE_URI:
				return null;
				
			default:
				debug('Unknown base type: '. $base_type);
				return null;
		}
	}
	
	protected function compare($left, $right, $base_type){
		switch($base_type){
			case Qti::BASETYPE_IDENTIFIER:
			case Qti::BASETYPE_STRING:
			case Qti::BASETYPE_URI:
				$result = $left == $right;
				break;
				
			case Qti::BASETYPE_BOOLEAN:
			case Qti::BASETYPE_INTEGER:
			case Qti::BASETYPE_FLOAT:
				$result = $left == $right;
				break;
				
			case Qti::BASETYPE_PAIR :
				if(is_array($left) && is_array($right)){
					$result = ($left[0] == $right[0] && $left[1] == $right[1]) ||
						($left[0] == $right[1] && $left[1] == $right[0] );
				}else{
					$result = $left == $right;
				}
				break;
				
			case Qti::BASETYPE_DIRECTEDPAIR:
				if(is_array($left) && is_array($right)){
					$result = ($left[0] == $right[0] && $left[1] == $right[1]);
				}else{
					$result = $left == $right;
				}
				break;
				
			case Qti::BASETYPE_POINT:
				$result = ($left[0] == $right[0] && $left[1] == $right[1]);
				break;
				
			case Qti::BASETYPE_DURATION:
				$result = $left == $right;
				break;
				
			default:
				debug('Unknown base type: '. $base_type);
				$result = $left == $right;;
				break;
		}
		
		return $result;
	}
	
	protected function process(ImsXmlReader $item, $_ = ''){
		if($this->break) return;
		if($item->is_empty()) return;

		$f = array($this, 'process_'.$item->name());
		$args = func_get_args();
		if(is_callable($f)){
			return call_user_func_array($f, $args);
		}else{
			throw new Exception('Unknown: '.$item->name());
		}
	}	
	
	protected function process_children(ImsXmlReader $item, $_ = ''){
		if($this->break) return;
		
		$result = false;
		$args = func_get_args();
		$children = $item->children();
		foreach($children as $child){
			$args[0] = $child;
			$result = call_user_func_array(array($this, 'process'), $args);
		}
		return $result;
	}
	
	protected function process_all($items, $_=''){
		if($this->break) return;
		
		$result = false;
		$args = func_get_args();
		$f = array($this, 'process');
		foreach($items as $item){
			$args[0] = $item;
			$result = call_user_func_array($f, $args);
		}
		return $result;
	}
	
	protected function process_assessmentItem(ImsXmlReader $item){
		$this->process_all($item->list_responseDeclaration());
		$this->process_all($item->list_outcomeDeclaration());
		$this->process_all($item->list_templateDeclaration());
		$this->process($item->get_templateProcessing());
		$this->process($item->get_responseProcessing()); 
	} 
	
	protected function process_outcomeDeclaration(ImsXmlReader $item){
		$identifier = $item->identifier;
		if(isset($this->outcomes[$identifier])) return;
		
		if(isset($this->default_values[$identifier])){
			$this->outcomes[$identifier] = $this->default_values[$identifier];
		}else if($item->has_defaultValue()){
			$this->outcomes[$identifier] = $this->process($item->get_defaultValue());
		}else{
			$this->outcomes[$identifier] = $this->get_basetype_default($item->baseType);
		}
	}

	protected function process_responseDeclaration(ImsXmlReader $item){
		$identifier = $item->identifier;
		if(isset($this->responses[$identifier])) return;
		
		if(isset($this->default_values[$identifier])){
			$this->responses[$identifier] = $this->default_values[$identifier];
		}else if($item->has_defaultValue()){
			$this->responses[$identifier] = $this->process($item->get_defaultValue());
		}else{
			$this->responses[$identifier] = $this->get_basetype_default($item->baseType);
		}
	}
	
	protected function process_templateDeclaration(ImsXmlReader $item){
		$identifier = $item->identifier;
		if(isset($this->templates[$identifier])) return;
		
		if(isset($this->default_values[$identifier])){
			$this->templates[$identifier] = $this->default_values[$identifier];
		}else if($item->has_defaultValue()){
			$this->templates[$identifier] = $this->process($item->get_defaultValue());
		}else{
			$this->templates[$identifier] = $this->get_basetype_default($item->baseType);
		}
	}
		
	protected function process_defaultValue(ImsXmlReader $item){
		$list = $item->list_value();
		$result = array(); 
		foreach($list as $item){
			$value = $item->valueof();
			$base_type = $item->baseType;
			$field_identifier = $item->fieldIdentifier;
			if(empty($field_identifier)){
				$result[] = $value;
			}else{
				$result[$field_identifier] = $value;
			}
		}
		$count = count($result); 
		if($count == 0){
			return null;					
		}else if($count == 1){
			return reset($result);
		}else{
			return $result;
		}
	}
	
	protected function process_responseProcessing(ImsXmlReader $item){
		$template = strtolower($item->template);
		if(strpos($template, 'match_correct') !== false){
			$this->process_standard_template_match_correct($item);
		}else if(strpos($template, 'map_response') !== false){
			$this->process_standard_template_map_response($item);
		}else if(strpos($template, 'map_response_point') !== false){
			$this->process_standard_template_map_response_point($item);
		}
		$this->process_children($item);
	}
	
	protected function process_templateProcessing(ImsXmlReader $item){
		$this->process_children($item);
	}

	protected function process_setTemplateValue(ImsXmlReader $item){
		$name = $item->identifier;
		$this->templates[$name] = $this->process_children($item);
	}	

	protected function process_exitTemplate(ImsXmlReader $item){
		$this->break = true;
	}
	
	protected function process_setCorrectResponse(ImsXmlReader $item){
		$identifier = $item->identifier;
		$this->correct_responses[$identifier] = $this->process($item->children_head());
	}
	
	protected function process_setDefaultValue(ImsXmlReader $item){
		$identifier = $item->identifier;
		$this->default_values[$identifier] = $this->process($item->children_head());
	}
	
	protected function process_templateCondition(ImsXmlReader $item){
		$if = $item->get_templateIf();
		if($this->process($if)) return;
		
		$elses = $item->list_templateElseIf();
		foreach($elses as $else){
			if($this->process($else)) return;
		}
		
		$else = $item->get_templateElse();
		$this->process($else);
	}

	protected function process_templateIf(ImsXmlReader $item){
		$response_rules = array();
		$children = $item->children();
		$condition = array_shift($children);
		if($result = $this->process($condition)){
			foreach($children as $template_rule){
				$this->process($template_rule);
			}
		}
		return $result;
	}
		
	protected function process_templateElseIf(ImsXmlReader $item){
		return $this->process_templateIf($item);
	}
		
	protected function process_templateElse(ImsXmlReader $item){
		$this->process_children($item);
	}
	
	protected function process_standard_template_match_correct($item){
		$var = $this->get_declaration('RESPONSE');
		$correct = $var->get_correctResponse()->first_value()->valueof();
		$val = $this->responses['RESPONSE'];
		$this->outcomes['SCORE'] = $val == $correct ? 1 : 0;
	}
	
	protected function process_standard_template_map_response($item){
		$id = 'RESPONSE';
		$var = $this->get_declaration($id);
		$base_type = $var->baseType;
		$value = $this->responses[$id];
		if(is_array($value)){
			$result = 0;
			foreach($value as $item){
				$result += $this->process($var->get_mapping(), $item, $base_type);
			}
		}else{
			$result = $this->process($var->get_mapping(), $value, $base_type);
		}
		
		$this->outcomes['SCORE'] = empty($result) ? 0 : $result;
	}
	
	protected function process_standard_template_map_response_point($item){
		$id = 'RESPONSE';
		$var = $this->get_declaration($id);
		$base_type = $var->baseType;
		$value = $this->responses[$id];
		if(is_array($value)){
			$result = 0;
			foreach($value as $item){
				$result += $this->process($var->get_areaMapping(), $item, $base_type);
			}
		}else{
			$result = $this->process($var->get_areaMapping(), $value, $base_type);
		};
		$this->outcomes['SCORE'] = empty($result) ? 0 : $result;
	}
		
	protected function process_exitResponse(ImsXmlReader $item){
		$this->break = true;
	}	
	
	protected function process_responseCondition(ImsXmlReader $item){
		$if = $item->get_responseIf();
		if($this->process($if)) return;
		
		$elses = $item->list_responseElseIf();
		foreach($elses as $else){
			if($this->process($else)) return;
		}
		
		$else = $item->get_responseElse();
		$this->process($else);
	}

	protected function process_responseIf(ImsXmlReader $item){
		$response_rules = array();
		$condition = null;
		$children = $item->children();
		foreach($children as $child){
			if($this->is_responseRule($child)){
				$response_rules[] = $child;
			}else if(empty($condition)){
				$condition = $child;
			}else{
				throw new Exception('Invalid input');
			}
		}
		$result = $this->process($condition);
		if($result){
			foreach($response_rules as $response){
				$this->process($response);
			}
		}
		return $result;
	}
		
	protected function process_responseElseIf(ImsXmlReader $item){
		return $this->process_responseIf($item);
	}
		
	protected function process_responseElse(ImsXmlReader $item){
		$this->process_children($item);
	}

	protected function process_setOutcomeValue(ImsXmlReader $item){
		$name = $item->identifier;
		$this->outcomes[$name] = $this->process_children($item);
	}	
	
	protected function process_variable(ImsXmlReader $item){
		$name = $item->identifier;
		if(array_key_exists($name, $this->responses)){
			$result = $this->responses[$name];
		}else if(array_key_exists($name, $this->outcomes)){
			$result = $this->outcomes[$name];
		}else if(array_key_exists($name, $this->sessions)){
			$result = $this->sessions[$name];
		}else if(array_key_exists($name, $this->templates)){
			$result = $this->templates[$name];
		}else{
			throw new Exception('Unknown variable: '. $name);		
		}
		return $result;
	}
	
	protected function process_default(ImsXmlReader $item){
		$var = $this->get_declaration($item->identifier);
		if($var->has_defaultValue()){
			return $this->process_defaultValue($var);
		}else{
			return null;
		}
	}
	
	protected function process_baseValue(ImsXmlReader $item){
		return $item->valueof();
	}
	
	/**
	 * This expression looks up the declaration of a responseVariable and returns the associated correctResponse or NULL if no correct value was declared.
	 * @param ImsXmlReader $item
	 */
	protected function process_correct(ImsXmlReader $item){
		$identitifer = $item->identifier;
		$var = $this->get_declaration($identifier);
		
		if(isset($this->correct_responses[$identifier])){
			$result = $this->correct_responses[$identitifer];	
		}else{
			$list = $var->get_correctResponse()->list_value(); 
			if (count($list)== 0){
				$result = null;
			}else if(count($list) == 1){
				$result = $list[0]->valueof();
			}else{
				$result = array();
				foreach($list as $item){
					$result[] = $item->valueof();
				}
			}
		}
		return $result;
	}
	
	/**
	 * Attribute : identifier [1]: identifier
	 * This expression looks up the value of a responseVariable that must be of base-type point , and transforms it using the associated areaMapping. The transformation is similar to mapResponse except that the points are tested against each area in turn. When mapping containers each area can be mapped once only. For example, if the candidate identified two points that both fall in the same area then the mappedValue is still added to the calculated total just once.
	 * @param ImsXmlReader $item
	 */
	protected function process_mapResponsePoint(ImsXmlReader $item){
		$id = $item->identifier;
		$var = $this->get_declaration($id);
		$value = $this->responses[$id];
		$result = $this->process($var->get_areaMapping(), $value);
		return $result;
	}
	
	protected function process_areaMapping(ImsXmlReader $item, $responses){
		$default = $item->defaultValue;
		$max = $item->upperBound;
		$min = $item->lowerBound;
		if(empty($responses) || !is_array($responses)){
			return $default;
		}
		$points = is_array(reset($responses)) ?  $responses : array($responses);
		$entries = $item->list_areaMapEntry();
		$done = array();
		
		$no_match = true;
		$result = 0;
		foreach($points as $point){
			foreach($entries as $key=>$entry){
				$shape = $entry->shape;
				$coords = $entry->coords;
				$value = $entry->mappedValue;
				if(	$this->is_point_inside_shape($item, $point, $shape, $coords) &&
					!isset($done[$key])){
					$result += $value;
					$no_match = false;
					$done[$key] = $key;
				}
			}
		}
		$result = $no_match ? $default : $result;
		$result = ($max !== '') ? min($max, $result) : $result;
		$result = ($min !== '') ? max($min, $result) : $result;
		return $result;
	}
	
	protected function process_inside(ImsXmlReader $item){
		$shape = $item->shape;
		$coords = $item->coords;
		$point = $this->process_children($item);
		$result = $this->is_point_inside_shape($item, $point, $shape, $coords);
		return $result;
	}
	
	protected function is_point_inside_shape($item, $point, $shape, $coords){
		$x = array_shift($point);
		$y = array_shift($point);
		$point = array($x, $y);
		if($shape == Qti::SHAPE_DEFAULT){
			$response_id = $item->get_parent()->identifier;
			if($rect = $this->get_image_rectangle($response_id)){
				return shape::inside_rectangle($rect, $point);
			}
		}else if($shape == Qti::SHAPE_RECT){
			$rect = shape::string_to_polygone($coords, ',');
			return shape::inside_rectangle($rect, $point);
			
		}else if($shape == Qti::SHAPE_CIRCLE){
			//@todo: radius as a percent;
			$coords = explode(' ', $coords); //center-x, center-y, radius.
			$x = $coords[0];
			$y = $coords[1];
			$radius = $coords[2];
			
			return shape::inside_circle($x, $y, $radius, $point);
			
		}else if($shape == Qti::SHAPE_POLY){
			$poly = shape::string_to_polygone($coords, ',');
			return shape::inside_polygone($poly, $point);
			
		}else{
			throw new Exception('Unknown shape');
		}
	}
	
	protected function process_mapResponse(ImsXmlReader $item){
		$id = $item->identifier;
		$var = $this->get_declaration($id);
		$base_type = $var->baseType;
		//$cardinality = $var->cardinality;
		$value = $this->responses[$id];
		if(is_array($value)){
			$result = 0;
			foreach($value as $item){
				$result += $this->process($var->get_mapping(), $item, $base_type);
			}
		}else{
			$result = $this->process($var->get_mapping(), $value, $base_type);
		}
		return $result;
	}
	
	protected function process_mapping(ImsXmlReader $item, $response, $base_type){
		$default = $item->defaultValue;
		$max = $item->upperBound;
		$min = $item->lowerBound;

		$result = '';
		$entries = $item->list_mapEntry();
		foreach($entries as $entry){
			$key = $entry->mapKey;
			$value = $entry->mappedValue;
			if($this->compare($key, $response, $base_type)){
				$value = $max !== '' && $max<$value ? $max : $value;
				$value = $min !== '' && $value < $min ? $min : $value;
				return $value;
			}
		}
		if($default !== ''){
			$value = $default;
			$value = $max !== '' && $max<$value ? $max : $value;
			$value = $min !== '' && $value < $min ? $min : $value;
			return $value;
		}
		throw new Exception('No match');
	}
	
	protected function process_null(ImsXmlReader $item){
		return null;
	}
	
	protected function process_randomFloat(ImsXmlReader $item) {
		$min = $item->min == '' ? 0 : $item->min;
		$max = $item->max == '' ? 1 : $item->max;
		return rand($min, $max);
	}

	protected function process_randomInteger(ImsXmlReader $item) {
		$min = $item->min == '' ? 0 : $item->min;
		$max = $item->max == '' ? 10 : $item->max;
		$step = $item->step == '' ? 1 : $item->step;
		$result = $min + rand(0, 1) * ($max-$min);
		$result = round($result);
		$result = $result - ($result % step);
		return $result;
	}
	
	protected function process_not(ImsXmlReader $item){
		$children = $item->children();
		return ! $this->process($children[0]);
	}
	
	/**
	 * The or operator takes one or more sub-expressions each with a base-type of boolean and single cardinality. The result is a single boolean which is true if any of the sub-expressions are true and false if all of them are false. If one or more sub-expressions are NULL and all the others are false then the operator also results in NULL.
	 * @param ImsXmlReader $item
	 */
	protected function process_or(ImsXmlReader $item){
		$children = $item->children();
		$result = $this->process($children[0]);
		if(is_null($result)){
			return $result;
		}
		for($i = 1, $count = count($children); $i < $count; $i++){
			$right = $this->process($children[$i]);
			if(is_null($right)){
				return $result;
			}
			$result = $result || $right; 
		}
		return $result;
	}

	/**
	 * The and operator takes one or more sub-expressions each with a base-type of boolean and single cardinality. The result is a single boolean which is true if all sub-expressions are true and false if any of them are false. If one or more sub-expressions are NULL and all others are true then the operator also results in NULL.
	 * @param ImsXmlReader $item
	 */
	protected function process_and(ImsXmlReader $item){
		$children = $item->children();
		$result = $this->process($children[0]);
		if(is_null($result)){
			return $result;
		}
		for($i = 1, $count = count($children); $i < $count; $i++){
			$right = $this->process($children[$i]);
			if(is_null($right)){
				return $result;
			}
			$result = $result && $right; 
		}
		return $result;
	}
	
	protected function process_gt(ImsXmlReader $item){
		$children = $item->children();
		$left = $children[0];
		$right = $children[1];
		return $this->process($left) > $this->process($right);
	}

	protected function process_gte(ImsXmlReader $item){
		$children = $item->children();
		$left = $children[0];
		$right = $children[1];
		return $this->process($left) >= $this->process($right);
	}

	protected function process_lt(ImsXmlReader $item){
		$children = $item->children();
		$left = $children[0];
		$right = $children[1];
		return $this->process($left) < $this->process($right);
	}
	
	protected function process_lte(ImsXmlReader $item){
		$children = $item->children();
		$left = $children[0];
		$right = $children[1];
		return $this->process($left) <= $this->process($right);
	}

	protected function process_isNull(ImsXmlReader $item){
		$children = $item->children();
		return is_null($this->process($children[0]));
	}
	
	protected function process_match(ImsXmlReader $item){
		$children = $item->children();
		$left = $children[0];
		$right = $children[1];
		return $this->process($left) == $this->process($right);
	}
	
	protected function process_substring(ImsXmlReader $item){
		$children = $item->children();
		$left = $this->process($children[0]);
		$right = $this->process($children[1]);
		$case_sensitive = $item->caseSensitive != 'false';
		if(!$case_sensitive){
			$left = strtolower($left);
			$right = strtolower($right);
		}
		return strpos($left, $right) !== false; 
	}
	
	protected function process_stringMatch(ImsXmlReader $item){
		$children = $item->children();
		$left = $this->process($children[0]);
		$right = $this->process($children[1]);
		$case_sensitive = $item->caseSensitive != 'false';
		$substring = $item->substring != 'false';
		if(!$case_sensitive){
			$left = strtolower($left);
			$right = strtolower($right);
		}
		if(!$substring){
			return $left == $right;
		}else{
			return strpos($left, $right) !== false; 
		}
	}

	/**
	 * The patternMatch operator takes a sub-expression which must have single cardinality and a base-type of string. The result is a single boolean with a value of true if the sub-expression matches the regular expression given by pattern and false if it doesn't. If the sub-expression is NULL then the operator results in NULL.
	 * 
	 * Attribute : pattern [1]: string
	 * The syntax for the regular expression language is as defined in Appendix F of [XML_SCHEMA2].
	 * 
	 * @param ImsXmlReader $item
	 */
	protected function process_patternMatch(ImsXmlReader $item){
		$children = $item->children();
		$expression = $this->process($children[0]);
		if(is_null($expression)){
			return null;
		}
		$delimiter = '/'; 
		$pattern = $item->pattern;
		if(strpos($expression, $delimiter) !== false){
			$pattern = str_replace($pattern, '\\'.$delimiter, $expression);
		}
		$pattern = $delimiter.$pattern.$delimiter;
		return preg_match($pattern, $expression) > 0;
	}

	/**
	 * The equal operator takes two sub-expressions which must both have single cardinality and have a numerical base-type. The result is a single boolean with a value of true if the two expressions are numerically equal and false if they are not. If either sub-expression is NULL then the operator results in NULL.
	 * 
	 * Attribute : toleranceMode [1]: toleranceMode = exact
	 * When comparing two floating point numbers for equality it is often desirable to have a tolerance to ensure that spurious errors in scoring are not introduced by rounding errors. The tolerance mode determines whether the comparison is done exactly, using an absolute range or a relative range. 
	 * 
	 * Attribute : tolerance [0..2]: float
	 * If the tolerance mode is absolute or relative then the tolerance must be specified. The tolerance consists of two positive numbers, t0 and t1, that define the lower and upper bounds. If only one value is given it is used for both.
	 * 
	 * In absolute mode the result of the comparison is true if the value of the second expression, y is within the following range defined by the first value, x.
	 * 
	 * [x-t0,x+t1]In relative mode, t0 and t1 are treated as percentages and the following range is used instead.
	 * 
	 * [x*(1-t0/100),x*(1+t1/100)]
	 * 
	 * @param ImsXmlReader $item
	 */
	protected function process_equal(ImsXmlReader $item){
		$left = $this->process($item->children(0));
		$right = $this->process($item->children(1));
		if(is_null($left) || is_null($right)){
			return null;
		}
		$tolerance_mode = $item->toleranceMode;
		if(empty($item->tolerance)){
			$tolerance = array(0, 0);	
		}else{
			$tolerance = explode(' ', $item->tolerance);
			$tolerance = count($tolerance)==1 ? array($tolerance[0], $tolerance[0]) : $tolerance;
		}
		
		if(empty($tolerance_mode) || $tolerance_mode == Qti::TOLERANCE_MODE_EXACT){
			return $left == $right;
		}else if($tolerance_mode == Qti::TOLERANCE_MODE_ABSOLUTE){
			return $left - $tolerance[0] <= $right && $right <= $left + $tolerance[1];
		}else if($tolerance_mode == Qti::TOLERANCE_MODE_RELATIVE){
			$tolerance = empty($tolerance) ? array(0, 0) : $tolerance;
			return $left * (1- $tolerance[0]/100) <= $right && $right <= $left * (1 + $tolerance[1]/100);
		}else{
			debug("Unknown tolerance type: $tolerance_type");
			return $left == $right;
		}
	}

	protected function process_sum(ImsXmlReader $item){
		$children = $item->children();
		$result = 0;
		foreach($children as $child){
			$result = $result + $this->process($child);
		}
		return $result;
	}

	protected function process_product(ImsXmlReader $item){
		$children = $item->children();
		$result = 1;
		foreach($children as $child){
			$result = $result * $this->process($child);
		}
		return $result;
	}

	protected function process_subtract(ImsXmlReader $item){
		$children = $item->children();
		$left = $children[0];
		$right = $children[1];
		return $this->process($left) - $this->process($right);
	}

	protected function process_divide(ImsXmlReader $item){
		$children = $item->children();
		$left = $children[0];
		$right = $children[1];
		return $this->process($left) / $this->process($right);
	}

	protected function process_power(ImsXmlReader $item){
		$children = $item->children();
		$left = $children[0];
		$right = $children[1];
		return pow($this->process($left), $this->process($right));
	}
	
	protected function process_integerDivide(ImsXmlReader $item){
		$children = $item->children();
		$left = $children[0];
		$right = $children[1];
		return round($this->process($left) / $this->process($right));
	}

	protected function process_integerModulus(ImsXmlReader $item){
		$children = $item->children();
		$left = $children[0];
		$right = $children[1];
		return round($this->process($left) % $this->process($right));
	}

	protected function process_round(ImsXmlReader $item){
		$children = $item->children();
		return round($this->process($children[0]));
	}
	
	protected function process_integerToFloat(ImsXmlReader $item){
		$children = $item->children();
		return $this->process($children[0]);
	}

	protected function process_random(ImsXmlReader $item){
		$items = $this->process_children($item);
		if(empty($items)){
			return null;
		}else{
			 $index = rand(0, count($items)-1);
			 return $items[$index];
		}
	}
	
	/**
	 * The multiple operator takes 0 or more sub-expressions all of which must have either single or multiple cardinality. Although the sub-expressions may be of any base-type they must all be of the same base-type. The result is a container with multiple cardinality containing the values of the sub-expressions, sub-expressions with multiple cardinality have their individual values added to the result: containers cannot contain other containers. For example, when applied to A, B and {C,D} the multiple operator results in {A,B,C,D}. All sub-expressions with NULL values are ignored. If no sub-expressions are given (or all are NULL) then the result is NULL.
	 * @param ImsXmlReader $item
	 */
	protected function process_multiple(ImsXmlReader $item){
		$result = array();
		$children = $item->children();
		foreach($children as $child){
			$child_result = $this->process($child);
			if(is_null($child_result)){
				;//ignore
			}else if(is_array($child_result)){
				$result = array_merge($result, $child_result);
			}else{
				$result[] = $child_result;
			}
		}
		return empty($result) ? null : $result;
	}
	
	protected function process_customOperator(ImsXmlReader $item){
		$op = $item->class;
		$args = array();
		$children = $item->children();
		foreach($children as $child){
			$args[] = $this->process($child);
		}
		return call_user_func_array($op, $args);
	}
	
	
}




























