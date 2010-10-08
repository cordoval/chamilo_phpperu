<?php

require_once dirname(__FILE__) .'/qti_renderer_base.class.php';

/**
 * Partial renderer. Used to extract parts - question text, answers, etc - for import.
 * Do not output interactions as HTML elements but as spans.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiPartialRenderer extends QtiRendererBase{

	public function __construct($resource_manager){
		parent::__construct($resource_manager);
	}
	
	protected function create_map(){
		$result = parent::create_map();
		$result['adaptive'] = ''; 
		$result['and'] = ''; 
		$result['anyN'] = ''; 
		$result['areaMapEntry'] = ''; 
		$result['areaMapping'] = ''; 
		$result['assessmentItem'] = ''; 
		$result['associableHotspot'] = ''; 
		$result['associateInteraction'] = 'span'; 
		$result['bankProfile'] = ''; 
		$result['baseValue'] = ''; 
		$result['categorizedStatistic'] = ''; 
		$result['choiceInteraction'] = 'span'; 
		$result['composite'] = ''; 
		$result['contains'] = ''; 
		$result['contentProfile'] = ''; 
		$result['correct'] = ''; 
		$result['correctResponse'] = ''; 
		$result['customInteraction'] = 'span'; 
		$result['customOperator'] = ''; 
		$result['default'] = ''; 
		$result['defaultValue'] = ''; 
		$result['delete'] = ''; 
		$result['divide'] = ''; 
		$result['drawingInteraction'] = 'span'; 
		$result['durationGTE'] = ''; 
		$result['durationLT'] = ''; 
		$result['endAttemptInteraction'] = ''; 
		$result['equal'] = ''; 
		$result['equalRounded'] = ''; 
		$result['exitResponse'] = ''; 
		$result['exitTemplate'] = ''; 
		$result['extendedTextInteraction'] = 'span'; 
		$result['feedbackBlock'] = 'div'; 
		$result['feedbackInline'] = 'span'; 
		$result['feedbackIntegrated'] = 'span'; 
		$result['feedbackModal'] = 'span'; 
		$result['feedbackType'] = ''; 
		$result['fieldValue'] = ''; 
		$result['gap'] = ''; 
		$result['gapImg'] = ''; 
		$result['gapMatchInteraction'] = 'span'; 
		$result['gapText'] = ''; 
		$result['graphicAssociateInteraction'] = 'span'; 
		$result['graphicGapMatchInteraction'] = 'span'; 
		$result['graphicOrderInteraction'] = 'span'; 
		$result['gt'] = ''; 
		$result['gte'] = ''; 
		$result['hotspotChoice'] = ''; 
		$result['hotspotInteraction'] = 'span'; 
		$result['hottext'] = ''; 
		$result['hottextInteraction'] = 'span'; 
		$result['hypertextElement'] = ''; 
		$result['imageElement'] = ''; 
		$result['imageType'] = ''; 
		$result['imsmd'] = ''; 
		$result['imsqtimd'] = ''; 
		$result['index'] = ''; 
		$result['inlineChoice'] = ''; 
		$result['inlineChoiceInteraction'] = 'span'; 
		$result['inside'] = ''; 
		$result['integerDivide'] = ''; 
		$result['integerModulus'] = ''; 
		$result['integerToFloat'] = ''; 
		$result['interactionType'] = ''; 
		$result['isNull'] = ''; 
		$result['itemBody'] = 'span'; 
		$result['itemTemplate'] = ''; 
		$result['listElements'] = ''; 
		$result['lomMetadata'] = ''; 
		$result['lt'] = ''; 
		$result['lte'] = ''; 
		$result['m:math'] = 'math'; 
		$result['mapEntry'] = ''; 
		$result['mapping'] = ''; 
		$result['mapResponse'] = ''; 
		$result['mapResponsePoint'] = ''; 
		$result['match'] = ''; 
		$result['matchInteraction'] = 'span'; 
		$result['mathElement'] = ''; 
		$result['mathVariable'] = ''; 
		$result['member'] = ''; 
		$result['metadataProfile'] = ''; 
		$result['modalFeedback'] = 'span'; 
		$result['multiple'] = ''; 
		$result['not'] = ''; 
		$result['null'] = ''; 
		$result['objectElements'] = ''; 
		$result['objectType'] = ''; 
		$result['or'] = ''; 
		$result['ordered'] = ''; 
		$result['orderInteraction'] = 'span'; 
		$result['ordinaryStatistic'] = ''; 
		$result['outcomeDeclaration'] = ''; 
		$result['patternMatch'] = ''; 
		$result['positionObjectInteraction'] = 'span'; 
		$result['positionObjectStage'] = ''; 
		$result['power'] = ''; 
		$result['presentationElements'] = ''; 
		$result['printedVariable'] = ''; 
		$result['printedVariables'] = ''; 
		$result['product'] = ''; 
		$result['prompt'] = 'div'; 
		$result['qtiMetadata'] = ''; 
		$result['random'] = ''; 
		$result['randomFloat'] = ''; 
		$result['randomInteger'] = ''; 
		$result['regexp'] = ''; 
		$result['responseCondition'] = ''; 
		$result['responseDeclaration'] = ''; 
		$result['responseElse'] = ''; 
		$result['responseElseIf'] = ''; 
		$result['responseIf'] = ''; 
		$result['responseProcessing'] = ''; 
		$result['responseRules'] = ''; 
		$result['round'] = ''; 
		$result['rounding'] = ''; 
		$result['rpTemplate'] = ''; 
		$result['rubric'] = ''; 
		$result['rubricBlock'] = ''; 
		$result['selectPointInteraction'] = 'span'; 
		$result['setCorrectResponse'] = ''; 
		$result['setDefaultValue'] = ''; 
		$result['setOutcomeValue'] = ''; 
		$result['setTemplateValue'] = ''; 
		$result['simpleAssociableChoice'] = ''; 
		$result['simpleChoice'] = ''; 
		$result['simpleMatchSet'] = ''; 
		$result['sliderInteraction'] = 'span'; 
		$result['solutionAvailable'] = ''; 
		$result['stringMatch'] = ''; 
		$result['stylesheet'] = 'link'; 
		$result['substring'] = ''; 
		$result['subtract'] = ''; 
		$result['sum'] = ''; 
		$result['tableElements'] = ''; 
		$result['targetObject'] = ''; 
		$result['templateBlock'] = ''; 
		$result['templateCondition'] = ''; 
		$result['templateDeclaration'] = ''; 
		$result['templateElse'] = ''; 
		$result['templateElseIf'] = ''; 
		$result['templateIf'] = ''; 
		$result['templateInline'] = ''; 
		$result['templateProcessing'] = ''; 
		$result['templates'] = ''; 
		$result['textElements'] = ''; 
		$result['textEntryInteraction'] = 'span'; 
		$result['timeDependent'] = ''; 
		$result['toolName'] = ''; 
		$result['toolVendor'] = ''; 
		$result['toolVersion'] = ''; 
		$result['truncate'] = ''; 
		$result['uploadInteraction'] = 'span'; 
		$result['usageData'] = ''; 
		$result['usageDataVocabulary'] = ''; 
		$result['value'] = ''; 
		$result['variable'] = ''; 
		return $result;
	}

}
	

?>