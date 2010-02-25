/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
 */

(function() {
	var a = 1, b = 2, c = 4, d = {
		id : [ {
			type : a,
			name : 'id'
		} ],
		classid : [ {
			type : a,
			name : 'classid'
		} ],
		codebase : [ {
			type : a,
			name : 'codebase'
		} ],
		pluginspage : [ {
			type : c,
			name : 'pluginspage'
		} ],
		src : [ {
			type : b,
			name : 'movie'
		}, {
			type : c,
			name : 'src'
		} ],
		name : [ {
			type : c,
			name : 'name'
		} ],
		align : [ {
			type : a,
			name : 'align'
		} ],
		title : [ {
			type : a,
			name : 'title'
		}, {
			type : c,
			name : 'title'
		} ],
		'class' : [ {
			type : a,
			name : 'class'
		}, {
			type : c,
			name : 'class'
		} ],
		width : [ {
			type : a,
			name : 'width'
		}, {
			type : c,
			name : 'width'
		} ],
		height : [ {
			type : a,
			name : 'height'
		}, {
			type : c,
			name : 'height'
		} ],
		hSpace : [ {
			type : a,
			name : 'hSpace'
		}, {
			type : c,
			name : 'hSpace'
		} ],
		vSpace : [ {
			type : a,
			name : 'vSpace'
		}, {
			type : c,
			name : 'vSpace'
		} ],
		style : [ {
			type : a,
			name : 'style'
		}, {
			type : c,
			name : 'style'
		} ],
		type : [ {
			type : c,
			name : 'type'
		} ]
	}, e = [ 'play', 'loop', 'menu', 'quality', 'scale', 'salign', 'wmode',
			'bgcolor', 'base', 'youtubevars', 'allowScriptAccess',
			'allowFullScreen' ];
	for ( var f = 0; f < e.length; f++)
		d[e[f]] = [ {
			type : c,
			name : e[f]
		}, {
			type : b,
			name : e[f]
		} ];
	e = [ 'allowFullScreen', 'play', 'loop', 'menu' ];
	for (f = 0; f < e.length; f++)
		d[e[f]][0]['default'] = d[e[f]][1]['default'] = true;
	function g(i, j, k) {
		var q = this;
		var l = d[q.id];
		if (!l)
			return;
		var m = q instanceof CKEDITOR.ui.dialog.checkbox;
		for ( var n = 0; n < l.length; n++) {
			var o = l[n];
			switch (o.type) {
			case a:
				if (!i)
					continue;
				if (i.getAttribute(o.name) !== null) {
					var p = i.getAttribute(o.name);
					if (m)
						q.setValue(p.toLowerCase() == 'true');
					else
						q.setValue(p);
					return;
				} else if (m)
					q.setValue(!!o['default']);
				break;
			case b:
				if (!i)
					continue;
				if (o.name in k) {
					p = k[o.name];
					if (m)
						q.setValue(p.toLowerCase() == 'true');
					else
						q.setValue(p);
					return;
				} else if (m)
					q.setValue(!!o['default']);
				break;
			case c:
				if (!j)
					continue;
				if (j.getAttribute(o.name)) {
					p = j.getAttribute(o.name);
					if (m)
						q.setValue(p.toLowerCase() == 'true');
					else
						q.setValue(p);
					return;
				} else if (m)
					q.setValue(!!o['default']);
			}
		}
	}
	;
	function h(i, j, k) {
		var s = this;
		var l = d[s.id];
		if (!l)
			return;
		var m = s.getValue() === '', n = s instanceof CKEDITOR.ui.dialog.checkbox;
		for ( var o = 0; o < l.length; o++) {
			var p = l[o];
			switch (p.type) {
			case a:
				if (!i)
					continue;
				var q = s.getValue();
				if (m || n && q === p['default'])
					i.removeAttribute(p.name);
				else
					i.setAttribute(p.name, q);
				break;
			case b:
				if (!i)
					continue;
				q = s.getValue();
				if (m || n && q === p['default']) {
					if (p.name in k)
						k[p.name].remove();
				} else if (p.name in k)
					k[p.name].setAttribute('value', q);
				else {
					var r = CKEDITOR.dom.element.createFromHtml(
							'<cke:param></cke:param>', i.getDocument());
					r.setAttributes( {
						name : p.name,
						value : q
					});
					if (i.getChildCount() < 1)
						r.appendTo(i);
					else
						r.insertBefore(i.getFirst());
				}
				break;
			case c:
				if (!j)
					continue;
				q = s.getValue();
				if (m || n && q === p['default'])
					j.removeAttribute(p.name);
				else
					j.setAttribute(p.name, q);
			}
		}
	}
	;
	CKEDITOR.dialog
			.add(
					'youtube',
					function(i) {
						var j = !i.config.youtubeEmbedTagOnly, k = i.config.youtubeAddEmbedTag
								|| i.config.youtubeEmbedTagOnly, l, m = '<div>'
								+ CKEDITOR.tools
										.htmlEncode(i.lang.image.preview)
								+ '<br>'
								+ '<div id="YoutubePreviewLoader" style="display:none"><div class="loading">&nbsp;</div></div>'
								+ '<div id="YoutubePreviewBox"></div></div>';
						return {
							title : i.lang.youtube.title,
							minWidth : 420,
							minHeight : 310,
							onShow : function() {
								var z = this;
								z.fakeImage = z.objectNode = z.embedNode = null;
								l = new CKEDITOR.dom.element('embeded',
										i.document);
								var n = z.getSelectedElement();
								if (n
										&& n
												.getAttribute('_cke_real_element_type')
										&& n
												.getAttribute('_cke_real_element_type') == 'youtube') {
									z.fakeImage = n;
									var o = i.restoreRealElement(n), p = null, q = null, r = {};
									if (o.getName() == 'cke:object') {
										p = o;
										var s = p.getElementsByTag('embed',
												'cke');
										if (s.count() > 0)
											q = s.getItem(0);
										var t = p.getElementsByTag('param',
												'cke');
										for ( var u = 0, v = t.count(); u < v; u++) {
											var w = t.getItem(u), x = w
													.getAttribute('name'), y = w
													.getAttribute('value');
											r[x] = y;
										}
									} else if (o.getName() == 'cke:embed')
										q = o;
									z.objectNode = p;
									z.embedNode = q;
									z.setupContent(p, q, r, n);
								}
							},
							onOk : function() {
								var w = this;
								var n = null, o = null, p = null;
								if (!w.fakeImage) {
									if (j) {
										n = CKEDITOR.dom.element
												.createFromHtml(
														'<cke:object></cke:object>',
														i.document);
										var q = {
											classid : 'clsid:d27cdb6e-ae6d-11cf-96b8-444553540000',
											codebase : 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0'
										};
										n.setAttributes(q);
									}
									if (k) {
										o = CKEDITOR.dom.element
												.createFromHtml(
														'<cke:embed></cke:embed>',
														i.document);
										o
												.setAttributes( {
													type : 'application/x-shockwave-flash',
													pluginspage : 'http://www.macromedia.com/go/getflashplayer'
												});
										if (n)
											o.appendTo(n);
									}
								} else {
									n = w.objectNode;
									o = w.embedNode;
								}
								if (n) {
									p = {};
									var r = n.getElementsByTag('param', 'cke');
									for ( var s = 0, t = r.count(); s < t; s++)
										p[r.getItem(s).getAttribute('name')] = r
												.getItem(s);
								}
								var u = {};
								w.commitContent(n, o, p, u);
								var v = i.createFakeElement(n || o,
										'cke_youtube', 'youtube', true);
								v.setStyles(u);
								if (w.fakeImage) {
									v.replace(w.fakeImage);
									i.getSelection().selectElement(v);
								} else
									i.insertElement(v);
							},
							onHide : function() {
								if (this.preview)
									this.preview.setHtml('');
							},
							contents : [
									{
										id : 'info',
										label : i.lang.common.generalTab,
										accessKey : 'I',
										elements : [
												{
													type : 'vbox',
													padding : 0,
													children : [
															{
																type : 'html',
																html : '<span>' + CKEDITOR.tools
																		.htmlEncode(i.lang.image.url) + '</span>'
															},
															{
																type : 'hbox',
																widths : [
																		'280px',
																		'110px' ],
																align : 'right',
																children : [
																		{
																			id : 'src',
																			type : 'text',
																			label : '',
																			validate : CKEDITOR.dialog.validate
																					.notEmpty(i.lang.youtube.validateSrc),
																			setup : g,
																			commit : h,
																			onLoad : function() {
																				var n = this
																						.getDialog(), o = function(
																						p) {
																					l
																							.setAttribute(
																									'src',
																									p);
																					n.preview
																							.setHtml('<embed height="100%" width="100%" src="' + CKEDITOR.tools
																									.htmlEncode(l
																											.getAttribute('src')) + '" type="application/x-shockwave-flash"></embed>');
																				};
																				n.preview = n
																						.getContentElement(
																								'info',
																								'preview')
																						.getElement()
																						.getChild(
																								3);
																				this
																						.on(
																								'change',
																								function(
																										p) {
																									if (p.data
																											&& p.data.value)
																										o(p.data.value);
																								});
																				this
																						.getInputElement()
																						.on(
																								'change',
																								function(
																										p) {
																									o(this
																											.getValue());
																								},
																								this);
																			}
																		},
																		{
																			type : 'button',
																			id : 'browse',
																			filebrowser : 'info:src',
																			hidden : true,
																			align : 'center',
																			label : i.lang.common.browseServer
																		} ]
															} ]
												},
												{
													type : 'hbox',
													widths : [ '33%', '33%', '33%' ],
													children : [
															{
																type : 'text',
																id : 'width',
																style : 'width:125px',
																label : i.lang.youtube.width,
																validate : CKEDITOR.dialog.validate
																		.integer(i.lang.youtube.validateWidth),
																setup : function(
																		n, o,
																		p, q) {
																	g
																			.apply(
																					this,
																					arguments);
																	if (q) {
																		var r = parseInt(
																				q.$.style.width,
																				10);
																		if (!isNaN(r))
																			this
																					.setValue(r);
																	}
																},
																commit : function(
																		n, o,
																		p, q) {
																	h
																			.apply(
																					this,
																					arguments);
																	if (this
																			.getValue())
																		q.width = this
																				.getValue() + 'px';
																}
															},
															{
																type : 'text',
																id : 'height',
																style : 'width:125px',
																label : i.lang.youtube.height,
																validate : CKEDITOR.dialog.validate
																		.integer(i.lang.youtube.validateHeight),
																setup : function(
																		n, o,
																		p, q) {
																	g
																			.apply(
																					this,
																					arguments);
																	if (q) {
																		var r = parseInt(
																				q.$.style.height,
																				10);
																		if (!isNaN(r))
																			this
																					.setValue(r);
																	}
																},
																commit : function(
																		n, o,
																		p, q) {
																	h
																			.apply(
																					this,
																					arguments);
																	if (this
																			.getValue())
																		q.height = this
																				.getValue() + 'px';
																}
															}, {
																id : 'scale',
																type : 'select',
																label : i.lang.youtube.quality,
																'default' : '',
																style : 'width : 100%;',
																items : [
																		[
																				i.lang.youtube.low,
																				'low' ],
																		[
																				i.lang.youtube.high,
																				'high' ] ],
																setup : g,
																commit : h
															} ]
												}, {
													type : 'vbox',
													children : [ {
														type : 'html',
														id : 'preview',
														style : 'width:95%;',
														html : m
													}]
												} ]
									} ]
						};
					});
})();
