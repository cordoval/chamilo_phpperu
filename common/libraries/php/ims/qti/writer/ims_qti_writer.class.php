<?php

/**
 * Utility class used to generate IMS QTI 2.0 XML schemas.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class ImsQtiWriter extends ImsXmlWriter{
	
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
	
	const VIEW_AUTHOR = 'author';
	const VIEW_CANDIDATE = 'candidate';
	const VIEW_PROCTOR = 'proctor';
	const VIEW_SCORER = 'scorer';
	const VIEW_TUTOR = 'tutor';
	const VIEW_ALL = 'author candidate proctor scorer tutor';
	
	private $id_counter = 0;
	
    function __construct(){
    	parent::__construct();    	
        
    }
    
    public function get_format_name(){
    	return 'qti';
    }

    public function get_format_version(){
    	return '2.0';
    }

	public function next_id(){
    	$result = $this->get_id_factory()->create_local_id('PID');
		return $result;
	}
	
    public function set_attribute($tag, $value, $write_empty=true){
    	if($tag=='identifier' && empty($value)){
    		$value = $this->next_id();
    	}
    	return parent::set_attribute($tag, $value, $write_empty);
    }
    	
    /**
     * A test is a group of assessmentItems with an associated set of rules that determine which of the items the candidate sees, in what order, and in what way the candidate interacts with them. The rules describe the valid paths through the test, when responses are submitted for response processing and when (if at all) feedback is to be given.
     * @param $identifier The principle identifier of the test. This identifier must have a corresponding entry in the test's meta-data. See Meta-data and Usage Data for more information.
     * @param $title The title of an assessmentTest is intended to enable the test to be selected outside of any test session. Therefore, delivery engines may reveal the title to candidates at any time, but are not required to do so.
     * @param $toolName The tool name attribute allows the tool creating the test to identify itself. Other processing systems may use this information to interpret the content of application specific data, such as labels on the elements of the test rubric.
     * @param $toolVersion The tool version attribute allows the tool creating the test to identify its version. This value must only be interpreted in the context of the toolName.
     * @return ImsQtiWriter 
     */
	public function add_assessmentTest($identifier, $title, $toolName='', $toolVersion=''){
    	$result = $this->add_element('assessmentTest');    	
    	$result->set_attribute('xmlns', 'http://www.imsglobal.org/xsd/imsqti_v2p1');
    	$result->set_attribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    	$result->set_attribute('xsi:schemaLocation', 'http://www.imsglobal.org/xsd/imsqti_v2p1 imsqti_v2p1.xsd');
     	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('title', $title);
    	$result->set_attribute('toolName', $toolName, false);
    	$result->set_attribute('toolVersion', $toolVersion, false);
    	return $result;
    }
    
    /**
     * 
     * @param $identifier
     * @param navigationMode $navigationMode The navigation mode determines the general paths that the candidate may take. A testPart in linear mode restricts the candidate to attempt each item in turn. Once the candidate moves on they are not permitted to return. A testPart in nonlinear mode removes this restriction - the candidate is free to navigate to any item in the test at any time. Test delivery systems are free to implement their own user interface elements to facilitate navigation provided they honor the navigation mode currently in effect. A test delivery system may implement nonlinear mode simply by providing a method to step forward or backwards through the test part.
     * @param submissionMode $submissionMode The submission mode determines when the candidate's responses are submitted for response processing. A testPart in individual mode requires the candidate to submit their responses on an item-by-item basis. In simultaneous mode the candidate's responses are all submitted together at the end of the testPart.
     * The choice of submission mode determines the states through which each item's session can pass during the test. In simultaneous mode, response processing cannot take place until the testPart is complete so each item session passes between the interacting and suspended states only. By definition the candidate can take one and only one attempt at each item and feedback cannot be seen during the test. Whether or not candidates can return to review their responses and/or any item-level feedback after the test, is outside the scope of this specification. Simultaneous mode is typical of paper-based tests.
     * In individual mode, response processing may take place during the test and the item session may pass through any of the states described in Items, subject to the itemSessionControl settings in force. Care should be taken when designing user interfaces for systems that support nonlinear navigation mode in combination with individual submission. With this combination candidates may change their responses for an item and then leave it in the suspended state by navigating to a different item in the same part of the test. Test delivery systems need to make it clear to candidates that there are unsubmitted responses (akin to unsaved changes in a traditional document editing system) at the end of the test part. A test delivery system may force candidates to submit or discard such responses before moving to a different item in individual mode if this is more appropriate.
     * @return ImsQtiWriter
     */
	public function add_testPart($identifier, $navigationMode, $submissionMode){
    	$result = $this->add_element('testPart');    	
    	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('navigationMode', $navigationMode);
    	$result->set_attribute('submissionMode', $submissionMode);
    	return $result;
    }
    
    /**
     * When items are referenced as part of a test, the test may impose constraints on how many attempts and which states are allowed. These constraints can be specified for individual items, for whole sections, or for an entire testPart. By default, a setting at testPart level affects all items in that part unless the setting is overridden at the assessmentSection level or ultimately at the individual assessmentItemRef. The defaults given below are used only in the absence of any applicable constraint.
     * @param $maxAttempts For non-adaptive items, maxAttempts controls the maximum number of attempts allowed in the given test context. Normally this is 1 as the scoring rules for non-adaptive items are the same for each attempt. A value of 0 indicates no limit. If it is unspecified it is treated as 1 for non-adaptive items. For adaptive items, the value of maxAttempts is ignored as the number of attempts is limited by the value of the completionStatus built-in outcome variable.
     * A value of maxAttempts greater than 1, by definition, indicates that any applicable feedback must be shown. This applies to both Modal Feedback and Integrated Feedback where applicable. However, once the maximum number of allowed attempts have been used (or for adaptive items, completionStatus has been set to completed) whether or not feedback is shown is controlled by the showFeedback constraint. 
     * @param $showFeedback This constraint affects the visibility of feedback after the end of the last attempt. If it is false then feedback is not shown. This includes both Modal Feedback and Integrated Feedback even if the candidate has access to the review state. The default is false.
     * @param $allowReview This constraint also applies only after the end of the last attempt. If set to true the item session is allowed to enter the review state during which the candidate can review the itemBody along with the responses they gave, but cannot update or resubmit them. If set to false the candidate can not review the itemBody or their responses once they have submitted their last attempt. The default is true.
     * If the review state is allowed, but feedback is not, delivery systems must take extra care not to show integrated feedback that resulted from the last attempt as part of the review process. Feedback can however take the form of hiding material that was previously visible, as well as the more usual form of showing material that was previously hidden. 
     * To resolve this ambiguity, for non-adaptive items the absence of feedback is defined to be the version of the itemBody displayed to the candidate at the start of each attempt. In other words, with the visibility of any integrated feedback determined by the default values of the outcome variables and not the values of the outcome variables updated by the invocation of response processing.
     * For Adaptive Items the situation is complicated by the iterative nature of response processing which makes it hard to identify the appropriate state in which to place the item for review. To avoid requiring delivery engines to cache the values of the outcome variables the setting of showFeedback should be ignored for adaptive items when allowReview is true. When in the review state, the final values of the outcome variables should be used to determine the visibility of integrated feedback.
     * @param $showSolution This constraint controls whether or not the system may provide the candidate with a way of entering the solution state. The default is false.
     * @param $allowComment Some delivery systems support the capture of candidate comments. The comment is not part of the assessed responses but provides feedback from the candidate to the other actors in the assessment process. This constraint controls whether or not the candidate is allowed to provide a comment on the item during the session.
     * @param $allowSkipping An item is defined to be skipped if the candidate has not provided any response. In other words, all response variables are submitted with their default value or are NULL. This definition is consistent with the numberResponded operator available in outcomeProcessing. If false, candidates are not allowed to skip the item, or in other words, they are not allowed to submit the item until they have provided a non-default value for at least one of the response variables. By definition, an item with no response variables cannot be skipped. The value of this attribute is only applicable when the item is in a testPart with individual submission mode. Note that if allowSkipping is true delivery engines must ensure that the candidate can choose to submit no response, for example, through the provision of a "skip" button.
     * @param $validateResponses This attribute controls the behavior of delivery engines when the candidate submits an invalid response. An invalid response is defined to be a response which does not satisfy the constraints imposed by the interaction with which it is associated. See interaction for more information. When validateResponses is turned on (true) then the candidates are not allowed to submit the item until they have provided valid responses for all interactions. When turned off (false) invalid responses may be accepted by the system. The value of this attribute is only applicable when the item is in a testPart with individual submission mode. (See Navigation and Submission.)
     * @return ImsQtiWriter
     */
	public function add_itemSessionControl($maxAttempts, $showFeedback=false, $allowReview=true, $showSolution=false, $allowComment=true, $allowSkipping=true, $validateResponses=false){
    	$result = $this->add_element('itemSessionControl');
    	$result->set_attribute('maxAttempts', $maxAttempts);
    	$result->set_attribute('showFeedback', $showFeedback?'true':'false');
    	$result->set_attribute('allowReview', $allowReview?'true':'false');
    	$result->set_attribute('showSolution', $showSolution?'true':'false');
    	$result->set_attribute('allowComment', $allowComment?'true':'false');
    	$result->set_attribute('allowSkipping', $allowSkipping?'true':'false');
    	$result->set_attribute('validateResponses', $validateResponses?'true':'false');
    	return $result;
    }

    /**
     * Sections group together individual item references and/or sub-sections. A number of common parameters are shared by both types of child element.
     * @param $identifier The identifier of the section or item reference must be unique within the test and must not be the identifier of any testPart.
     * @param $title The title of the section is intended to enable the section to be selected in situations where the contents of the section are not available, for example when a candidate is browsing a test. Therefore, delivery engines may reveal the title to candidates at any time during the test but are not required to do so.
     * @param $visible A visible section is one that is identifiable by the candidate. For example, delivery engines might provide a hierarchical view of the test to aid navigation. In such a view, a visible section would be a visible node in the hierarchy. Conversely, an invisible section is one that is not visible to the candidate�the child elements of an invisible section appear to the candidate as if they were part of the parent section (or testPart). The visibility of a section does not affect the visibility of its child elements. The visibility of each section is determined solely by the value of its own visible attribute.
     * @param $keepTogether An invisible section with a parent that is subject to shuffling can specify whether or not its children, which will appear to the candidate as if they were part of the parent, are shuffled as a block or mixed up with the other children of the parent section.
     * @param $required If a child element is required it must appear (at least once) in the selection. It is in error if a section contains a selection rule that selects fewer child elements than the number of required elements it contains.
     * @param $fixed If a child element is fixed it must never be shuffled. When used in combination with a selection rule fixed elements do not have their position fixed until after selection has taken place. For example, selecting 3 elements from {A,B,C,D} without replacement might result in the selection {A,B,C}. If the section is subject to shuffling but B is fixed then permutations such as {A,C,B} are not allowed whereas permutations like {C,B,A} are. 
     * @return ImsQtiWriter
     */
	public function add_assessmentSection($identifier, $title, $visible, $keepTogether=true, $required=false, $fixed=false){
    	$result = $this->add_element('assessmentSection');
     	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('title', $title);
    	$result->set_attribute('visible', $visible?'true':'false');
    	$result->set_attribute('keepTogether', $keepTogether?'true':'false');
    	$result->set_attribute('required', $required?'true':'false');
    	$result->set_attribute('fixed', $fixed?'true':'false');
    	return $result;
    }

    /**
     * Items are incorporated into the test by reference and not by direct aggregation. Note that the identifier of the reference need not have any meaning outside the test. In particular it is not required to be unique in the context of any catalog, or be represented in the item's meta-data. The syntax of this identifier is more restrictive than that of the identifier attribute of the assessmentItem itself.
     * @param $identifier The identifier of the section or item reference must be unique within the test and must not be the identifier of any testPart.
     * @param $href The uri used to refer to the item's file (e.g., elsewhere in the same content package). There is no requirement that this be unique. A test may refer to the same item multiple times within a test. Note however that each reference must have a unique identifier.
     * @param identifier $category [*] Items can optionally be assigned to one or more categories. Categories are used to allow custom sets of item outcomes to be aggregated during outcomes processing. 
     * @param $required If a child element is required it must appear (at least once) in the selection. It is in error if a section contains a selection rule that selects fewer child elements than the number of required elements it contains.
     * @param $fixed If a child element is fixed it must never be shuffled. When used in combination with a selection rule fixed elements do not have their position fixed until after selection has taken place. For example, selecting 3 elements from {A,B,C,D} without replacement might result in the selection {A,B,C}. If the section is subject to shuffling but B is fixed then permutations such as {A,C,B} are not allowed whereas permutations like {C,B,A} are. 
     * @return ImsQtiWriter
    */
	public function add_assessmentItemRef($identifier, $href, $category='', $required=false, $fixed=false){
    	$result = $this->add_element('assessmentItemRef');
     	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('href', $href);
    	$result->set_attribute('category', $category, false);
    	$result->set_attribute('required', $required?'true':'false');
    	$result->set_attribute('fixed', $fixed?'true':'false');
    	return $result;
    }
    
    /**
     * The contribution of an individual item score to an overall test score typically varies from test to test. The score of the item is said to be weighted. Weights are defined as part of each reference to an item (assessmentItemRef) within a test. 
     * @param $identifier An item can have any number of weights, each one is given an identifier that is used to refer to the weight in outcomes processing. (See the variable and testVariables definitions.)
     * @param $value Weights are floating point values. Weights can be applied to outcome variables of base type float or integer. The weight is applied at the time the variable's value is evaluated during outcomes processing. The result is always treated as having base type float even if the variable itself was declared as being of base type integer. 
     */
	public function add_weight($identifier, $value){
    	$result = $this->add_element('weight');
    	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('value', $value);
    	return $result;
    }
    
    /**
     * Contains : responseDeclaration [*]
     * Contains : outcomeDeclaration [*]
     * Contains : templateDeclaration [*]
     * Contains : templateProcessing [0..1]
     * Contains : stylesheet [0..*]
     * Contains : itemBody [0..1]
     * Contains : responseProcessing [0..1]
     * Contains : modalFeedback [*]
     * @param $title The title of an assessmentItem is intended to enable the item to be selected in situations where the full text of the itemBody is not available, for example when a candidate is browsing a set of items to determine the order in which to attempt them. Therefore, delivery engines may reveal the title to candidates at any time but are not required to do so.
     * @param $adaptive Items are classified into Adaptive Items and Non-adaptive Items.
     * @param $timeDependent
     * @param $label
     * @param $lang
     * @return ImsQtiWriter
     */
    public function add_assessmentItem($identifier = '', $title, $adaptive = false, $timeDependent = false, $label = '', $lang = '', $toolName ='', $toolVersion = ''){	
    	$result = $this->add_element('assessmentItem');
    	$result->set_attribute('xmlns', 'http://www.imsglobal.org/xsd/imsqti_v2p1');
    	$result->set_attribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    	$result->set_attribute('xsi:schemaLocation', 'http://www.imsglobal.org/xsd/imsqti_v2p1 imsqti_v2p1.xsd');
    	$result->set_attribute('identifier', empty($identifier) ? $this->create_local_id('ASSESSMENT') : $identifier);
    	$result->set_attribute('title', $title);
    	$result->set_attribute('adaptive', $adaptive ? 'true' : 'false');
    	$result->set_attribute('timeDependent', $timeDependent ? 'true' : 'false');
    	$result->set_attribute('label', $label, false);
    	$result->set_attribute('xml:lang', $lang, false);
    	$result->set_attribute('toolName', $toolName, false);
    	$result->set_attribute('toolVersion', $toolVersion, false);
    	return $result;
    }
        
    // DECLARATION
    
    /**
     * Response variables are declared by response declarations and bound to interactions in the itemBody.
     * @return ImsQtiWriter
     */
    public function add_responseDeclaration($identifier=self::RESPONSE, $cardinality=self::CARDINALITY_SINGLE, $baseType=self::BASETYPE_STRING){
    	return $this->add_variableDeclaration('responseDeclaration', $identifier, $cardinality, $baseType);
    }

    /**
     * Outcome variables are declared by outcome declarations. Their value is set either from a default given in the declaration itself or by a responseRule during responseProcessing.
     * @param $identifier The identifiers of the built-in session variables are reserved. They are completionStatus and duration. All item variables declared in an item share the same namespace. Different items have different namespaces.
     * @param $cardinality Each variable is either single valued or multi-valued. Multi-valued variables are referred to as containers and come in ordered, unordered and record types. See cardinality for more information.
     * @param $baseType The value space from which the variable's value can be drawn (or in the case of containers, from which the individual values are drawn) is identified with a baseType. The baseType selects one of a small set of predefined types that are considered to have atomic values within the runtime data model. Variables with record cardinality have no base-type.
     * @param $normalMaximum The normalMaximum attribute optionally defines the maximum magnitude of numeric outcome variables, it must be a positive value. If given, the outcome's value can be divided by normalMaximum and then truncated (if necessary) to obtain a normalized score in the range [-1.0,1.0]. normalMaximum has no affect on responseProcessing or the values that the outcome variable itself can take.
     * @return ImsQtiWriter
     */
    public function add_outcomeDeclaration($identifier=self::SCORE, $cardinality=self::CARDINALITY_SINGLE, $baseType=self::BASETYPE_INTEGER, $normalMaximum = ''){
    	$result = $this->add_variableDeclaration('outcomeDeclaration', $identifier, $cardinality, $baseType);
    	$result->set_attribute('normalMaximum', $normalMaximum, false);
    	return $result;
    }
    
    /**
     * Outcome variables are declared by outcome declarations. Their value is set either from a default given in the declaration itself or by a responseRule during responseProcessing.
     * @param $identifier The identifiers of the built-in session variables are reserved. They are completionStatus and duration. All item variables declared in an item share the same namespace. Different items have different namespaces.
     * @param $cardinality Each variable is either single valued or multi-valued. Multi-valued variables are referred to as containers and come in ordered, unordered and record types. See cardinality for more information.
         * @return ImsQtiWriter
     */
    public function add_outcomeDeclaration_feedback($identifier=self::FEEDBACK, $cardinality=self::CARDINALITY_SINGLE){
    	return $this->add_variableDeclaration('outcomeDeclaration', $identifier, $cardinality, self::BASETYPE_IDENTIFIER);
    }
    
    /**
     * Outcome variables are declared by outcome declarations. Their value is set either from a default given in the declaration itself or by a responseRule during responseProcessing.
     * @param $identifier The identifiers of the built-in session variables are reserved. They are completionStatus and duration. All item variables declared in an item share the same namespace. Different items have different namespaces.
     * @param $cardinality Each variable is either single valued or multi-valued. Multi-valued variables are referred to as containers and come in ordered, unordered and record types. See cardinality for more information.
     * @param $baseType The value space from which the variable's value can be drawn (or in the case of containers, from which the individual values are drawn) is identified with a baseType. The baseType selects one of a small set of predefined types that are considered to have atomic values within the runtime data model. Variables with record cardinality have no base-type.
     * @param $normalMaximum The normalMaximum attribute optionally defines the maximum magnitude of numeric outcome variables, it must be a positive value. If given, the outcome's value can be divided by normalMaximum and then truncated (if necessary) to obtain a normalized score in the range [-1.0,1.0]. normalMaximum has no affect on responseProcessing or the values that the outcome variable itself can take.
     * @return ImsQtiWriter
     */
    protected function add_variableDeclaration($tag, $identifier, $cardinality, $baseType){
    	$result = $this->add_element($tag);
    	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('cardinality', $cardinality);
    	$result->set_attribute('baseType', $baseType, false);
    	return $result;
    }
    
    /**
     * 
     * @param $interpretation A human readable interpretation of the default value.
     * @return ImsQtiWriter
     */
    public function add_defaultValue($interpretation=''){
    	$result = $this->add_element('defaultValue');
    	$result->set_attribute('interpretation', $interpretation, false);
    	return $result;
    }
    
    /**
     * A class that can represent a single value of any baseType in variable declarations. The base-type is defined by the baseType attribute of the declaration except in the case of variables with record cardinality. 
     * @param $value 
     * @param $fieldIdentifier This attribute is used for specifying the field identifier for a value that forms part of a record.
     * @param $baseType This attribute is used for specifying the base-type of a value that forms part of a record. 
     * @return ImsQtiWriter
     */
    public function add_value($value, $fieldIdentifier='', $baseType=''){
    	$result = $this->add_element('value', $value);
    	$result->set_attribute('fieldIdentifier', $fieldIdentifier, false);
    	$result->set_attribute('baseType', $baseType, false);
    	return $result;
    }

    /**
     * A special class used to create a mapping from a source set of any baseType to a single float. When mapping containers the result is the sum of the mapped values from the target set. See mapResponse for details.
     * Contains : mapEntry [1..*]
     * The map is defined by a set of mapEntries, each of which maps a single value from the source set onto a single float.
     * @param $lowerBound The lower bound for the result of mapping a container. If unspecified there is no lower-bound.
     * @param $upperBound The upper bound for the result of mapping a container. If unspecified there is no upper-bound.
     * @param $defaultValue The default value from the target set to be used when no explicit mapping for a source value is given.
     * @return ImsQtiWriter
     */
    public function add_mapping($lowerBound='', $upperBound='', $defaultValue=0){
      $result = $this->add_element('mapping');
      if($lowerBound !== ''){//if $lowerBound == 0 write it to the file
      	$result->set_attribute('lowerBound', $lowerBound); 
      }
      if($upperBound !== ''){
      	$result->set_attribute('upperBound', $upperBound);
      }
      $result->set_attribute('defaultValue', $defaultValue);
      return $result;
    }
    
    /**
     * 
     * @param $mapKey The source value
     * @param $mappedValue The mapped value
     * @return ImsQtiWriter
     */
    public function add_mapEntry($mapKey, $mappedValue){
      $result = $this->add_element('mapEntry');
      $result->set_attribute('mapKey', $mapKey);
      $result->set_attribute('mappedValue', $mappedValue);
      return $result;
    }
     
    /**
     * A special class used to create a mapping from a source set of point values to a target set of float values. When mapping containers the result is the sum of the mapped values from the target set. See mapResponsePoint for details. The attributes have the same meaning as the similarly named attributes on mapping.
     * Contains : areaMapEntry [1..*] {ordered}
     * The map is defined by a set of areaMapEntries, each of which maps an area of the coordinate space onto a single float. When mapping points each area is tested in turn, with those listed first taking priority in the case where areas overlap and a point falls in the intersection.
     * @param $lowerBound The lower bound for the result of mapping a container. If unspecified there is no lower-bound.
     * @param $upperBound The upper bound for the result of mapping a container. If unspecified there is no upper-bound.
     * @param $defaultValue The default value from the target set to be used when no explicit mapping for a source value is given.
     * @return ImsQtiWriter
     */
 	public function add_areaMapping($lowerBound='', $upperBound='', $defaultValue=0){
      $result = $this->add_element('areaMapping');
      if($lowerBound !== ''){//if $lowerBound == 0 write it to the file
      	$result->set_attribute('lowerBound', $lowerBound); 
      }
      if($upperBound !== ''){
      	$result->set_attribute('upperBound', $upperBound);
      }
      $result->set_attribute('defaultValue', $defaultValue);
      return $result;
    }

    /**
     * 
     * @param $shape The shape of the area
     * @param $coords The size and position of the area, interpreted in conjunction with the shape.
     * @param $mappedValue The mapped value
     */
    public function add_areaMapEntry($shape, $coords, $mappedValue){
      $result = $this->add_element('areaMapEntry');
      $result->set_attribute('shape', $shape);
      $result->set_attribute('coords', $coords);
      $result->set_attribute('mappedValue', $mappedValue);
      return $result;
    }
    
    
    //END DECLARATION
    
    /**
     * The item body contains the text, graphics, media objects and interactions that describe the item's content and information about how it is structured. The body is presented by combining it with stylesheet information, either explicitly or implicitly using the default style rules of the delivery or authoring system. 
     * The body must be presented to the candidate when the associated itemSession is in the interacting state. In this state, the candidate must be able to interact with each of the visible interactions and therefore set or update the values of the associated responseVariables. The body may be presented to the candidate when the item session is in the closed or review state. In these states, although the candidate's responses should be visible, the interactions must be disabled so as to prevent the candidate from setting or updating the values of the associated response variables. Finally, the body may be presented to the candidate in the solution state, in which case the correct values of the response variables must be visible and the associated interactions disabled.
     * The content model employed by this specification uses many concepts taken directly from [XHTML]. In effect, this part of the specification defines a profile of XHTML. Only some of the elements defined in XHTML are allowable in an assessmentItem and of those that are, some have additional constraints placed on their attributes. Finally, this specification defines some new elements which are used to represent the interactions and to control the display of Integrated Feedback and content restricted to one or more of the defined content views.
     * @return ImsQtiWriter
     */
    public function add_itemBody($xml=''){
    	$result = $this->add_element('itemBody');
    	$result->add_xml($xml);
    	return $result;
    }
   
    /**
     * Modal feedback is shown to the candidate directly following response processing. The value of an outcomeVariable is used in conjunction with the showHide and identifier attributes to determine whether or not the feedback is shown in a similar way to feedbackElement.
     * @param $outcomeIdentifier
     * @param $identifier
     * @param $showHide
     * @param $title Delivery engines are not required to present the title to the candidate but may do so, for example as the title of a modal pop-up window. 
     * @return ImsQtiWriter
     */
    public function add_modalFeedback($outcomeIdentifier=self::FEEDBACK, $identifier, $showHide='show', $title=''){
    	$result = $this->add_element('modalFeedback');
    	$result->set_attribute('outcomeIdentifier', $outcomeIdentifier);
    	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('showHide', $showHide);
    	$result->set_attribute('title', $title, false);
    	return $result;
    }

    // CONTENT MODEL
    
    /**
     * A prompt must not contain any nested interactions.
     * @return ImsQtiWriter
     */
    public function add_prompt($text){
      if(!empty($text)){
        $result = $this->add_element('prompt', $text);
        return $result;
      }else{
        return $this;
      }
    }
    
    /**
     * @return ImsQtiWriter
     */
    public function add_flow($xml){
    	$this->add_xml($xml);
    	return $this;
    }
    
     /**
     * @return ImsQtiWriter
     */
    public function add_span($text=''){
    	$result = $this->add_element('span', $text);
    	return $result;
    }
 
    /**
     * 
     * @param string $data The data attribute provides a URI for locating the data associated with the object.
     * @param mimeType $type
     * @param length $width
     * @param length $height
     * @return unknown
     * @return ImsQtiWriter
     */
    public function add_object($data, $type, $width='', $height=''){
    	$result = $this->add_element('object');
      	$result->set_attribute('data', $data);
      	$result->set_attribute('type', $type);
      	$result->set_attribute('width', $width, false);
      	$result->set_attribute('height', $height, false);
    	return $result;
    }
    
    //END CONTENT MODEL
    
    //VARIABLE CONTENT

    /**
     * A feedback element that forms part of a Non-adaptive Item must not contain an interaction object, either directly or indirectly.
     * When an interaction is contained in a hidden feedback element it must also be hidden. The candidate must not be able to set or update the value of the associated responseVariable.
     * @param $outcomeIdentifier
     * @param $identifier
     * @param $showHide
     * @param $title
     * @return ImsQtiWriter
     */
    public function add_feedbackInline($outcomeIdentifier=self::FEEDBACK, $identifier, $showHide='show'){
      $result = $this->add_element('feedbackInline');
      $result->set_attribute('outcomeIdentifier', $outcomeIdentifier);
      $result->set_attribute('identifier', $identifier);
      $result->set_attribute('showHide', $showHide);
      return $result;
    }
    
    /**
     * A rubric block identifies part of an assessmentItem's itemBody that represents instructions to one or more of the actors that view the item. Although rubric blocks are defined as simpleBlocks they must not contain interactions.
     * @param $view_ The views in which the rubric block's content are to be shown.
     * @return ImsQtiWriter
     */
    public function add_rubricBlock($view_, $class = '', $id='', $lang='', $label = ''){
      $result = $this->add_element('rubricBlock');
      $result->set_attribute('class', $class, false);
      $result->set_attribute('label', $label, false);
      $result->set_attribute('id', $id, false);
      $result->set_attribute('lang', $lang, false);
      $func_args = func_get_args();  
      $result->set_attribute('view', implode(' ', $func_args));  //Bug fix #1335
      //$result->set_attribute('view', implode(' ', func_get_args()));
      return $result;
      
    }
        
    /**
     * 
     * @param $identifier
     * The outcomeVariable or templateVariable that must have been defined and have single cardinality. The values of responseVariables cannot be printed directly as their values are implicitly known to the candidate through the interactions they are bound to. If necessary, their values can be assigned to outcomeVariables during responseProcessing and displayed to the candidate as part of a bodyElement visible only in the appropriate feedback states.
     * If the variable's value is NULL then the element is ignored.
     * Variables of baseType string are treated as simple runs of text.
     * Variables of baseType integer or float are converted to runs of text (strings) using the formatting rules described below. Float values should only be formatted in the e, E, f, g, G, r or R styles..
     * Variables of baseType duration are treated as floats, representing the duration in seconds.
     * @param $format The format conversion specifier to use when converting numerical values to strings. See Number Formatting Rules for details. 
     * @param $base The number base to use when converting integer variables to strings with the i conversion type code.
     * Variables of baseType file are rendered using a control that enables the user to open the file. The control should display the name associated with the file, if any.
     * Variables of baseType uri are rendered using a control that enables the user to open the identified resource, for example, by following a hypertext link in the case of a URL.
     * @return ImsQtiWriter
     */
    public function add_printedVariable($identifier, $format='', $base){
      $result = $this->add_element('printedVariable');
      $result->set_attribute('identifier', $identifier);
      $result->set_attribute('format', $format, false);
      $result->set_attribute('base', $base , false);
      return $result;
      
    }
    
    //END VARIABLE CONTENT

    //INTERACTIONS 

    /**
     * An extended text interaction is a blockInteraction that allows the candidate to enter an extended amount of text.
     * The extendedTextInteraction must be bound to a responseVariable with baseType of string, integer or float. When bound to response variable with single cardinality a single string of text is required from the candidate. When bound to a response variable with multiple or ordered cardinality several separate text strings may be required, see maxStrings below.
     * @param $responseIdentifier  The identifier of the choice. This identifier must not be used by any other choice or item variable.
     * @param $expectedLength The expectedLength attribute provides a hint to the candidate as to the expected overall length of the desired response. A Delivery Engine should use the value of this attribute to set the size of the response box, where applicable.
     * @param $expectedLines The expectedLines attribute provides a hint to the candidate as to the expected number of lines of input required. A Delivery Engine should use the value of this attribute to set the size of the response box, where applicable.
     * @param $maxStrings The maxStrings attribute is required when the interaction is bound to a response variable that is a container. A Delivery Engine must use the value of this attribute to control the maximum number of separate strings accepted from the candidate. When multiple strings are accepted, expectedLength applies to each string.
     * @param $placeholderText In visual environments, string interactions are typically represented by empty boxes into which the candidate writes or types. However, in speech based environments it is helpful to have some placeholder text that can be used to vocalize the interaction. Delivery engines should use the value of this attribute (if provided) instead of their default placeholder text when this is required. Implementors should be aware of the issues concerning the use of default values described in the section on responseVariables. 
     * @return ImsQtiWriter
     */
    public function add_extendedTextInteraction($responseIdentifier=self::RESPONSE, $expectedLength='', $expectedLines='', $maxStrings='', $placeholderText='', $class = '', $id='', $lang='', $label = ''){
      	$result = $this->add_element('extendedTextInteraction');
      	$result->set_attribute('responseIdentifier', $responseIdentifier, false);
      	$result->set_attribute('expectedLength', $expectedLength, false);
      	$result->set_attribute('expectedLines', $expectedLines, false);
      	$result->set_attribute('maxStrings', $maxStrings, false);
      	$result->set_attribute('placeholderText', $placeholderText, false);
      	$result->set_attribute('class', $class, false);
      	$result->set_attribute('id', $id, false);
      	$result->set_attribute('lang', $lang, false);
      	$result->set_attribute('label', $label, false);
      	return $result;
    }
    
    /**
     * In an order interaction the candidate's task is to reorder the choices, the order in which the choices are displayed initially is significant.
     * If a default value is specified for the response variable associated with an order interaction then its value should be used to override the order of the choices specified here.
     * By its nature, an order interaction may be difficult to render in an unanswered state so implementors should be aware of the issues concerning the use of default values described in the section on responseVariables.
     * The orderInteraction must be bound to a responseVariable with a baseType of identifier and ordered cardinality only.
     * @param unknown_type $responseIdentifier The identifier of the choice. This identifier must not be used by any other choice or item variable.
     * @param bool $shuffle If the shuffle attribute is true then the delivery engine must randomize the order in which the choices are initially presented subject to the fixed attribute.
     * @param orientation $orientation The orientation attribute provides a hint to rendering systems that the ordering has an inherent vertical or horizontal interpretation.
     * @return ImsQtiWriter
     */
    public function add_orderInteraction($responseIdentifier=self::RESPONSE, $shuffle=false, $orientation='', $class = '', $id='', $lang='', $label = ''){
      	$result = $this->add_element('orderInteraction');
      	$result->set_attribute('responseIdentifier', $responseIdentifier, false);
      	$result->set_attribute('shuffle', $shuffle ? 'true': 'false');
      	$result->set_attribute('orientation', $orientation, false);
      	$result->set_attribute('class', $class, false);
      	$result->set_attribute('id', $id, false);
      	$result->set_attribute('lang', $lang, false);
      	$result->set_attribute('label', $label, false);
      return $result;
    }
 
    /**
     * A hotspot interaction is a graphical interaction with a corresponding set of choices that are defined as areas of the graphic image. The candidate's task is to select one or more of the areas (hotspots). The hotspot interaction should only be used when the spatial relationship of the choices with respect to each other (as represented by the graphic image) is important to the needs of the item. Otherwise, choiceInteraction should be used instead with separate material for each option.
     * The delivery engine must clearly indicate the selected area(s) of the image and may also indicate the unselected areas as well. Interactions with hidden hotspots are achieved with the selectPointInteraction.
     * The hotspot interaction must be bound to a responseVariable with a baseType of identifier and single or multiple cardinality.
     * @param $responseIdentifier  The identifier of the choice. This identifier must not be used by any other choice or item variable.
     * @param $maxChoices The maximum number of choices that the candidate is allowed to select. If maxChoices is 0 there is no restriction. If maxChoices is greater than 1 (or 0) then the interaction must be bound to a response with multiple cardinality.
     * @return ImsQtiWriter
     */
    public function add_hotspotInteraction($responseIdentifier=self::RESPONSE, $maxChoices=1, $class = '', $id='', $lang='', $label = ''){
      	$result = $this->add_element('hotspotInteraction');
      	$result->set_attribute('responseIdentifier', $responseIdentifier, false);
    	$result->set_attribute('maxChoices', $maxChoices);
      	$result->set_attribute('class', $class, false);
      	$result->set_attribute('id', $id, false);
      	$result->set_attribute('lang', $lang, false);
      	$result->set_attribute('label', $label, false);
      	return $result;
    }
    
    /**
     * Like hotspotInteraction, a select point interaction is a graphic interaction. The candidate's task is to select one or more points. The associated response may have an areaMapping that scores the response on the basis of comparing it against predefined areas but the delivery engine must not indicate these areas of the image. Only the actual point(s) selected by the candidate shall be indicated.
     * The select point interaction must be bound to a responseVariable with a baseType of point and single or multiple cardinality.
     * @param $responseIdentifier  The identifier of the choice. This identifier must not be used by any other choice or item variable.
     * @param $maxChoices The maximum number of choices that the candidate is allowed to select. If maxChoices is 0 there is no restriction. If maxChoices is greater than 1 (or 0) then the interaction must be bound to a response with multiple cardinality.
     * @return ImsQtiWriter
     */
    public function add_selectPointInteraction($responseIdentifier=self::RESPONSE, $maxChoices=1, $class = '', $id='', $lang='', $label = ''){
      	$result = $this->add_element('selectPointInteraction');
      	$result->set_attribute('responseIdentifier', $responseIdentifier, false);
    	$result->set_attribute('maxChoices', $maxChoices);
      	$result->set_attribute('class', $class, false);
      	$result->set_attribute('id', $id, false);
      	$result->set_attribute('lang', $lang, false);
      	$result->set_attribute('label', $label, false);
      	return $result;
    }    
    
    /**
     * Contains : object [1] The image to be used as a stage onto which individual positionObjectInteractions allow the candidate to place their objects.
     * Contains : positionObjectInteraction [1..*]
     * @return ImsQtiWriter
     */
    public function add_positionObjectStage(){      	
    	$result = $this->add_element('positionObjectStage');
      	return $result;
    }

    /**
     * The position object interaction consists of a single image which must be positioned on another graphic image (the stage) by the candidate. Like selectPointInteraction, the associated response may have an areaMapping that scores the response on the basis of comparing it against predefined areas but the delivery engine must not indicate these areas of the stage. Only the actual position(s) selected by the candidate shall be indicated.
     * The position object interaction must be bound to a responseVariable with a baseType of point and single or multiple cardinality. The point records the coordinates, with respect to the stage, of the center point of the image being positioned.
     * Contains : object [1] The image to be positioned on the stage by the candidate.
     * @param $responseIdentifier  The identifier of the choice. This identifier must not be used by any other choice or item variable.
     * @param $maxChoices The maximum number of choices that the candidate is allowed to select. If maxChoices is 0 there is no restriction. If maxChoices is greater than 1 (or 0) then the interaction must be bound to a response with multiple cardinality.
     * @param $centerPoint The centrePoint attribute defines the point on the image being positioned that is to be treated as the center as an offset from the top-left corner of the image in horizontal, vertical order. By default this is the center of the image's bounding rectangle. The stage on which the image is to be positioned may be shared amongst several position object interactions and is therefore defined in a class of its own: positionObjectStage.
     * @return ImsQtiWriter
     */
    public function add_positionObjectInteraction($responseIdentifier=self::RESPONSE, $maxChoices=1, $centerPoint='', $class = '', $id='', $lang='', $label = ''){
      	$result = $this->add_element('positionObjectInteraction');
      	$result->set_attribute('responseIdentifier', $responseIdentifier, false);
    	$result->set_attribute('maxChoices', $maxChoices);
    	$result->set_attribute('centerPoint', $centerPoint, false);
      	$result->set_attribute('class', $class, false);
      	$result->set_attribute('id', $id, false);
      	$result->set_attribute('lang', $lang, false);
      	$result->set_attribute('label', $label, false);
      	return $result;
    }
    
    /**
     * Some of the graphic interactions involve images with specially defined areas or hotspots.
     * @param $identifier  The identifier of the choice. This identifier must not be used by any other choice or item variable.
     * @param $shape The shape of the hotspot.
     * @param $coords The size and position of the hotspot, interpreted in conjunction with the shape.
     * @param $hotspotLabel The alternative text for this (hot) area of the image, if specified it must be treated in the same way as alternative text for img. For hidden hotspots this label is ignored.
     * @param $fixed If fixed is true for a choice then the position of this choice within the interaction must not be changed by the delivery engine even if the immediately enclosing interaction supports the shuffling of choices. If no value is specified then the choice is free to be shuffled.
     * @return ImsQtiWriter
     */
    public function add_hotspotChoice($identifier, $shape, $coords, $hotspotLabel='', $fixed = false){
    	$result = $this->add_element('hotspotChoice');
    	$result->set_attribute('identifier', $identifier);
      	$result->set_attribute('shape', $shape);
      	$result->set_attribute('coords', $coords);
    	$result->set_attribute('hotspotLabel', $hotspotLabel, false);
    	$result->set_attribute('fixed', $fixed?'true':'false');
    	return $result;
    }   
    
    /**
     * The upload interaction allows the candidate to upload a pre-prepared file representing their response. It must be bound to a responseVariable with base-type file and single cardinality.
     * @param $responseIdentifier  The identifier of the choice. This identifier must not be used by any other choice or item variable.
     * @param mimeType $type The expected mime-type of the uploaded file.
     * @return ImsQtiWriter
     */
    public function add_uploadInteraction($responseIdentifier=self::RESPONSE, $type = '', $class = '', $id='', $lang='', $label = ''){ 
    	$result = $this->add_element('uploadInteraction');
      	$result->set_attribute('responseIdentifier', $responseIdentifier, false);
      	$result->set_attribute('type', $type, false);
      	$result->set_attribute('class', $class, false);
      	$result->set_attribute('id', $id, false);
      	$result->set_attribute('lang', $lang, false);
      	$result->set_attribute('label', $label, false);
      	return $result;
    }
       
    /**
     * The choice interaction presents a set of choices to the candidate. The candidate's task is to select one or more of the choices, up to a maximum of maxChoices. There is no corresponding minimum number of choices. The interaction is always initialized with no choices selected. 
     * The choiceInteraction must be bound to a responseVariable with a baseType of identifier and single or multiple cardinality.
     * Contains : simpleChoice [1..*]
     * An ordered list of the choices that are displayed to the user. The order is the order of the choices presented to the user unless shuffle is true.
     * @param $responseIdentifier
     * @param $maxChoices The maximum number of choices that the candidate is allowed to select. If maxChoices is 0 then there is no restriction. If maxChoices is greater than 1 (or 0) then the interaction must be bound to a response with multiple cardinality. 
     * @param $shuffle If the shuffle attribute is true then the delivery engine must randomize the order in which the choices are presented subject to the fixed attribute.
     * @param $label The label attribute provides authoring systems with a mechanism for labeling elements of the content model with application specific data. If an item uses labels then values for the associated toolName and toolVersion attributes must also be provided.
     * @return ImsQtiWriter
     */
    public function add_choiceInteraction($responseIdentifier=self::RESPONSE, $maxChoices=1, $shuffle=false, $class = '', $id='', $lang='', $label = ''){
    	$result = $this->add_element('choiceInteraction');
    	$result->set_attribute('responseIdentifier', $responseIdentifier);
    	$result->set_attribute('shuffle', $shuffle?'true':'false');
    	$result->set_attribute('maxChoices', $maxChoices);
      	$result->set_attribute('class', $class, false);
      	$result->set_attribute('id', $id, false);
      	$result->set_attribute('lang', $lang, false);
      	$result->set_attribute('label', $label, false);
    	return $result;
    }
    
    /**
     * simpleChoice is a choice that contains flowStatic objects. A simpleChoice must not contain any nested interactions. 
     * @param $identifier The identifier of the choice. This identifier must not be used by any other choice or item variable.
     * @param $fixed If fixed is true for a choice then the position of this choice within the interaction must not be changed by the delivery engine even if the immediately enclosing interaction supports the shuffling of choices. If no value is specified then the choice is free to be shuffled.
     * @return ImsQtiWriter
     */
    public function add_simpleChoice($identifier, $fixed = false){
    	$result = $this->add_element('simpleChoice');
    	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('fixed', $fixed?'true':'false');
    	return $result;
    }
     
    /**
     * A inline choice is an inlineInteraction that presents the user with a set of choices, each of which is a simple piece of text. The candidate's task is to select one of the choices. Unlike the choiceInteraction, the delivery engine must allow the candidate to review their choice within the context of the surrounding text.
     * The inlineChoiceInteraction must be bound to a responseVariable with a baseType of identifier and single cardinality only.
     * Contains : inlineChoice [1..*] An ordered list of the choices that are displayed to the user. The order is the order of the choices presented to the user unless shuffle is true.
     * @param $responseIdentifier The response variable associated with the interaction.
      * @param $shuffle If the shuffle attribute is true then the delivery engine must randomize the order in which the choices are presented subject to the fixed attribute.
     * @return ImsQtiWriter
     */
 	public function add_inlineChoiceInteraction($responseIdentifier=self::RESPONSE, $shuffle=false, $class = '', $id='', $lang='', $label = ''){
    	$result = $this->add_element('inlineChoiceInteraction');
    	$result->set_attribute('responseIdentifier', $responseIdentifier);
    	$result->set_attribute('shuffle', $shuffle?'true':'false');
      	$result->set_attribute('class', $class, false);
      	$result->set_attribute('id', $id, false);
      	$result->set_attribute('lang', $lang, false);
      	$result->set_attribute('label', $label, false);
    	return $result;
    }

    /**
     * A simple run of text to be displayed to the user.
     * @param $identifier  The identifier of the choice. This identifier must not be used by any other choice or item variable.
     * @param $fixed  If fixed is true for a choice then the position of this choice within the interaction must not be changed by the delivery engine even if the immediately enclosing interaction supports the shuffling of choices. If no value is specified then the choice is free to be shuffled.
     */
    public function add_inlineChoice($identifier, $fixed = false){
    	$result = $this->add_element('inlineChoice');
    	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('fixed', $fixed?'true':'false');
    	return $result;
    }
    
    /**
     * A match interaction is a blockInteraction that presents candidates with two sets of choices and allows them to create associates between pairs of choices in the two sets, but not between pairs of choices in the same set. Further restrictions can still be placed on the allowable associations using the matchMax and matchGroup attributes of the choices.
     * The matchInteraction must be bound to a responseVariable with base-type directedPair and either single or multiple cardinality.
     * Contains : simpleMatchSet [2]
     * The two sets of choices, the first set defines the source choices and the second set the targets.
     * @param $responseIdentifier The response variable associated with the interaction.
     * @param $maxAssociations The maximum number of associations that the candidate is allowed to make. If maxAssociations is 0 then there is no restriction. If maxAssociations is greater than 1 (or 0) then the interaction must be bound to a response with multiple cardinality. 
     * @param $shuffle If the shuffle attribute is true then the delivery engine must randomize the order in which the choices are presented within each set, subject to the fixed attribute of the choices themselves.
     * @return ImsQtiWriter
     */
    public function add_matchInteraction($responseIdentifier=self::RESPONSE, $maxAssociations=1, $shuffle=false, $class = '', $id='', $lang='', $label = ''){
    	$result = $this->add_element('matchInteraction');
    	$result->set_attribute('responseIdentifier', $responseIdentifier);
    	$result->set_attribute('shuffle', $shuffle?'true':'false');
    	$result->set_attribute('maxAssociations', $maxAssociations);
      	$result->set_attribute('class', $class, false);
      	$result->set_attribute('id', $id, false);
      	$result->set_attribute('lang', $lang, false);
      	$result->set_attribute('label', $label, false);
    	return $result;
    }
    
	  /** 
	   * Contains : simpleAssociableChoice [*]
	   * An ordered set of choices for the set.
     * @return ImsQtiWriter
     */
    public function add_simpleMatchSet(){
    	$result = $this->add_element('simpleMatchSet');
    	return $result;
    }

    /**
     * Contains : flowStatic [*]
     * associableChoice is a choice that contains flowStatic objects, it must not contain nested interactions.
     * @param $identifier The response variable associated with the interaction.
     * @param $fixed
     * @param $matchGroups
     * @param $matchMax The maximum number of choices this choice may be associated with. If matchMax is 0 then there is no restriction. 
     * @return ImsQtiWriter
     */
    public function add_simpleAssociableChoice($identifier, $fixed = false, $matchGroups = array(), $matchMax=1){
    	$matchGroups = empty($matchGroups) ? array() : $matchGroups;
    	
    	$result = $this->add_element('simpleAssociableChoice');
    	$result->set_attribute('identifier', $identifier);
    	$result->set_attribute('fixed', $fixed?'true':'false', false);
    	foreach($matchGroups as $matchGroup){
    		$result->add_attribute('matchGroup', $matchGroup);
    	}
    	$result->set_attribute('matchMax', $matchMax);
    	return $result;
    }
    
    /**
     * 
     * @param identifier $responseIdentifier The response variable associated with the interaction.
     * @param integer $base If the string interaction is bound to a numeric response variable then the base attribute must be used to set the number base in which to interpret the value entered by the candidate.
     * @param identifier $stringIdentifier If the string interaction is bound to a numeric response variable then the actual string entered by the candidate can also be captured by binding the interaction to a second response variable (of base-type string).
     * @param integer $expectedLength The expectedLength attribute provides a hint to the candidate as to the expected overall length of the desired response. A Delivery Engine should use the value of this attribute to set the size of the response box, where applicable.
     * @param string $patternMask If given, the pattern mask specifies a regular expression that the candidate's response must match in order to be considered valid. The regular expression language used is defined in Appendix F of [XML_SCHEMA2].
     * @param string $placeholderText In visual environments, string interactions are typically represented by empty boxes into which the candidate writes or types. However, in speech based environments it is helpful to have some placeholder text that can be used to vocalize the interaction. Delivery engines should use the value of this attribute (if provided) instead of their default placeholder text when this is required. Implementors should be aware of the issues concerning the use of default values described in the section on responseVariables. 
     * @return ImsQtiWriter
     */
    public function add_textEntryInteraction($responseIdentifier=self::RESPONSE, $base = '', $stringIdentifier = '', $expectedLength='', $patternMask='', $placeholderText='', $class = '', $id='', $lang='', $label = ''){
    	$result = $this->add_element('textEntryInteraction');
    	$result->set_attribute('responseIdentifier', $responseIdentifier);
    	$result->set_attribute('base', $base, false);
    	$result->set_attribute('stringIdentifier', $stringIdentifier, false);
    	$result->set_attribute('expectedLength', $expectedLength, false);
    	$result->set_attribute('patternMask', $patternMask, false);
    	$result->set_attribute('placeholderText', $placeholderText, false);
      	$result->set_attribute('class', $class, false);
      	$result->set_attribute('id', $id, false);
      	$result->set_attribute('lang', $lang, false);
      	$result->set_attribute('label', $label, false);
    	return $result;
    }
 	
    /**
     * The slider interaction presents the candidate with a control for selecting a numerical value between a lower and upper bound. It must be bound to a response variable with single cardinality with a base-type of either integer or float.
     * Note that a slider interaction does not have a default or initial position except where specified by a default value for the associated responseVariable. The currently selected value, if any, must be clearly indicated to the candidate .
     * @param identifier $responseIdentifier The response variable associated with the interaction.
     * @param float $lowerBound If the associated response variable is of type integer then the lowerBound must be rounded down to the greatest integer less than or equal to the value given.
     * @param float $upperBound If the associated response variable is of type integer then the upperBound must be rounded up to the least integer greater than or equal to the value given.
     * @param $step The steps that the control moves in. For example, if the lowerBound and upperBound are [0,10] and step is 2 then the response would be constrained to the set of values {0,2,4,6,8,10}. If bound to an integer response the default step is 1, otherwise the slider is assumed to operate on an approximately continuous scale.
     * @param $stepLabel By default, sliders are labeled only at their ends. The stepLabel attribute controls whether or not each step on the slider should also be labeled. It is unlikely that delivery engines will be able to guarantee to label steps so this attribute should be treated only as request.
     * @param $orientation The orientation attribute provides a hint to rendering systems that the slider is being used to indicate the value of a quantity with an inherent vertical or horizontal interpretation. For example, an interaction that is used to indicate the value of height might set the orientation to vertical to indicate that rendering it horizontally could spuriously increase the difficulty of the item.
     * @param $reverse The reverse attribute provides a hint to rendering systems that the slider is being used to indicate the value of a quantity for which the normal sense of the upper and lower bounds is reversed. For example, an interaction that is used to indicate a depth below sea level might specify both a vertical orientation and set reverse.
     * @return ImsQtiWriter
     */
    public function add_sliderInteraction($responseIdentifier, $lowerBound, $upperBound, $step='', $stepLabel='', $orientation='', $reverse='', $class = '', $id='', $lang='', $label = ''){
    	$result = $this->add_element('sliderInteraction');
    	$result->set_attribute('responseIdentifier', $responseIdentifier);
    	$result->set_attribute('lowerBound', $lowerBound);
    	$result->set_attribute('upperBound', $upperBound);
    	$result->set_attribute('step', $step, false);
    	$result->set_attribute('stepLabel', $stepLabel, false);
    	$result->set_attribute('orientation', $orientation, false);
    	$result->set_attribute('reverse', $reverse, false);
      	$result->set_attribute('class', $class, false);
      	$result->set_attribute('id', $id, false);
      	$result->set_attribute('lang', $lang, false);
      	$result->set_attribute('label', $label, false);
    	return $result;
    	
    }
    
    // END INTERACTIONS
    
    // RESPONSE PROCESSING 
    
    /**
     * Map Response
     * rptemplates/map_response.xml
     * Full template URI: http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response 
     * The map response processing template uses the mapResponse operator to map the value of a response variable RESPONSE onto a value for the outcome SCORE. Both variables must have been declared and RESPONSE must have an associated mapping. The template applies to responses of any baseType and cardinality. See the notes about mapResponse for details of its behavior when applied to containers.
     * If RESPONSE was NULL the SCORE is set to 0.
     * @return ImsQtiWriter
     */
    public function add_standard_response_map_response($response = self::RESPONSE, $score = self::SCORE){
		$result = $this->add_responseCondition();
		$if = $result->add_responseIf();
		$if->add_isNull()->add_variable($response);
		$if->add_setOutcomeValue($score)->add_baseValue(self::BASETYPE_FLOAT, 0);
		$result->add_responseElse()->add_setOutcomeValue($score)->add_mapResponse($response);
		return $result;
    }
    
	public function add_standard_response_match_correct($response = self::RESPONSE, $score = self::SCORE){
      $result = $this->add_responseCondition();
      $if = $result->add_responseIf();
      	$match = $if->add_match();
      	$match->add_variable($response);
      	$match->add_correct($response);
      	$if->add_setOutcomeValue($score)->add_baseValue(Qti::BASETYPE_INTEGER, 1);
      $else = $result->add_responseElse();
      	$else->add_setOutcomeValue($score)->add_baseValue(Qti::BASETYPE_INTEGER, 0);
      return $result;
    }
    
    public function add_standard_response_assign_feedback($response = self::RESPONSE, $feedback=self::FEEDBACK){
    	$result = $this->add_setOutcomeValue($feedback)->add_variable($response);
    	return $result;
    }
    
    public function add_standard_response_map_response_point($response = self::RESPONSE, $score = self::SCORE){
    	$result = $this->add_responseCondition();
      	$if = $result->add_responseIf();
      	$if->add_isNull()->add_variable($response);
      	$if->add_setOutcomeValue($score)->add_baseValue(self::BASETYPE_FLOAT, 0);
      	$result->add_responseElse()->add_setOutcomeValue($score)->add_mapResponsePoint($response);
      	return $result;
    }
    
    /**
     * Contains : responseRule [*]
     * The mapping from values assigned to Response Variables by the candidate onto appropriate values for the item's Outcome Variables is achieved through a number of rules.
     * @param $template If a template identifier is given it may be used to locate an externally defined responseProcessing template. The rules obtained from the external template may be used instead of the rules defined within the item itself, though if both are given the internal rules are still preferred.
     * @param $templateLocation In practice, the template attribute may well contain a URN or the URI of a template stored on a remote web server, such as the standard response processing templates defined by this specification. When processing an assessmentItem tools working offline will not be able to obtain the template from a URN or remote URI. The templateLocation attribute provides an alternative URI, typically a relative URI to be resolved relative to the location of the assessmentItem itself, that can be used to obtain a copy of the response processing template.
     * @return ImsQtiWriter
     */
    public function add_responseProcessing($template='', $templateLocation=''){
    	$result = $this->add_element('responseProcessing');
    	$result->set_attribute('template', $template, false);
    	$result->set_attribute('templateLocation', $templateLocation, false);
    	return $result;
    }

    /**
     * Contains : expression [1]
     * An expression which must have an effective baseType and cardinality that matches the base-type and cardinality of the outcomeVariable being set.
     * The setOutcomeValue rule sets the value of an outcomeVariable to the value obtained from the associated expression. An outcome variable can be updated with reference to a previously assigned value, in other words, the outcomeVariable being set may appear in the expression where it takes the value previously assigned to it.
     * Special care is required when using the numeric base-types because floating point values can not be assigned to integer variables and vice-versa. The truncate, round or integerToFloat operators must be used to achieve numeric type conversion.
     * @param $identifier The outcomeVariable to be set.
     * @return ImsQtiWriter
     */
    public function add_setOutcomeValue($identifier){
    	$result = $this->add_element('setOutcomeValue');
    	$result->set_attribute('identifier', $identifier);
    	return $result;
    }
    
    /**
     * This expression looks up the value of an itemVariable that has been declared in a corresponding variableDeclaration or is one of the built-in variables. The result has the base-type and cardinality declared for the variable.
     * @param $identifier
     * @return ImsQtiWriter
     */
    public function add_variable($identifier){
    	$result = $this->add_element('variable');
    	$result->set_attribute('identifier', $identifier);
    	return $result;
    }
    
    /**
     * Contains : responseIf [1]
     * Contains : responseElseIf [*]
     * Contains : responseElse [0..1]
     * If the expression given in a responseIf or responseElseIf evaluates to true then the sub-rules contained within it are followed and any following responseElseIf or responseElse parts are ignored for this response condition.
     * If the expression given in a responseIf or responseElseIf does not evaluate to true then consideration passes to the next responseElseIf or, if there are no more responseElseIf parts then the sub-rules of the responseElse are followed (if specified).
     * @return ImsQtiWriter
     */
    public function add_responseCondition(){
    	$result = $this->add_element('responseCondition');
    	return $result;
    }
    
    /**
     * Contains : expression [1]
     * Contains : responseRule [*]
     * A responseIf part consists of an expression which must have an effective baseType of boolean and single cardinality. For more information about the runtime data model employed see Expressions. It also contains a set of sub-rules. If the expression is true then the sub-rules are processed, otherwise they are skipped (including if the expression is NULL) and the following responseElseIf or responseElse parts (if any) are considered instead.
     * @return ImsQtiWriter
     */
    public function add_responseIf(){
    	$result = $this->add_element('responseIf');
    	return $result;
    }
    
	  /**
	   * Contains : expression [1]
	   * Contains : responseRule [*]
	   * responseElseIf is defined in an identical way to responseIf.
     * @return ImsQtiWriter
     */
    public function add_responseElseIf(){
    	$result = $this->add_element('responseElseIf');
    	return $result;
    }
    
	  /**
	   * Contains : responseRule [*]
     * @return ImsQtiWriter
     */
    public function add_responseElse(){
    	$result = $this->add_element('responseElse');
    	return $result;
    }

    /**
     * This expression looks up the value of a responseVariable and then transforms it using the associated mapping, which must have been declared. The result is a single float. If the response variable has single cardinality then the value returned is simply the mapped target value from the map. If the response variable has single or multiple cardinality then the value returned is the sum of the mapped target values. This expression cannot be applied to variables of record cardinality. 
     * For example, if a mapping associates the identifiers {A,B,C,D} with the values {0,1,0.5,0} respectively then mapResponse will map the single value 'C' to the numeric value 0.5 and the set of values {C,B} to the value 1.5.
     * If a container contains multiple instances of the same value then that value is counted once only. To continue the example above {B,B,C} would still map to 1.5 and not 2.5.
     * @param $identifier
     * @return ImsQtiWriter
     */
    public function add_mapResponse($identifier){
      $result = $this->add_element('mapResponse');
      $result->set_attribute('identifier', $identifier);
      return $result;
    }
    
    /**
     * A human readable interpretation of the correct value.
     * @param $interpretation
     * @return ImsQtiWriter
     */
    public function add_correctResponse($interpretation=''){
      $result = $this->add_element('correctResponse');
      $result->set_attribute('interpretation', $interpretation, false);
      return $result;
    }
    
    //EXPRESSIONS
    

    /**
     * The anyN operator takes one or more sub-expressions each with a base-type of boolean and single cardinality. The result is a single boolean which is true if at least min of the sub-expressions are true and at most max of the sub-expressions are true. If more than n - min sub-expressions are false (where n is the total number of sub-expressions) or more than max sub-expressions are true then the result is false. If one or more sub-expressions are NULL then it is possible that neither of these conditions is satisfied, in which case the operator results in NULL. For example, if min is 3 and max is 4 and the sub-expressions have values {true,true,false,NULL} then the operator results in NULL whereas {true,false,false,NULL} results in false and {true,true,true,NULL} results in true. The result NULL indicates that the correct value for the operator cannot be determined.
     * @param $min The minimum number of sub-expressions that must be true.
     * @param $max The maximum number of sub-expressions that may be true.
     * @return ImsQtiWriter
     */
    public function add_anyN($min , $max){
      $result = $this->add_element('anyN');
      $result->set_attribute('min', $min);
      $result->set_attribute('max', $max);
      return $result;
    }
    
    /**
     * The contains operator takes two sub-expressions which must both have the same base-type and cardinality - either multiple or ordered. The result is a single boolean with a value of true if the container given by the first sub-expression contains the value given by the second sub-expression and false if it doesn't. Note that the contains operator works differently depending on the cardinality of the two sub-expressions. For unordered containers the values are compared without regard for ordering, for example, [A,B,C] contains [C,A]. Note that [A,B,C] does not contain [B,B] but that [A,B,B,C] does. For ordered containers the second sub-expression must be a strict sub-sequence within the first. In other words, [A,B,C] does not contain [C,A] but it does contain [B,C].
     * If either sub-expression is NULL then the result of the operator is NULL. Like the member operator, the contains operator should not be used on sub-expressions with a base-type of float and must not be used on sub-expressions with a base-type of duration.
     * @return ImsQtiWriter
     */
    public function add_contains(){
      $result = $this->add_element('contains');
      return $result;
    } 
    
    /**
     * This expression looks up the declaration of a responseVariable and returns the associated correctResponse or NULL if no correct value was declared.
     * @param $identifier
     * @return ImsQtiWriter
     */
     public function add_correct($identifier){
      $result = $this->add_element('correct');
      $result->set_attribute('identifier', $identifier);
      return $result;
    } 
    
    /**
     * This expression looks up the declaration of an itemVariable and returns the associated defaultValue or NULL if no default value was declared.
     * @param $identifier
     * @return ImsQtiWriter
     */
    public function add_default($identifier){
      $result = $this->add_element('default');
      $result->set_attribute('identifier', $identifier);
      return $result;
    } 
    
    /**
     * The delete operator takes two sub-expressions which must both have the same base-type. The first sub-expression must have single cardinality and the second must be a multiple or ordered container. The result is a new container derived from the second sub-expression with all instances of the first sub-expression removed. For example, when applied to A and {B,A,C,A} the result is the container {B,C}.
     * @return ImsQtiWriter
     */
    public function add_delete(){
      $result = $this->add_element('delete');
      return $result;
    } 
    
    /**
     * The durationGTE operator takes two sub-expressions which must both have single cardinality and base-type duration. The result is a single boolean with a value of true if the first duration is longer (or equal, within the limits imposed by truncation as described above) than the second and false if it is shorter than the second. If either sub-expression is NULL then the operator results in NULL.
     * See durationLT for more information about testing the equality of durations.
     * @return ImsQtiWriter
     */
    public function add_durationGTE(){
      $result = $this->add_element('durationGTE');
      return $result;
    } 
    
    /**
     * The durationLT operator takes two sub-expressions which must both have single cardinality and base-type duration. The result is a single boolean with a value of true if the first duration is shorter than the second and false if it is longer than (or equal) to the second. If either sub-expression is NULL then the operator results in NULL.
     * There is no 'durationLTE' or 'durationGT' because equality of duration is meaningless given the variable precision allowed by duration. Given that duration values are obtained by truncation rather than rounding it makes sense to test only less-than or greater-than-equal inequalities only. For example, if we want to determine if a candidate took less than 10 seconds to complete a task in a system that reports durations to a resolution of epsilon seconds (epsilon<1) then a value equal to 10 would cover all durations in the range [10,10+epsilon).
     * @return ImsQtiWriter
     */
    public function add_durationLT(){
      $result = $this->add_element('durationLT');
      return $result;
    } 
    
    /**
     * The equalRounded operator takes two sub-expressions which must both have single cardinality and have a numerical base-type. The result is a single boolean with a value of true if the two expressions are numerically equal after rounding and false if they are not. If either sub-expression is NULL then the operator results in NULL.
     * @param $roundingMode Numbers are rounded to a given number of significantFigures or decimalPlaces. 
     * @param $figures The number of figures to round to. For example, if the two values are 1.56 and 1.6 and significantFigures mode is used with figures=2 then the result would be true.
     * @return ImsQtiWriter
     */
    public function add_equalRounded($roundingMode, $figures){
      $result = $this->add_element('equalRounded');
      $result->set_attribute('roundingMode', $roundingMode);
      $result->set_attribute('figures', $figures);
      return $result;
    } 
    
    /**
     * The field-value operator takes a sub-expression with a record container value. The result is the value of the field with the specified fieldIdentifier. If there is no field with that identifier then the result of the operator is NULL.
     * @param $fieldIdentifier The identifier of the field to be selected. 
     * @return ImsQtiWriter
     */
    public function add_fieldValue($fieldIdentifier){
      $result = $this->add_element('fieldValue');
      $result->set_attribute('fieldIdentifier', $fieldIdentifier);
      return $result;
    }    

    /**
     * The index operator takes a sub-expression with an ordered container value and any base-type. The result is the nth value of the container. The result has the same base-type as the sub-expression but single cardinality. The first value of a container has index 1, the second 2 and so on. n must be a positive integer. If n exceeds the number of values in the container then the result of the index operator is NULL.
     * @param $n
     * @return ImsQtiWriter
     */
    public function add_index($n){
      $result = $this->add_element('index');
      $result->set_attribute('n', $n);
      return $result;
    }    
    
    /**
     * The inside operator takes a single sub-expression which must have a baseType of point. The result is a single boolean with a value of true if the given point is inside the area defined by shape and coords. If the sub-expression is a container the result is true if any of the points are inside the area. If either sub-expression is NULL then the operator results in NULL.
     * @param $shape The shape of the area.
     * @param $coords The size and position of the area, interpreted in conjunction with the shape.
     * @return ImsQtiWriter
     */
    public function add_inside($shape, $coords){
      $result = $this->add_element('inside');
      $result->set_attribute('shape', $shape);
      $result->set_attribute('coords', $coords);
      return $result;
    }    
    
    /**
     * The integer divide operator takes 2 sub-expressions which both have single cardinality and base-type integer. The result is the single integer that corresponds to the first expression (x) divided by the second expression (y) rounded down to the greatest integer (i) such that i<=(x/y). If y is 0, or if either of the sub-expressions is NULL then the operator results in NULL.
     * @return ImsQtiWriter
     */
    public function add_integerDivide(){
      $result = $this->add_element('integerDivide');
      return $result;
    }    

    /**
     * Contains : expression [2]
     * The integer modulus operator takes 2 sub-expressions which both have single cardinality and base-type integer. The result is the single integer that corresponds to the remainder when the first expression (x) is divided by the second expression (y). If z is the result of the corresponding integerDivide operator then the result is x-z*y. If y is 0, or if either of the sub-expressions is NULL then the operator results in NULL.
     * The integer modulus operator takes 2 sub-expressions which both have single cardinality and base-type integer. The result is the single integer that corresponds to the remainder when the first expression (x) is divided by the second expression (y). If z is the result of the corresponding integerDivide operator then the result is x-z*y. If y is 0, or if either of the sub-expressions is NULL then the operator results in NULL.
     * @return ImsQtiWriter
     */
    public function add_integerModulus(){
      $result = $this->add_element('integerModulus');
      return $result;
    }

    /**
     * The integer to float conversion operator takes a single sub-expression which must have single cardinality and base-type integer. The result is a value of base type float with the same numeric value. If the sub-expression is NULL then the operator results in NULL.
     * @return ImsQtiWriter
     */
    public function add_integerToFloat(){
      $result = $this->add_element('integerToFloat');
      return $result;
    }

    /**
     * This expression looks up the value of a responseVariable that must be of base-type point , and transforms it using the associated areaMapping. The transformation is similar to mapResponse except that the points are tested against each area in turn. When mapping containers each area can be mapped once only. For example, if the candidate identified two points that both fall in the same area then the mappedValue is still added to the calculated total just once.
     * @param $identifier
     * @return ImsQtiWriter
     */
    public function add_mapResponsePoint($identifier){
      $result = $this->add_element('mapResponsePoint');
      $result->set_attribute('identifier', $identifier);
      return $result;
    }

    /**
     * The match operator takes two sub-expressions which must both have the same base-type and cardinality. The result is a single boolean with a value of true if the two expressions represent the same value and false if they do not. If either sub-expression is NULL then the operator results in NULL.
     * The match operator must not be confused with broader notions of equality such as numerical equality. To avoid confusion, the match operator should not be used to compare subexpressions with base-types of float and must not be used on sub-expressions with a base-type of duration.
     * @return ImsQtiWriter
     */
    public function add_match(){
      $result = $this->add_element('match');
      return $result;
    }

    /**
     * The member operator takes two sub-expressions which must both have the same base-type. The first sub-expression must have single cardinality and the second must be a multiple or ordered container. The result is a single boolean with a value of true if the value given by the first sub-expression is in the container defined by the second sub-expression. If either sub-expression is NULL then the result of the operator is NULL.
     * The member operator should not be used on sub-expressions with a base-type of float because of the poorly defined comparison of values. It must not be used on sub-expressions with a base-type of duration.
     * @return ImsQtiWriter
     */
    public function add_member(){
      $result = $this->add_element('member');
      return $result;
    }

    /**
     * The not operator takes a single sub-expression with a base-type of boolean and single cardinality. The result is a single boolean with a value obtained by the logical negation of the sub-expression's value. If the sub-expression is NULL then the not operator also results in NULL.
     * @return ImsQtiWriter
     */
    public function add_not(){
      $result = $this->add_element('not');
      return $result;
    }

    /**
     * null is a simple expression that returns the NULL value - the null value is treated as if it is of any desired baseType.
     * @return ImsQtiWriter
     */
    public function add_null(){
      $result = $this->add_element('null');
      return $result;
    }
    
    /**
     * The or operator takes one or more sub-expressions each with a base-type of boolean and single cardinality. The result is a single boolean which is true if any of the sub-expressions are true and false if all of them are false. If one or more sub-expressions are NULL and all the others are false then the operator also results in NULL.
     * @return ImsQtiWriter
     */
    public function add_or(){
      $result = $this->add_element('or');
      return $result;
    }

    /**
     * The ordered operator takes 0 or more sub-expressions all of which must have either single or ordered cardinality. Although the sub-expressions may be of any base-type they must all be of the same base-type. The result is a container with ordered cardinality containing the values of the sub-expressions, sub-expressions with ordered cardinality have their individual values added (in order) to the result: contains cannot contain other containers. For example, when applied to A, B, {C,D} the ordered operator results in {A,B,C,D}. Note that the ordered operator never results in an empty container. All sub-expressions with NULL values are ignored. If no sub-expressions are given (or all are NULL) then the result is NULL
     * @return ImsQtiWriter
     */
    public function add_ordered(){
      $result = $this->add_element('ordered');
      return $result;
    }
    
    /**
     * Selects a random float from the specified range [min,max].
     * @param $min
     * @param $max
     * @return ImsQtiWriter
     */
    public function add_randomFloat($min=0, $max){
      $result = $this->add_element('randomFloat');
      $result->set_attribute('min', $min);
      $result->set_attribute('max', $max);
      return $result;
    }

    /**
     * Selects a random integer from the specified range [min,max] satisfying min + step * n for some integer n. For example, with min=2, max=11 and step=3 the values {2,5,8,11} are possible.
     * @param $min
     * @param $max
     * @param $step
     * @return ImsQtiWriter
     */
    public function add_randomInteger($min=0, $max, $step=1){
      $result = $this->add_element('randomInteger');
      $result->set_attribute('min', $min);
      $result->set_attribute('max', $max);
      $result->set_attribute('step', $step);
      return $result;
    }

    /**
     * The round operator takes a single sub-expression which must have single cardinality and base-type float. The result is a value of base-type integer formed by rounding the value of the sub-expression. The result is the integer n for all input values in the range [n-0.5,n+0.5). In other words, 6.8 and 6.5 both round up to 7, 6.49 rounds down to 6 and -6.5 rounds up to -6. If the sub-expression is NULL then the operator results in NULL
     * @return ImsQtiWriter
     */
    public function add_round(){
      $result = $this->add_element('round');
      return $result;
    }

    /**
     * Contains : expression [2]
     * The stringMatch operator takes two sub-expressions which must have single and a base-type of string. The result is a single boolean with a value of true if the two strings match according to the comparison rules defined by the attributes below and false if they don't. If either sub-expression is NULL then the operator results in NULL.
     * @param $caseSensitive Whether or not the match is to be carried out case sensitively.
     * @param boolean $substring If true, then the comparison returns true if the first string contains the second one, otherwise it returns true only if they match entirely.
     * @return ImsQtiWriter
     */
    public function add_stringMatch($caseSensitive, $substring=false){
      $result = $this->add_element('stringMatch');
      $result->set_attribute('caseSensitive', $caseSensitive ? 'true' : 'false');
      $result->set_attribute('substring', $substring ? 'true' : 'false');
      return $result;
    }

    /**
     * The substring operator takes two sub-expressions which must both have an effective base-type of string and single cardinality. The result is a single boolean with a value of true if the first expression is a substring of the second expression and false if it isn't. If either sub-expression is NULL then the result of the operator is NULL.
     * @param $caseSensitive Used to control whether or not the substring is matched case sensitively. If true then the match is case sensitive and, for example, "Hell" is not a substring of "Shell". If false then the match is not case sensitive and "Hell" is a substring of "Shell".
     * @return ImsQtiWriter
     */
    public function add_substring($caseSensitive){
      $result = $this->add_element('substring');
      $result->set_attribute('caseSensitive', $caseSensitive);
      return $result;
    }
    
	  /**
	   * Contains : expression [1]
	   * The isNull operator takes a sub-expression with any base-type and cardinality. The result is a single boolean with a value of true if the sub-expression is NULL and false otherwise. Note that empty containers and empty strings are both treated as NULL. 
     * @return ImsQtiWriter
     */
    public function add_isNull(){
    	$result = $this->add_element('isNull');
    	return $result;
    }
    
    /**
     * Contains : expression [2]
     * The equal operator takes two sub-expressions which must both have single cardinality and have a numerical base-type. The result is a single boolean with a value of true if the two expressions are numerically equal and false if they are not. If either sub-expression is NULL then the operator results in NULL.
     * If the tolerance mode is absolute or relative then the tolerance must be specified. The tolerance consists of two positive numbers, t0 and t1, that define the lower and upper bounds. If only one value is given it is used for both.
     * In absolute mode the result of the comparison is true if the value of the second expression, y is within the following range defined by the first value, x.
     * [x-t0,x+t1]In relative mode, t0 and t1 are treated as percentages and the following range is used instead.
     * [x*(1-t0/100),x*(1+t1/100)]
     * @param $toleranceMode When comparing two floating point numbers for equality it is often desirable to have a tolerance to ensure that spurious errors in scoring are not introduced by rounding errors. The tolerance mode determines whether the comparison is done exactly, using an absolute range or a relative range. 
     * @param $tolerance_low 
     * @param $tolerance_high
     * @return ImsQtiWriter
     */
    public function add_equal($toleranceMode = self::TOLERANCE_MODE_EXACT, $tolerance_low='', $tolerance_high=''){
    	$result = $this->add_element('equal');
    	$result->set_attribute('toleranceMode', $toleranceMode);
    	if(!empty($tolerance_low) && !empty($tolerance_high)){
          $result->set_attribute('tolerance', "$tolerance_low $tolerance_high", false);
    	}
    	return $result;
    }
    
    /**
     * The gt operator takes two sub-expressions which must both have single cardinality and have a numerical base-type. The result is a single boolean with a value of true if the first expression is numerically greater than the second and false if it is less than or equal to the second. If either sub-expression is NULL then the operator results in NULL.
     * @return ImsQtiWriter
     */
    public function add_gt(){
      $result = $this->add_element('gt');
      return $result;
    }
    
    /**
     * The lt operator takes two sub-expressions which must both have single cardinality and have a numerical base-type. The result is a single boolean with a value of true if the first expression is numerically less than the second and false if it is greater than or equal to the second. If either sub-expression is NULL then the operator results in NULL.
     * @return ImsQtiWriter
     */
    public function add_lt(){
      $result = $this->add_element('lt');
      return $result;
    }
    
    /**
     * The lte operator takes two sub-expressions which must both have single cardinality and have a numerical base-type. The result is a single boolean with a value of true if the first expression is numerically less than or equal to the second and false if it is greater than the second. If either sub-expression is NULL then the operator results in NULL.
     * @return ImsQtiWriter
     */
    public function add_lte(){
      $result = $this->add_element('lte');
      return $result;
    }
    
    /**
     * The gte operator takes two sub-expressions which must both have single cardinality and have a numerical base-type. The result is a single boolean with a value of true if the first expression is numerically less than or equal to the second and false if it is greater than the second. If either sub-expression is NULL then the operator results in NULL.
     * @return ImsQtiWriter
     */
    public function add_gte(){
      $result = $this->add_element('gte');
      return $result;
    }
    
    /**
     * The and operator takes one or more sub-expressions each with a base-type of boolean and single cardinality. The result is a single boolean which is true if all sub-expressions are true and false if any of them are false. If one or more sub-expressions are NULL and all others are true then the operator also results in NULL.
     * @return ImsQtiWriter
     */
    public function add_and(){
      $result = $this->add_element('and');
      return $result;
    }
    
    /**
     * Contains : expression [1]
     * The patternMatch operator takes a sub-expression which must have single cardinality and a base-type of string. The result is a single boolean with a value of true if the sub-expression matches the regular expression given by pattern and false if it doesn't. If the sub-expression is NULL then the operator results in NULL.
     * @param $pattern The syntax for the regular expression language is as defined in Appendix F of [XML_SCHEMA2].
     * @return ImsQtiWriter
     */
    public function add_patternMatch($pattern){
    	$result = $this->add_element('patternMatch');
    	$result->set_attribute('pattern', $pattern);
    	return $result;
    }
    
    /**
     * Contains : expression [1..*]
     * The sum operator takes 1 or more sub-expressions which all have single cardinality and have numerical base-types. The result is a single float or, if all sub-expressions are of integer type, a single integer that corresponds to the sum of the numerical values of the sub-expressions. If any of the sub-expressions are NULL then the operator results in NULL.
     * @return ImsQtiWriter
     */
    public function add_sum(){
    	$result = $this->add_element('sum');
    	return $result;
    }
    
    /**
     * The subtract operator takes 2 sub-expressions which all have single cardinality and numerical base-types. The result is a single float or, if both sub-expressions are of integer type, a single integer that corresponds to the first value minus the second. If either of the sub-expressions is NULL then the operator results in NULL.
     * @return ImsQtiWriter
     */
    public function add_subtract(){
    	$result = $this->add_element('subtract');
    	return $result;
    }
    
    /**
     * The divide operator takes 2 sub-expressions which both have single cardinality and numerical base-types. The result is a single float that corresponds to the first expression divided by the second expression. If either of the sub-expressions is NULL then the operator results in NULL.
     * Item authors should make every effort to ensure that the value of the second expression is never 0, however, if it is zero or the resulting value is outside the value set defined by float (not including positive and negative infinity) then the operator should result in NULL.
     * @return ImsQtiWriter
     */
    public function add_divide(){
    	$result = $this->add_element('divide');
    	return $result;
    }
    
  	/**
  	 * The product operator takes 1 or more sub-expressions which all have single cardinality and have numerical base-types. The result is a single float or, if all sub-expressions are of integer type, a single integer that corresponds to the product of the numerical values of the sub-expressions. If any of the sub-expressions are NULL then the operator results in NULL.
     * @return ImsQtiWriter
     */
    public function add_product(){
    	$result = $this->add_element('product');
    	return $result;
    }
        
  	/**
  	 * The power operator takes 2 sub-expression which both have single cardinality and numerical base-types. The result is a single float that corresponds to the first expression raised to the power of the second. If either or the sub-expressions is NULL then the operator results in NULL.
     * If the resulting value is outside the value set defined by float (not including positive and negative infinity) then the operator shall result in NULL.
     * @return ImsQtiWriter
     */
    public function add_power(){
    	$result = $this->add_element('power');
    	return $result;
    }
    
    /**
     * The simplest expression returns a single value from the set defined by the given baseType.
     * @param $baseType The base-type of the value.
     * @param $value
     * @return ImsQtiWriter
     */
    public function add_baseValue($baseType='', $value){
    	$result = $this->add_element('baseValue', $value);
    	
    	if(empty($baseType)){
    		if(is_int($value)){
    			$baseType = self::BASETYPE_INTEGER;
    		}else if(is_float($value)){
    			$baseType = self::BASETYPE_FLOAT;
    		}else if(is_bool($value)){
    			$baseType = self::BASETYPE_BOOLEAN;
    		}else if(is_string($value)){
    			$baseType = self::BASETYPE_STRING;
    		}
    	}
    	$result->set_attribute('baseType', $baseType);
    	return $result;
    }
    	
    /**
     * @return ImsQtiWriter
     */
    public function add_minus(){
    	$result = $this->add_subtract();
    	$result->add_baseValue(self::BASETYPE_INTEGER, 0);
    	return $result;
    }
    
	  /**
     * @return ImsQtiWriter
     */
    public function add_inverse(){
    	$result = $this->add_divide();
    	$result->add_baseValue(self::BASETYPE_FLOAT, 1);
    	return $result;
    }
 
 	  /**
 	   * The truncate operator takes a single sub-expression which must have single cardinality and base-type float. The result is a value of base-type integer formed by truncating the value of the sub-expression towards zero. For example, the value 6.8 becomes 6 and the value -6.8 becomes -6. If the sub-expression is NULL then the operator results in NULL.
     * @return ImsQtiWriter
     */
    public function add_truncate(){
    	$result = $this->add_element('truncate', $value);
    	return $result;
    }
    
  	/**
	   * Returns e raised to the power of arg.
     * @return ImsQtiWriter
     */
    public function add_exp(){
    	$result = $this->add_power();
    	$result->add_baseValue(self::BASETYPE_FLOAT, 2.7182818);
    	return $result;
    }
    
    /**
     * Returns the next highest integer value by rounding up value if necessary. 
     * @return ImsQtiWriter
     */
    public function add_ceiling(){
    	$result = $this->add_truncate()->add_sum();
    	$result->add_baseValue(self::BASETYPE_INTEGER, 1);
    	return $result;
    }
    
    /**
     * The custom operator provides an extension mechanism for defining operations not currently supported by this specification.
     * Contains : expression [*]
     * Custom operators can take any number of sub-expressions of any type to be treated as parameters.
     * @param $class The class attribute allows simple sub-classes to be named. The definition of a sub-class is tool specific and may be inferred from toolName and toolVersion. 
     * @param $definition A URI that identifies the definition of the custom operator in the global namespace. 
     * @return ImsQtiWriter     
     */
    public function add_customOperator($class='', $definition=''){
    	$result = $this->add_element('customOperator');
    	$result->set_attribute('class', $class, false);
    	$result->set_attribute('definition', $definition, false);
    	return $result;
    }
    
    /**
     * The random operator takes a sub-expression with a multiple or ordered container value and any base-type. The result is a single value randomly selected from the container. The result has the same base-type as the sub-expression but single cardinality. If the sub-expression is NULL then the result is also NULL.
     * @return ImsQtiWriter
     */
    public function add_random(){
    	$result = $this->add_element('random');
    	return $result;
    	
    }
    
    /**
     * The multiple operator takes 0 or more sub-expressions all of which must have either single or multiple cardinality. Although the sub-expressions may be of any base-type they must all be of the same base-type. The result is a container with multiple cardinality containing the values of the sub-expressions, sub-expressions with multiple cardinality have their individual values added to the result: containers cannot contain other containers. For example, when applied to A, B and {C,D} the multiple operator results in {A,B,C,D}. All sub-expressions with NULL values are ignored. If no sub-expressions are given (or all are NULL) then the result is NULL.  
     * @return ImsQtiWriter
     */
    public function add_multiple(){
    	$result = $this->add_element('multiple');
    	return $result;
    }
    
    // END EXPRESSIONS
    

    // TEMPLATE PROCESSING

    /**
     * Template declarations declare item variables that are to be used specifically for the purposes of cloning items. They can have their value set only during templateProcessing. They are referred to within the itemBody in order to individualize the clone and possibly also within the responseProcessing rules if the cloning process affects the way the item is scored.
     * @param unknown_type $identifier
     * @param unknown_type $cardinality
     * @param unknown_type $basetype
     * @param unknown_type $paramVariable This attribute determines whether or not the template variable's value should be substituted for object parameter values that match its name. See param for more information. 
     * @param unknown_type $mathVariable This attribute determines whether or not the template variable's value should be substituted for identifiers that match its name in MathML expressions. See Combining Template Variables and MathML for more information. 
     */
    public function add_templateDeclaration($identifier, $cardinality, $basetype, $paramVariable, $mathVariable = false ){
    	$result = $this->add_variableDeclaration('templateDeclaration', $identifier, $cardinality, $basetype);
    	$result->set_attribute('paramVariable', $paramVariable ? 'true' : 'false');
    	$result->set_attribute('mathVariable', $mathVariable ? 'true' : 'false');
    	return $result;
    }
    
    /**
     * Template processing consists of one or more templateRules that are followed by the cloning engine or delivery system in order to assign values to the templateVariables. Template processing is identical in form to responseProcessing except that the purpose is to assign values to Template Variables, not outcomeVariables.
     * @return ImsQtiWriter
     */
    public function add_templateProcessing(){
    	$result = $this->add_element('templateProcessing');
    	return $result;
    }
    
    /**
     * The setTemplateValue rules sets the value of a templateVariable to the value obtained from the associated expression. A template variable can be updated with reference to a previously assigned value, in other words, the templateVariable being set may appear in the expression where it takes the value previously assigned to it.
     * @param $identifier The templateVariable to be set.
     * @return ImsQtiWriter
     */
    public function add_setTemplateValue($identifier){
    	$result = $this->add_element('setTemplateValue');
    	$result->set_attribute('identifier', $identifier);
    	return $result;
    }

    /**
     * The exit template rule terminates template processing immediately.
     * @param $identifier
     * @return ImsQtiWriter
     */
    public function add_exitTemplate(){
    	$result = $this->add_element('exitTemplate');
    	return $result;
    }
    
	/**
	 * Contains : expression [1]
	 * @param $identifier The responseVariable to have its correct value set.
   * @return ImsQtiWriter
	 */
    public function add_setCorrectResponse($identifier){
    	$result = $this->add_element('setCorrectResponse');
    	$result->set_attribute('identifier', $identifier);
    	return $result;
    }
    
    /**
     * Contains : expression [1]
     * @param $identifier The responseVariable or outcomeVariable to have its default value set.
     * @return ImsQtiWriter 
     */
	  public function add_setDefaultValue($identifier){
    	$result = $this->add_element('setDefaultValue');
    	$result->set_attribute('identifier', $identifier);
    	return $result;
    }

    /**
     * If the expression given in the templateIf or templateElseIf evaluates to true then the sub-rules contained within it are followed and any following templateElseIf or templateElse parts are ignored for this template condition.
	   * If the expression given in the templateIf or templateElseIf does not evaluate to true then consideration passes to the next templateElseIf or, if there are no more templateElseIf parts then the sub-rules of the templateElse are followed (if specified).
     * @param $identifier
     * @return ImsQtiWriter 
     */
    public function add_templateCondition(){
    	$result = $this->add_element('templateCondition');
    	return $result;
    }
    
    /**
     * A templateIf part consists of an expression which must have an effective baseType of boolean and single cardinality. For more information about the runtime data model employed see Expressions. It also contains a set of sub-rules. If the expression is true then the sub-rules are processed, otherwise they are skipped (including if the expression is NULL) and the following templateElseIf or templateElse parts (if any) are considered instead.
     * @return ImsQtiWriter 
     */
	  public function add_templateIf(){
    	$result = $this->add_element('templateIf');
    	return $result;
    }
    
    /**
     * templateElseIf is defined in an identical way to templateIf. 
     * Contains : expression [1]
     * Contains : templateRule [*]
     * @return ImsQtiWriter 
     */
	  public function add_templateElseIf(){
    	$result = $this->add_element('templateElseIf');
    	return $result;
    }
    
    /**
     * Contains : templateRule [*]
     */
	  public function add_templateElse(){
    	$result = $this->add_element('templateElse');
    	return $result;
    }
    
    //END TEMPLATE PROCESSING
    
}






























?>