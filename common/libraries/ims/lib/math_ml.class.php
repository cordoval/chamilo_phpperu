<?php

/**
 * 
 * MathML tags.
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class MathML{
	
	private static $tags = null;
	
	public static function get_tags(){
		if(empty(self::$tags)){
			$result['abs'] = 'abs';
			$result['and'] = 'and';
			$result['annotation'] = 'annotation';
			$result['annotation-xml'] = 'annotation-xml';
			$result['apply'] = 'apply';
			$result['approx'] = 'approx';
			$result['arccos'] = 'arccos';
			$result['arccosh'] = 'arccosh';
			$result['arccot'] = 'arccot';
			$result['arccoth'] = 'arccoth';
			$result['arccsc'] = 'arccsc';
			$result['arccsch'] = 'arccsch';
			$result['arcsec'] = 'arcsec';
			$result['arcsech'] = 'arcsech';
			$result['arcsin'] = 'arcsin';
			$result['arcsinh'] = 'arcsinh';
			$result['arctan'] = 'arctan';
			$result['arctanh'] = 'arctanh';
			$result['arg'] = 'arg';
			$result['bvar'] = 'bvar';
			$result['card'] = 'card';
			$result['cartesianproduct'] = 'cartesianproduct';
			$result['ceiling'] = 'ceiling';
			$result['ci'] = 'ci';
			$result['cn'] = 'cn';
			$result['codomain'] = 'codomain';
			$result['complexes'] = 'complexes';
			$result['compose'] = 'compose';
			$result['condition'] = 'condition';
			$result['conjugate'] = 'conjugate';
			$result['cos'] = 'cos';
			$result['cosh'] = 'cosh';
			$result['cot'] = 'cot';
			$result['coth'] = 'coth';
			$result['csc'] = 'csc';
			$result['csch'] = 'csch';
			$result['csymbol'] = 'csymbol';
			$result['curl'] = 'curl';
			$result['declare'] = 'declare';
			$result['degree'] = 'degree';
			$result['determinant'] = 'determinant';
			$result['diff'] = 'diff';
			$result['divergence'] = 'divergence';
			$result['divide'] = 'divide';
			$result['domain'] = 'domain';
			$result['domainofapplication'] = 'domainofapplication';
			$result['emptyset'] = 'emptyset';
			$result['encoding'] = 'encoding';
			$result['eq'] = 'eq';
			$result['equivalent'] = 'equivalent';
			$result['eulergamma'] = 'eulergamma';
			$result['exists'] = 'exists';
			$result['exp'] = 'exp';
			$result['exponentiale'] = 'exponentiale';
			$result['factorial'] = 'factorial';
			$result['factorof'] = 'factorof';
			$result['false'] = 'false';
			$result['floor'] = 'floor';
			$result['fn'] = 'fn';
			$result['forall'] = 'forall';
			$result['function'] = 'function';
			$result['gcd'] = 'gcd';
			$result['geq'] = 'geq';
			$result['grad'] = 'grad';
			$result['gt'] = 'gt';
			$result['ident'] = 'ident';
			$result['image'] = 'image';
			$result['imaginary'] = 'imaginary';
			$result['imaginaryi'] = 'imaginaryi';
			$result['implies'] = 'implies';
			$result['in'] = 'in';
			$result['infinity'] = 'infinity';
			$result['int'] = 'int';
			$result['integers'] = 'integers';
			$result['intersect'] = 'intersect';
			$result['interval'] = 'interval';
			$result['inverse'] = 'inverse';
			$result['J.1'] = 'J.1';
			$result['J.1'] = 'J.1';
			$result['lambda'] = 'lambda';
			$result['laplacian'] = 'laplacian';
			$result['lcm'] = 'lcm';
			$result['leq'] = 'leq';
			$result['limit'] = 'limit';
			$result['list'] = 'list';
			$result['ln'] = 'ln';
			$result['log'] = 'log';
			$result['logbase'] = 'logbase';
			$result['lowlimit'] = 'lowlimit';
			$result['lt'] = 'lt';
			$result['maction'] = 'maction';
			$result['malign'] = 'malign';
			$result['maligngroup'] = 'maligngroup';
			$result['malignmark'] = 'malignmark';
			$result['malignscope'] = 'malignscope';
			$result['math'] = 'math';
			$result['matrix'] = 'matrix';
			$result['matrixrow'] = 'matrixrow';
			$result['max'] = 'max';
			$result['mean'] = 'mean';
			$result['median'] = 'median';
			$result['menclose'] = 'menclose';
			$result['merror'] = 'merror';
			$result['mfenced'] = 'mfenced';
			$result['mfrac'] = 'mfrac';
			$result['mfraction'] = 'mfraction';
			$result['mglyph'] = 'mglyph';
			$result['mi'] = 'mi';
			$result['min'] = 'min';
			$result['minus'] = 'minus';
			$result['mlabeledtr'] = 'mlabeledtr';
			$result['mmultiscripts'] = 'mmultiscripts';
			$result['mn'] = 'mn';
			$result['mo'] = 'mo';
			$result['mode'] = 'mode';
			$result['moment'] = 'moment';
			$result['momentabout'] = 'momentabout';
			$result['mover'] = 'mover';
			$result['mpadded'] = 'mpadded';
			$result['mphantom'] = 'mphantom';
			$result['mprescripts'] = 'mprescripts';
			$result['mroot'] = 'mroot';
			$result['mrow'] = 'mrow';
			$result['ms'] = 'ms';
			$result['mspace'] = 'mspace';
			$result['msqrt'] = 'msqrt';
			$result['mstyle'] = 'mstyle';
			$result['msub'] = 'msub';
			$result['msubsup'] = 'msubsup';
			$result['msup'] = 'msup';
			$result['mtable'] = 'mtable';
			$result['mtd'] = 'mtd';
			$result['mtext'] = 'mtext';
			$result['mtr'] = 'mtr';
			$result['munder'] = 'munder';
			$result['munderover'] = 'munderover';
			$result['naturalnumbers'] = 'naturalnumbers';
			$result['neq'] = 'neq';
			$result['none'] = 'none';
			$result['not'] = 'not';
			$result['notanumber'] = 'notanumber';
			$result['notin'] = 'notin';
			$result['notprsubset'] = 'notprsubset';
			$result['notsubset'] = 'notsubset';
			$result['or'] = 'or';
			$result['otherwise'] = 'otherwise';
			$result['outerproduct'] = 'outerproduct';
			$result['partialdiff'] = 'partialdiff';
			$result['pi'] = 'pi';
			$result['piece'] = 'piece';
			$result['piecewice'] = 'piecewice';
			$result['piecewise'] = 'piecewise';
			$result['plus'] = 'plus';
			$result['power'] = 'power';
			$result['primes'] = 'primes';
			$result['product'] = 'product';
			$result['prsubset'] = 'prsubset';
			$result['quotient'] = 'quotient';
			$result['rationals'] = 'rationals';
			$result['real'] = 'real';
			$result['reals'] = 'reals';
			$result['reln'] = 'reln';
			$result['rem'] = 'rem';
			$result['root'] = 'root';
			$result['scalarproduct'] = 'scalarproduct';
			$result['sdev'] = 'sdev';
			$result['sec'] = 'sec';
			$result['sech'] = 'sech';
			$result['selector'] = 'selector';
			$result['semantics'] = 'semantics';
			$result['sep'] = 'sep';
			$result['set'] = 'set';
			$result['setdiff'] = 'setdiff';
			$result['sin'] = 'sin';
			$result['sinh'] = 'sinh';
			$result['subset'] = 'subset';
			$result['sum'] = 'sum';
			$result['tan'] = 'tan';
			$result['tanh'] = 'tanh';
			$result['tendsto'] = 'tendsto';
			$result['times'] = 'times';
			$result['transpose'] = 'transpose';
			$result['true'] = 'true';
			$result['union'] = 'union';
			$result['uplimit'] = 'uplimit';
			$result['variance'] = 'variance';
			$result['vector'] = 'vector';
			$result['vectorproduct'] = 'vectorproduct';
			$result['xor'] = 'xor';
			self::$tags = $result;
		}
		
		return self::$tags;
	}
	
}







?>