/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
 */

(function() {
	var a = /\.swf(?:$|\?)/i, b = /^\d+(?:\.\d+)?$/;
	function c(f) {
		if (b.test(f))
			return f + 'px';
		return f;
	}
	;
	function d(f) {
		var g = f.attributes;
		return g.type == 'application/x-shockwave-flash' || a.test(g.src || '');
	}
	;
	function e(f, g) {
		var h = f.createFakeParserElement(g, 'cke_youtube', 'youtube', true), i = h.attributes.style || '', j = g.attributes.width, k = g.attributes.height;
		if (typeof j != 'undefined')
			i = h.attributes.style = i + 'width:' + c(j) + ';';
		if (typeof k != 'undefined')
			i = h.attributes.style = i + 'height:' + c(k) + ';';
		return h;
	}
	;
	CKEDITOR.plugins
			.add(
					'youtube',
					{
						init : function(f) {
							f.addCommand('youtube', new CKEDITOR.dialogCommand(
									'youtube'));
							f.ui.addButton('Youtube', {
								label : f.lang.common.youtube,
								command : 'youtube',
								icon: this.path + "youtube.png"
							});
							CKEDITOR.dialog.add('youtube',
									this.path + 'dialogs/youtube.js');
							f
									.addCss('img.cke_youtube{background-image: url('
											+ CKEDITOR
													.getUrl(this.path + 'images/placeholder.png')
											+ ');'
											+ 'background-position: center center;'
											+ 'background-repeat: no-repeat;'
											+ 'border: 1px solid #a9a9a9;'
											+ 'width: 80px;'
											+ 'height: 80px;'
											+ '}');
							if (f.addMenuItems)
								f.addMenuItems( {
									youtube : {
										label : f.lang.youtube.properties,
										command : 'youtube',
										group : 'youtube'
									}
								});
							if (f.contextMenu)
								f.contextMenu
										.addListener(function(g, h) {
											if (g
													&& g.is('img')
													&& g
															.getAttribute('_cke_real_element_type') == 'youtube')
												return {
													youtube : CKEDITOR.TRISTATE_OFF
												};
										});
						},
						afterInit : function(f) {
							var g = f.dataProcessor, h = g && g.dataFilter;
							if (h)
								h
										.addRules(
												{
													elements : {
														'cke:object' : function(
																i) {
															var j = i.attributes, k = j.classid
																	&& String(
																			j.classid)
																			.toLowerCase();
															if (!k) {
																for ( var l = 0; l < i.children.length; l++)
																	if (i.children[l].name == 'embed') {
																		if (!d(i.children[l]))
																			return null;
																		return e(
																				f,
																				i);
																	}
																return null;
															}
															return e(f, i);
														},
														'cke:embed' : function(
																i) {
															if (!d(i))
																return null;
															return e(f, i);
														}
													}
												}, 5);
						},
						requires : [ 'fakeobjects' ]
					});
})();
CKEDITOR.tools.extend(CKEDITOR.config, {
	youtubeEmbedTagOnly : false,
	youtubeAddEmbedTag : true,
	youtubeConvertOnEdit : false
});
