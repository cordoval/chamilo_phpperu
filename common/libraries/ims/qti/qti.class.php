<?php

/**
 * Helper class. Contains constants, tags and helper functions.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class Qti{
	
	const BASETYPE_IDENTIFIER =	'identifier'; //The set of identifier values is the same as the set of values defined by the identifier class
	const BASETYPE_BOOLEAN = 'boolean'; //The set of boolean values is the same as the set of values defined by the boolean class. 
	const BASETYPE_INTEGER = 'integer'; //The set of integer values is the same as the set of values defined by the integer class.
	const BASETYPE_FLOAT = 'float'; //The set of float values is the same as the set of values defined by the float class.
	const BASETYPE_STRING = 'string'; //The set of string values is the same as the set of values defined by the string class.
	const BASETYPE_POINT = 'point'; //A point value represents an integer tuple corresponding to a graphic point. The two integers correspond to the horizontal (x-axis) and vertical (y-axis) positions respectively. The up/down and left/right senses of the axes are context dependent.
	const BASETYPE_PAIR = 'pair'; //A pair value represents a pair of identifiers corresponding to an association between two objects. The association is undirected so (A,B) and (B,A) are equivalent.
	const BASETYPE_DIRECTEDPAIR = 'directedPair'; //A directedPair value represents a pair of identifiers corresponding to a directed association between two objects. The two identifiers correspond to the source and destination objects.
	const BASETYPE_DURATION = 'duration'; //A duration value specifies a distance (in time) between two time points. In other words, a time period as defined by [ISO8601]. Durations are measured in seconds and may have a fractional part.
	const BASETYPE_FILE = 'file'; //A file value is any sequence of octets (bytes) qualified by a content-type and an optional filename given to the file (for example, by the candidate when uploading it as part of an interaction). The content type of the file is one of the MIME types defined by [RFC2045].
	const BASETYPE_URI = 'uri'; //A URI value is a Uniform Resource Identifier
	
	const CARDINALITY_SINGLE = 'single';
	const CARDINALITY_MULTIPLE = 'multiple';
	const CARDINALITY_ORDERED = 'ordered';
	const CARDINALITY_RECORD = 'record';
	
	const TOLERANCE_MODE_EXACT = 'exact';
	const TOLERANCE_MODE_ABSOLUTE = 'absolute';
	const TOLERANCE_MODE_RELATIVE = 'relative';
	
	const SCORE = 'SCORE';
	const RESPONSE = 'RESPONSE';
	const FEEDBACK = 'FEEDBACK';
	const FEEDBACK_SHOW = 'show';
	const FEEDBACK_HIDE = 'hide';
	
	const VIEW_AUTHOR = 'author';
	const VIEW_CANDIDATE = 'candidate';
	const VIEW_PROCTOR = 'proctor';
	const VIEW_SCORER = 'scorer';
	const VIEW_TUTOR = 'tutor';
	const VIEW_ALL = 'author candidate proctor scorer tutor';
	
	const ORIENTATION_VERTICAL = 'vertical';
	const ORIENTATION_HORIZONTAL = 'horizontal';	
	
	const SUBMISSION_MODE_INDIVIDUAL = 'individual';
	const SUBMISSION_MODE_SIMULTANEOUS = 'simultaneous';
	
	const NAVIGATION_MODE_LINEAR = 'linear';
	const NAVIGATION_MODE_NON_LINEAR = 'nonlinear';
	
	const SHAPE_POLY = 'poly';
	const SHAPE_CIRCLE = 'circle';
	const SHAPE_RECT = 'rect';
	const SHAPE_DEFAULT = 'default';
	const SHAPE_ELLIPSE = 'ellipse'; //DEPRECATED
	
	private static $tags = null;
	
	public static function get_tags(){
		if(empty(self::$tags)){
			$result['a'] = 'a';
			$result['abbr'] = 'abbr';
			$result['acronym'] = 'acronym';
			$result['adaptive'] = 'adaptive';
			$result['address'] = 'address';
			$result['and'] = 'and';
			$result['anyN'] = 'anyN';
			$result['areaMapEntry'] = 'areaMapEntry';
			$result['areaMapping'] = 'areaMapping';
			$result['assessmentItem'] = 'assessmentItem';
			$result['associableHotspot'] = 'associableHotspot';
			$result['associateInteraction'] = 'associateInteraction';
			$result['b'] = 'b';
			$result['bankProfile'] = 'bankProfile';
			$result['baseValue'] = 'baseValue';
			$result['big'] = 'big';
			$result['blockquote'] = 'blockquote';
			$result['br'] = 'br';
			$result['caption'] = 'caption';
			$result['categorizedStatistic'] = 'categorizedStatistic';
			$result['choiceInteraction'] = 'choiceInteraction';
			$result['cite'] = 'cite';
			$result['code'] = 'code';
			$result['col'] = 'col';
			$result['colgroup'] = 'colgroup';
			$result['composite'] = 'composite';
			$result['contains'] = 'contains';
			$result['contentProfile'] = 'contentProfile';
			$result['correct'] = 'correct';
			$result['correctResponse'] = 'correctResponse';
			$result['customInteraction'] = 'customInteraction';
			$result['customOperator'] = 'customOperator';
			$result['dd'] = 'dd';
			$result['default'] = 'default';
			$result['defaultValue'] = 'defaultValue';
			$result['delete'] = 'delete';
			$result['dfn'] = 'dfn';
			$result['div'] = 'div';
			$result['divide'] = 'divide';
			$result['dl'] = 'dl';
			$result['drawingInteraction'] = 'drawingInteraction';
			$result['dt'] = 'dt';
			$result['durationGTE'] = 'durationGTE';
			$result['durationLT'] = 'durationLT';
			$result['em'] = 'em';
			$result['endAttemptInteraction'] = 'endAttemptInteraction';
			$result['equal'] = 'equal';
			$result['equalRounded'] = 'equalRounded';
			$result['exitResponse'] = 'exitResponse';
			$result['exitTemplate'] = 'exitTemplate';
			$result['extendedTextInteraction'] = 'extendedTextInteraction';
			$result['feedbackBlock'] = 'feedbackBlock';
			$result['feedbackInline'] = 'feedbackInline';
			$result['feedbackIntegrated'] = 'feedbackIntegrated';
			$result['feedbackModal'] = 'feedbackModal';
			$result['feedbackType'] = 'feedbackType';
			$result['fieldValue'] = 'fieldValue';
			$result['gap'] = 'gap';
			$result['gapImg'] = 'gapImg';
			$result['gapMatchInteraction'] = 'gapMatchInteraction';
			$result['gapText'] = 'gapText';
			$result['graphicAssociateInteraction'] = 'graphicAssociateInteraction';
			$result['graphicGapMatchInteraction'] = 'graphicGapMatchInteraction';
			$result['graphicOrderInteraction'] = 'graphicOrderInteraction';
			$result['gt'] = 'gt';
			$result['gte'] = 'gte';
			$result['h1'] = 'h1';
			$result['h2'] = 'h2';
			$result['h3'] = 'h3';
			$result['h4'] = 'h4';
			$result['h5'] = 'h5';
			$result['h6'] = 'h6';
			$result['hotspotChoice'] = 'hotspotChoice';
			$result['hotspotInteraction'] = 'hotspotInteraction';
			$result['hottext'] = 'hottext';
			$result['hottextInteraction'] = 'hottextInteraction';
			$result['hr'] = 'hr';
			$result['hypertextElement'] = 'hypertextElement';
			$result['i'] = 'i';
			$result['imageElement'] = 'imageElement';
			$result['imageType'] = 'imageType';
			$result['img'] = 'img';
			$result['imsmd'] = 'imsmd';
			$result['imsqtimd'] = 'imsqtimd';
			$result['index'] = 'index';
			$result['inlineChoice'] = 'inlineChoice';
			$result['inlineChoiceInteraction'] = 'inlineChoiceInteraction';
			$result['inside'] = 'inside';
			$result['integerDivide'] = 'integerDivide';
			$result['integerModulus'] = 'integerModulus';
			$result['integerToFloat'] = 'integerToFloat';
			$result['interactionType'] = 'interactionType';
			$result['isNull'] = 'isNull';
			$result['itemBody'] = 'itemBody';
			$result['itemTemplate'] = 'itemTemplate';
			$result['kbd'] = 'kbd';
			$result['li'] = 'li';
			$result['listElements'] = 'listElements';
			$result['lomMetadata'] = 'lomMetadata';
			$result['lt'] = 'lt';
			$result['lte'] = 'lte';
			$result['m:math'] = 'm:math';
			$result['mapEntry'] = 'mapEntry';
			$result['mapping'] = 'mapping';
			$result['mapResponse'] = 'mapResponse';
			$result['mapResponsePoint'] = 'mapResponsePoint';
			$result['match'] = 'match';
			$result['matchInteraction'] = 'matchInteraction';
			$result['mathElement'] = 'mathElement';
			$result['mathVariable'] = 'mathVariable';
			$result['member'] = 'member';
			$result['metadataProfile'] = 'metadataProfile';
			$result['modalFeedback'] = 'modalFeedback';
			$result['multiple'] = 'multiple';
			$result['not'] = 'not';
			$result['null'] = 'null';
			$result['object'] = 'object';
			$result['objectElements'] = 'objectElements';
			$result['objectType'] = 'objectType';
			$result['ol'] = 'ol';
			$result['or'] = 'or';
			$result['ordered'] = 'ordered';
			$result['orderInteraction'] = 'orderInteraction';
			$result['ordinaryStatistic'] = 'ordinaryStatistic';
			$result['outcomeDeclaration'] = 'outcomeDeclaration';
			$result['p'] = 'p';
			$result['param'] = 'param';
			$result['patternMatch'] = 'patternMatch';
			$result['positionObjectInteraction'] = 'positionObjectInteraction';
			$result['positionObjectStage'] = 'positionObjectStage';
			$result['power'] = 'power';
			$result['pre'] = 'pre';
			$result['presentationElements'] = 'presentationElements';
			$result['printedVariable'] = 'printedVariable';
			$result['printedVariables'] = 'printedVariables';
			$result['product'] = 'product';
			$result['prompt'] = 'prompt';
			$result['q'] = 'q';
			$result['qtiMetadata'] = 'qtiMetadata';
			$result['random'] = 'random';
			$result['randomFloat'] = 'randomFloat';
			$result['randomInteger'] = 'randomInteger';
			$result['regexp'] = 'regexp';
			$result['responseCondition'] = 'responseCondition';
			$result['responseDeclaration'] = 'responseDeclaration';
			$result['responseElse'] = 'responseElse';
			$result['responseElseIf'] = 'responseElseIf';
			$result['responseIf'] = 'responseIf';
			$result['responseProcessing'] = 'responseProcessing';
			$result['responseRules'] = 'responseRules';
			$result['round'] = 'round';
			$result['rounding'] = 'rounding';
			$result['rpTemplate'] = 'rpTemplate';
			$result['rubric'] = 'rubric';
			$result['rubricBlock'] = 'rubricBlock';
			$result['samp'] = 'samp';
			$result['selectPointInteraction'] = 'selectPointInteraction';
			$result['setCorrectResponse'] = 'setCorrectResponse';
			$result['setDefaultValue'] = 'setDefaultValue';
			$result['setOutcomeValue'] = 'setOutcomeValue';
			$result['setTemplateValue'] = 'setTemplateValue';
			$result['simpleAssociableChoice'] = 'simpleAssociableChoice';
			$result['simpleChoice'] = 'simpleChoice';
			$result['simpleMatchSet'] = 'simpleMatchSet';
			$result['sliderInteraction'] = 'sliderInteraction';
			$result['small'] = 'small';
			$result['solutionAvailable'] = 'solutionAvailable';
			$result['span'] = 'span';
			$result['stringMatch'] = 'stringMatch';
			$result['strong'] = 'strong';
			$result['stylesheet'] = 'stylesheet';
			$result['sub'] = 'sub';
			$result['substring'] = 'substring';
			$result['subtract'] = 'subtract';
			$result['sum'] = 'sum';
			$result['sup'] = 'sup';
			$result['table'] = 'table';
			$result['tableElements'] = 'tableElements';
			$result['targetObject'] = 'targetObject';
			$result['tbody'] = 'tbody';
			$result['td'] = 'td';
			$result['templateBlock'] = 'templateBlock';
			$result['templateCondition'] = 'templateCondition';
			$result['templateDeclaration'] = 'templateDeclaration';
			$result['templateElse'] = 'templateElse';
			$result['templateElseIf'] = 'templateElseIf';
			$result['templateIf'] = 'templateIf';
			$result['templateInline'] = 'templateInline';
			$result['templateProcessing'] = 'templateProcessing';
			$result['templates'] = 'templates';
			$result['textElements'] = 'textElements';
			$result['textEntryInteraction'] = 'textEntryInteraction';
			$result['tfoot'] = 'tfoot';
			$result['th'] = 'th';
			$result['thead'] = 'thead';
			$result['timeDependent'] = 'timeDependent';
			$result['toolName'] = 'toolName';
			$result['toolVendor'] = 'toolVendor';
			$result['toolVersion'] = 'toolVersion';
			$result['tr'] = 'tr';
			$result['truncate'] = 'truncate';
			$result['tt'] = 'tt';
			$result['ul'] = 'ul';
			$result['uploadInteraction'] = 'uploadInteraction';
			$result['usageData'] = 'usageData';
			$result['usageDataVocabulary'] = 'usageDataVocabulary';
			$result['value'] = 'value';
			$result['var'] = 'var';
			$result['variable'] = 'variable';
			self::$tags = $result;
		}
		return self::$tags;
	}

	private static $interactions = null;
	public static function get_interactions(){
		if(empty(self::$interactions)){
	    	$result = array();
			$result['customInteraction'] = 'customInteraction';
			$result['drawingInteraction'] = 'drawingInteraction';
			$result['gapMatchInteraction'] = 'gapMatchInteraction';
			$result['matchInteraction'] = 'matchInteraction';
			$result['graphicGapMatchInteraction'] = 'graphicGapMatchInteraction';
			$result['hotspotInteraction'] = 'hotspotInteraction';
			$result['graphicOrderInteraction'] = 'graphicOrderInteraction';
			$result['selectPointInteraction'] = 'selectPointInteraction';
			$result['graphicAssociateInteraction'] = 'graphicAssociateInteraction';
			$result['sliderInteraction'] = 'sliderInteraction';
			$result['choiceInteraction'] = 'choiceInteraction';
			$result['hottextInteraction'] = 'hottextInteraction';
			$result['orderInteraction'] = 'orderInteraction';
			$result['extendedTextInteraction'] = 'extendedTextInteraction';
			$result['uploadInteraction'] = 'uploadInteraction';
		    $result['associateInteraction'] = 'associateInteraction';
		    $result['inlineChoiceInteraction'] = 'inlineChoiceInteraction';
		    $result['textEntryInteraction'] = 'textEntryInteraction'; 
		    $result['positionObjectInteraction'] = 'positionObjectInteraction'; 
	        self::$interactions = $result;
		}
		return self::$interactions;
	}

	private static $feedbacks = null;
	public static function get_feedbacks(){
		if(empty(self::$feedbacks)){
			$result = array();
			$result['feedbackBlock'] = 'feedbackBlock';
			$result['feedbackInline'] = 'feedbackInline';
			$result['feedbackIntegrated'] = 'feedbackIntegrated';
			$result['feedbackModal'] = 'feedbackModal';
			$result['feedbackType'] = 'feedbackType';
			$result['modalFeedback'] = 'modalFeedback';
			self::$feedbacks = $result;
		}
		
		return self::$feedbacks;
	}
	
	public static function is_interaction($name){
		$tags = self::get_interactions();
		return isset($tags[$name]);  
	}

	public static function is_feedback($name){
		$tags = self::get_feedbacks();
		return isset($tags[$name]);  
	}

	public static function get_tool_name(){
		return 'imsqti:unige:chamilo';
	}
	
	public static function get_tool_version(){
		return '20100422';
	}

	public static function is_qti_file($path, $n1 = 'http://www.imsglobal.org/xsd/imsqti_v2p0', $n2 = 'http://www.imsglobal.org/xsd/imsqti_v2p1'){
		if(strtolower(pathinfo($path, PATHINFO_EXTENSION)) != 'xml'){
			return false;
		}
		$basename = basename($path, '.xml');
		$basename = strtolower($basename);
		if($basename == 'imsmanifest'){
			return false;
		}
		$doc = new DOMDocument();
		$doc->load($path);
		$root = $doc->documentElement;
		if($root->tagName != 'assessmentItem' && $root->tagName != 'assessmentTest'){
			return false;
		}
		$namespace = $doc->documentElement->getAttribute('xmlns');
		if(empty($namespace)){
			return true;
		}
		$namespace = reset(explode(' ', $namespace));
		$qti_namespaces = func_get_args();
		array_shift($qti_namespaces);
		$qti_namespaces[] = $n1;
		$qti_namespaces[] = $n2;
		foreach($qti_namespaces as $qti_namespace){
			if(strtolower($qti_namespace) == strtolower($namespace)){
				return true;
			}
		}
		return false;	
	}
	
	public static function is_question_file($path, $n1 = 'http://www.imsglobal.org/xsd/imsqti_v2p0', $n2 = 'http://www.imsglobal.org/xsd/imsqti_v2p1'){
		if(!self::is_qti_file($path, $n1, $n2)){
			return false;
		}
		$doc = new DOMDocument();
		$doc->load($path);
		$root = $doc->documentElement;
		return $root->tagName == 'assessmentItem';
	}
	
	public static function is_test_file($path, $n1 = 'http://www.imsglobal.org/xsd/imsqti_v2p0', $n2 = 'http://www.imsglobal.org/xsd/imsqti_v2p1'){
		if(!self::is_qti_file($path, $n1, $n2)){
			return false;
		}
		$doc = new DOMDocument();
		$doc->load($path);
		$root = $doc->documentElement;
		return $root->tagName == 'assessmentTest';
	}
	
}











?>