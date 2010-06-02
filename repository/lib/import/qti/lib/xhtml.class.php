<?php

/**
 * XHTML tags and helper functions.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class Xhtml{
	
	private static $tags = null;
	private static $attributes = null; 
	
	public static function get_tags(){
		if(empty(self::$tags)){
			$result['a'] = 'a';
			$result['abbr'] = 'abbr';
			$result['acronym'] = 'acronym';
			$result['address'] = 'address';
			$result['applet'] = 'applet';
			$result['area'] = 'area';
			$result['b'] = 'b';
			$result['base'] = 'base'; 
			$result['basefont'] = 'basefont'; 
			$result['bdo'] = 'bdo';
			$result['big'] = 'big';
			$result['blockquote'] = 'blockquote';
			$result['body'] = 'body';
			$result['br'] = 'br'; 
			$result['button'] = 'button';
			$result['caption'] = 'caption';
			$result['center'] = 'center';
			$result['cite'] = 'cite';
			$result['code'] = 'code';
			$result['col'] = 'col';
			$result['colgroup'] = 'colgroup';
			$result['dd'] = 'dd';
			$result['del'] = 'del';
			$result['dfn'] = 'dfn';
			$result['dir'] = 'dir';
			$result['div'] = 'div';
			$result['dl'] = 'dl';
			$result['dt'] = 'dt';
			$result['em'] = 'em';
			$result['fieldset'] = 'fieldset';
			$result['font'] = 'font';
			$result['form'] = 'form';
			$result['frame'] = 'frame';
			$result['frameset'] = 'frameset';
			$result['h1'] = 'h1';
			$result['h2'] = 'h2';
			$result['h3'] = 'h3';
			$result['h4'] = 'h4';
			$result['h5'] = 'h5';
			$result['h6'] = 'h6';
			$result['h7'] = 'h7';
			$result['h8'] = 'h8';
			$result['h9'] = 'h9';
			$result['head'] = 'head';
			$result['hr'] = 'hr'; 
			$result['html'] = 'html';
			$result['i'] = 'i';
			$result['iframe'] = 'iframe';
			$result['img'] = 'img';
			$result['input'] = 'input'; 
			$result['ins'] = 'ins';
			$result['isindex'] = 'isindex';
			$result['kbd'] = 'kbd';
			$result['label'] = 'label';
			$result['legend'] = 'legend';
			$result['li'] = 'li';
			$result['link'] = 'link';
			$result['map'] = 'map';
			$result['menu'] = 'menu';
			$result['meta'] = 'meta'; 
			$result['noframes'] = 'noframes';
			$result['noscript'] = 'noscript';
			$result['object'] = 'object';
			$result['ol'] = 'ol';
			$result['optgroup'] = 'optgroup';
			$result['option'] = 'option';
			$result['p'] = 'p';
			$result['param'] = 'param'; 
			$result['pre'] = 'pre';
			$result['q'] = 'q';
			$result['s'] = 's';
			$result['samp'] = 'samp';
			$result['script'] = 'script';
			$result['select'] = 'select';
			$result['small'] = 'small';
			$result['span'] = 'span';
			$result['strike'] = 'strike';
			$result['strong'] = 'strong';
			$result['style'] = 'style';
			$result['sub'] = 'sub';
			$result['sup'] = 'sup';
			$result['table'] = 'table';
			$result['tbody'] = 'tbody';
			$result['td'] = 'td';
			$result['textarea'] = 'textarea';
			$result['tfoot'] = 'tfoot';
			$result['th'] = 'th';
			$result['thead'] = 'thead';
			$result['title'] = 'title';
			$result['tr'] = 'tr';
			$result[ 'tt'] = 'tt';
			$result['u'] = 'u';
			$result['ul'] = 'ul';
			$result['var'] = 'var';
			self::$tags = $result;
		}
		return self::$tags ;
	}
	
	public static function get_attributes(){
		if(empty($tags)){
			$result = array();
			$result['abbr'] = 'abbr';
			$result['accept'] = 'accept';
			$result['accept-charset'] = 'accept-charset';
			$result['accesskey'] = 'accesskey';
			$result['action'] = 'action';
			$result['align'] = 'align';
			$result['alink'] = 'alink';
			$result['alt'] = 'alt';
			$result['archive'] = 'archive';
			$result['axis'] = 'axis';
			$result['background'] = 'background';
			$result['bgcolor'] = 'bgcolor';
			$result['border'] = 'border';
			$result['cellpadding'] = 'cellpadding';
			$result['cellspacing'] = 'cellspacing';
			$result['char'] = 'char';
			$result['charoff'] = 'charoff';
			$result['charset'] = 'charset';
			$result['checked'] = 'checked';
			$result['cite'] = 'cite';
			$result['class'] = 'class';
			$result['classid'] = 'classid';
			$result['clear'] = 'clear';
			$result['code'] = 'code';
			$result['codebase'] = 'codebase';
			$result['codetype'] = 'codetype';
			$result['color'] = 'color';
			$result['cols'] = 'cols';
			$result['colspan'] = 'colspan';
			$result['compact'] = 'compact';
			$result['content'] = 'content';
			$result['coords'] = 'coords';
			$result['data'] = 'data';
			$result['datetime'] = 'datetime';
			$result['declare'] = 'declare';
			$result['defer'] = 'defer';
			$result['dir'] = 'dir';
			$result['disabled'] = 'disabled';
			$result['enctype'] = 'enctype';
			$result['face'] = 'face';
			$result['for'] = 'for';
			$result['frame'] = 'frame';
			$result['frameborder'] = 'frameborder';
			$result['headers'] = 'headers';
			$result['height'] = 'height';
			$result['href'] = 'href';
			$result['hreflang'] = 'hreflang';
			$result['hspace'] = 'hspace';
			$result['http-equiv'] = 'http-equiv';
			$result['id'] = 'id';
			$result['ismap'] = 'ismap';
			$result['label'] = 'label';
			$result['lang'] = 'lang';
			$result['language'] = 'language';
			$result['link'] = 'link';
			$result['longdesc'] = 'longdesc';
			$result['marginheight'] = 'marginheight';
			$result['marginwidth'] = 'marginwidth';
			$result['maxlength'] = 'maxlength';
			$result['media'] = 'media';
			$result['method'] = 'method';
			$result['multiple'] = 'multiple';
			$result['name'] = 'name';
			$result['nohref'] = 'nohref';
			$result['noresize'] = 'noresize';
			$result['noshade'] = 'noshade';
			$result['nowrap'] = 'nowrap';
			$result['object'] = 'object';
			$result['onblur'] = 'onblur';
			$result['onchange'] = 'onchange';
			$result['onclick'] = 'onclick';
			$result['ondblclick'] = 'ondblclick';
			$result['onfocus'] = 'onfocus';
			$result['onkeydown'] = 'onkeydown';
			$result['onkeypress'] = 'onkeypress';
			$result['onkeyup'] = 'onkeyup';
			$result['onload'] = 'onload';
			$result['onmousedown'] = 'onmousedown';
			$result['onmousemove'] = 'onmousemove';
			$result['onmouseout'] = 'onmouseout';
			$result['onmouseover'] = 'onmouseover';
			$result['onmouseup'] = 'onmouseup';
			$result['onreset'] = 'onreset';
			$result['onselect'] = 'onselect';
			$result['onsubmit'] = 'onsubmit';
			$result['onunload'] = 'onunload';
			$result['profile'] = 'profile';
			$result['prompt'] = 'prompt';
			$result['readonly'] = 'readonly';
			$result['rel'] = 'rel';
			$result['rev'] = 'rev';
			$result['rows'] = 'rows';
			$result['rowspan'] = 'rowspan';
			$result['rules'] = 'rules';
			$result['scheme'] = 'scheme';
			$result['scope'] = 'scope';
			$result['scrolling'] = 'scrolling';
			$result['selected'] = 'selected';
			$result['shape'] = 'shape';
			$result['size'] = 'size';
			$result['span'] = 'span';
			$result['src'] = 'src';
			$result['standby'] = 'standby';
			$result['start'] = 'start';
			$result['style'] = 'style';
			$result['summary'] = 'summary';
			$result['tabindex'] = 'tabindex';
			$result['target'] = 'target';
			$result['text'] = 'text';
			$result['title'] = 'title';
			$result['type'] = 'type';
			$result['usemap'] = 'usemap';
			$result['valign'] = 'valign';
			$result['value'] = 'value';
			$result['valuetype'] = 'valuetype';
			$result['version'] = 'version';
			$result['vlink'] = 'vlink';
			$result['vspace'] = 'vspace';
			$result['width'] = 'width';
			self::$attributes = $result;
		}
		return self::$attributes;
	}
	
	public static function is($name){
		$tags = self::get_tags();
		return isset($tags[$name]);
	}
	
	public static function is_attribute($attribute_name){
		$attributes = self::get_attributes();
		return isset($attributes[$attribute_name]);
	}
	
}