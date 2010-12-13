<?php
/**
 * ClaroNext Coding Standard.
 *
 * PHP version 5
 *
 * @author    Systho
 * @license   undefined
 */

if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * ClaroNext Coding Standard.
 *
 * This class selects the differents sniffs used for validating ClaroNext Coding Standards.
 * Those Standards are mainly the ones form the Zend Framework plus some minor changes
 * 
 * @see http://docs.google.com/View?id=ddfhmpkf_15fwvvc3dw
 *
 */
class PHP_CodeSniffer_Standards_Chamilo2_Chamilo2CodingStandard
    extends PHP_CodeSniffer_Standards_CodingStandard
{
    /**
     * Return a list of external sniffs to include with this standard.
     *
     * We will mainly use Zend Standard and make some exceptions
     *
     * @return array
     */
    public function getIncludedSniffs()
    {
        return array(
            /* Opening brace must be on line after function declaration */
            'Generic/Sniffs/Functions/OpeningFunctionBraceBsdAllmanSniff.php',
            /* do not use <? instead of <?php */
            'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',
            /* do not use tabs for indentation, use spaces */
            #'Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php',
            /* opening brace must be alone on line after class declaration
             * and there cannot be any spaces in front of it
             */
            #'PEAR/Sniffs/Classes/ClassDeclarationSniff.php',
            /* EOL character is "\n" */            
            #'PEAR/Sniffs/Files/LineEndingsSniff.php',
            /* function calls arguments  have
             *  - no spaces before comma
             *  - 1 space after comma
             *  - 1 space before and 1 space after equal sign of default value
             */
            'PEAR/Sniffs/Functions/FunctionCallArgumentSpacingSniff.php',
            /* function calls have
             *  - no space before opening parenthesis
             *  - no space after closing parenthesis
             *  - no space before closing parenthesis
             *  - correct indentation when multiline'd
             *  - nothing after opening parenthesis when multiline'd
             *  - a separate line for the closing parenthesis when multiline'd
             */
            'PEAR/Sniffs/Functions/FunctionCallSignatureSniff.php',
            /* arguments with default values must be at the end */
            'PEAR/Sniffs/Functions/ValidDefaultValueSniff.php',
            /* closing braces are on their own line and are aligned
             * with the first token of the matching opening brace
             */
            'PEAR/Sniffs/WhiteSpace/ScopeClosingBraceSniff.php',
            /* The php file does not end with a closing tag */
            'Zend/Sniffs/Files/ClosingTagSniff.php',
            /* Line should not exceed 80 chars and cannot exceed 120 chars */
            'Zend/Sniffs/Files/LineLengthSniff.php', 
            /* constructor names follow php5 convention => __construct */
            'Generic/Sniffs/NamingConventions/ConstructorNameSniff.php',
           );
    
    }
    
    
    
    
}
