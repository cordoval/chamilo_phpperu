/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function()
{
	/*
	 * It is possible to set things in three different places.
	 * 1. As attributes in the object tag.
	 * 2. As param tags under the object tag.
	 * 3. As attributes in the embed tag.
	 * It is possible for a single attribute to be present in more than one place.
	 * So let's define a mapping between a sementic attribute and its syntactic
	 * equivalents.
	 * Then we'll set and retrieve attribute values according to the mapping,
	 * instead of having to check and set each syntactic attribute every time.
	 *
	 * Reference: http://kb.adobe.com/selfservice/viewContent.do?externalId=tn_12701
	 */
	var ATTRTYPE_OBJECT = 1,
		ATTRTYPE_PARAM = 2,
		ATTRTYPE_EMBED = 4;

	var attributesMap =
	{
//		id : [ { type : ATTRTYPE_OBJECT, name :  'id' } ],
		classid : [ { type : ATTRTYPE_OBJECT, name : 'classid' } ],
		codebase : [  { type : ATTRTYPE_PARAM, name : 'codebase'}, { type : ATTRTYPE_EMBED, name : 'codebase'} ],
		pluginspage : [ { type : ATTRTYPE_PARAM, name : 'pluginspage' }, { type : ATTRTYPE_EMBED, name : 'pluginspage' } ],
		src : [ { type : ATTRTYPE_PARAM, name : 'url' }, { type : ATTRTYPE_EMBED, name : 'src' } ],
		
		autosize : [ { type : ATTRTYPE_EMBED, name : 'autosize' } ],
		autostart : [ { type : ATTRTYPE_PARAM, name : 'autostart' }, { type : ATTRTYPE_EMBED, name : 'autostart' } ],
		showcontrols : [ { type : ATTRTYPE_PARAM, name : 'showcontrols' }, { type : ATTRTYPE_EMBED, name : 'showcontrols' } ],
		showpositioncontrols : [ { type : ATTRTYPE_PARAM, name : 'showpositioncontrols' }, { type : ATTRTYPE_EMBED, name : 'showpositioncontrols' } ],
		showtracker : [ { type : ATTRTYPE_PARAM, name : 'showtracker' }, { type : ATTRTYPE_EMBED, name : 'showtracker' } ],
		showaudiocontrols : [ { type : ATTRTYPE_PARAM, name : 'showaudiocontrols' }, { type : ATTRTYPE_EMBED, name : 'showaudiocontrols' } ],
		showgotobar : [ { type : ATTRTYPE_PARAM, name : 'showgotobar' }, { type : ATTRTYPE_EMBED, name : 'showgotobar' } ],
		showstatusbar : [ { type : ATTRTYPE_PARAM, name : 'showstatusbar' }, { type : ATTRTYPE_EMBED, name : 'showstatusbar' } ],
//		standby : [ { type : ATTRTYPE_PARAM, name : 'standby' } ],
		
		type : [ { type : ATTRTYPE_EMBED, name : 'type' } ]
	};

//	var names = [ 'play', 'loop', 'menu', 'quality', 'scale', 'salign', 'wmode', 'bgcolor', 'base', 'chamilovideovars', 'allowScriptAccess',
//		'allowFullScreen' ];
//	for ( var i = 0 ; i < names.length ; i++ )
//		attributesMap[ names[i] ] = [ { type : ATTRTYPE_EMBED, name : names[i] }, { type : ATTRTYPE_PARAM, name : names[i] } ];
//	names = [ 'allowFullScreen', 'play', 'loop', 'menu' ];
//	for ( i = 0 ; i < names.length ; i++ )
//		attributesMap[ names[i] ][0]['default'] = attributesMap[ names[i] ][1]['default'] = true;

	function loadValue( objectNode, embedNode, paramMap )
	{
		var attributes = attributesMap[ this.id ];
		if ( !attributes )
			return;

		var isCheckbox = ( this instanceof CKEDITOR.ui.dialog.checkbox );
		for ( var i = 0 ; i < attributes.length ; i++ )
		{
			var attrDef = attributes[ i ];
			switch ( attrDef.type )
			{
				case ATTRTYPE_OBJECT:
					if ( !objectNode )
						continue;
					if ( objectNode.getAttribute( attrDef.name ) !== null )
					{
						var value = objectNode.getAttribute( attrDef.name );
						if ( isCheckbox )
							this.setValue( value.toLowerCase() == 'true' );
						else
							this.setValue( value );
						return;
					}
					else if ( isCheckbox )
						this.setValue( !!attrDef[ 'default' ] );
					break;
				case ATTRTYPE_PARAM:
					if ( !objectNode )
						continue;
					if ( attrDef.name in paramMap )
					{
						value = paramMap[ attrDef.name ];
						if ( isCheckbox )
							this.setValue( value.toLowerCase() == 'true' );
						else
							this.setValue( value );
						return;
					}
					else if ( isCheckbox )
						this.setValue( !!attrDef[ 'default' ] );
					break;
				case ATTRTYPE_EMBED:
					if ( !embedNode )
						continue;
					if ( embedNode.getAttribute( attrDef.name ) )
					{
						value = embedNode.getAttribute( attrDef.name );
						if ( isCheckbox )
							this.setValue( value.toLowerCase() == 'true' );
						else
							this.setValue( value );
						return;
					}
					else if ( isCheckbox )
						this.setValue( !!attrDef[ 'default' ] );
			}
		}
	}

	function commitValue( objectNode, embedNode, paramMap )
	{
		var attributes = attributesMap[ this.id ];
		if ( !attributes )
			return;

		var isRemove = ( this.getValue() === '' ),
			isCheckbox = ( this instanceof CKEDITOR.ui.dialog.checkbox );

		for ( var i = 0 ; i < attributes.length ; i++ )
		{
			var attrDef = attributes[i];
			switch ( attrDef.type )
			{
				case ATTRTYPE_OBJECT:
					if ( !objectNode )
						continue;
					var value = this.getValue();
					if ( isRemove || isCheckbox && value === attrDef[ 'default' ] )
						objectNode.removeAttribute( attrDef.name );
					else
						objectNode.setAttribute( attrDef.name, value );
					break;
				case ATTRTYPE_PARAM:
					if ( !objectNode )
						continue;
					value = this.getValue();
					if ( isRemove || isCheckbox && value === attrDef[ 'default' ] )
					{
						if ( attrDef.name in paramMap )
							paramMap[ attrDef.name ].remove();
					}
					else
					{
						if ( attrDef.name in paramMap )
							paramMap[ attrDef.name ].setAttribute( 'value', value );
						else
						{
							var param = CKEDITOR.dom.element.createFromHtml( '<cke:param></cke:param>', objectNode.getDocument() );
							param.setAttributes( { name : attrDef.name, value : value } );
							if ( objectNode.getChildCount() < 1 )
								param.appendTo( objectNode );
							else
								param.insertBefore( objectNode.getFirst() );
						}
					}
					break;
				case ATTRTYPE_EMBED:
					if ( !embedNode )
						continue;
					value = this.getValue();
					if ( isRemove || isCheckbox && value === attrDef[ 'default' ])
						embedNode.removeAttribute( attrDef.name );
					else
						embedNode.setAttribute( attrDef.name, value );
			}
		}
	}

	CKEDITOR.dialog.add( 'chamilovideo', function( editor )
	{
		var makeObjectTag = !editor.config.chamilovideoEmbedTagOnly,
			makeEmbedTag = editor.config.chamilovideoAddEmbedTag || editor.config.chamilovideoEmbedTagOnly;

		var previewPreloader,
			previewAreaHtml = '<div>' + CKEDITOR.tools.htmlEncode( editor.lang.common.preview ) +'<br>' +
			'<div id="ChamilovideoPreviewLoader" style="display:none"><div class="loading">&nbsp;</div></div>' +
			'<div id="ChamilovideoPreviewBox"></div></div>';

		return {
			title : editor.lang.chamilovideo.title,
			minWidth : 420,
			minHeight : 310,
			onShow : function()
			{
				// Clear previously saved elements.
				this.fakeImage = this.objectNode = this.embedNode = null;
				previewPreloader = new CKEDITOR.dom.element( 'embeded', editor.document );

				// Try to detect any embed or object tag that has Video parameters.
				var fakeImage = this.getSelectedElement();
				if ( fakeImage && fakeImage.getAttribute( '_cke_real_element_type' ) && fakeImage.getAttribute( '_cke_real_element_type' ) == 'chamilovideo' )
				{
					this.fakeImage = fakeImage;

					var realElement = editor.restoreRealElement( fakeImage ),
						objectNode = null, embedNode = null, paramMap = {};
					if ( realElement.getName() == 'cke:object' )
					{
						objectNode = realElement;
						var embedList = objectNode.getElementsByTag( 'embed', 'cke' );
						if ( embedList.count() > 0 )
							embedNode = embedList.getItem( 0 );
						var paramList = objectNode.getElementsByTag( 'param', 'cke' );
						for ( var i = 0, length = paramList.count() ; i < length ; i++ )
						{
							var item = paramList.getItem( i ),
								name = item.getAttribute( 'name' ),
								value = item.getAttribute( 'value' );
							paramMap[ name ] = value;
						}
					}
					else if ( realElement.getName() == 'cke:embed' )
						embedNode = realElement;

					this.objectNode = objectNode;
					this.embedNode = embedNode;

					this.setupContent( objectNode, embedNode, paramMap, fakeImage );
				}
			},
			onOk : function()
			{
				// If there's no selected object or embed, create one. Otherwise, reuse the
				// selected object and embed nodes.
				var objectNode = null,
					embedNode = null,
					paramMap = null;
				if ( !this.fakeImage )
				{
					if ( makeObjectTag )
					{
						objectNode = CKEDITOR.dom.element.createFromHtml( '<cke:object></cke:object>', editor.document );
						var attributes = {
							classid : 'classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95',
							codebase : 'http://www.microsoft.com/Windows/MediaPlayer/'
						};
						objectNode.setAttributes( attributes );
					}
					if ( makeEmbedTag )
					{
						embedNode = CKEDITOR.dom.element.createFromHtml( '<cke:embed></cke:embed>', editor.document );
						embedNode.setAttributes(
							{
								type : 'video/x-msvideo',
								pluginspage : 'http://www.microsoft.com/Windows/MediaPlayer/'
							} );
						if ( objectNode )
							embedNode.appendTo( objectNode );
					}
				}
				else
				{
					objectNode = this.objectNode;
					embedNode = this.embedNode;
				}

				// Produce the paramMap if there's an object tag.
				if ( objectNode )
				{
					paramMap = {};
					var paramList = objectNode.getElementsByTag( 'param', 'cke' );
					for ( var i = 0, length = paramList.count() ; i < length ; i++ )
						paramMap[ paramList.getItem( i ).getAttribute( 'name' ) ] = paramList.getItem( i );
				}

				// Apply or remove chamilovideo parameters.
				var extraStyles = {};
				this.commitContent( objectNode, embedNode, paramMap, extraStyles );

				// Refresh the fake image.
				var newFakeImage = editor.createFakeElement( objectNode || embedNode, 'cke_chamilovideo', 'chamilovideo', true );
				newFakeImage.setStyles( extraStyles );
				if ( this.fakeImage )
				{
					newFakeImage.replace( this.fakeImage );
					editor.getSelection().selectElement( newFakeImage );
				}
				else
					editor.insertElement( newFakeImage );
			},

			onHide : function()
			{
				if ( this.preview )
					this.preview.setHtml('');
			},

			contents : [
				{
					id : 'info',
					label : editor.lang.common.generalTab,
					accessKey : 'I',
					elements :
					[
						{
							type : 'vbox',
							padding : 0,
							children :
							[
								{
									type : 'hbox',
									widths : [ '280px', '110px' ],
									align : 'right',
									children :
									[
										{
											id : 'src',
											type : 'text',
											label : editor.lang.common.url,
											required : true,
											validate : CKEDITOR.dialog.validate.notEmpty( editor.lang.chamilovideo.validateSrc ),
											setup : loadValue,
											commit : commitValue,
											onLoad : function()
											{
												var dialog = this.getDialog(),
												updatePreview = function( src ){
													// Query the preloader to figure out the url impacted by based href.
													previewPreloader.setAttribute( 'src', src );
													dialog.preview.setHtml( '<embed height="100%" width="100%" src="'
														+ CKEDITOR.tools.htmlEncode( previewPreloader.getAttribute( 'src' ) )
														+ '" type="video/x-msvideo"></embed>' );
												};
												// Preview element
												dialog.preview = dialog.getContentElement( 'info', 'preview' ).getElement().getChild( 3 );

												// Sync on inital value loaded.
												this.on( 'change', function( evt ){

														if ( evt.data && evt.data.value )
															updatePreview( evt.data.value );
													} );
												// Sync when input value changed.
												this.getInputElement().on( 'change', function( evt ){

													updatePreview( this.getValue() );
												}, this );
											}
										},
										{
											type : 'button',
											id : 'browse',
											filebrowser : 'info:src',
											hidden : true,
											// v-align with the 'src' field.
											// TODO: We need something better than a fixed size here.
											style : 'display:inline-block;margin-top:10px;',
											label : editor.lang.common.browseServer
										}
									]
								}
							]
						},
						{
							type : 'vbox',
							children :
							[
								{
									type : 'html',
									id : 'preview',
									style : 'width:95%;',
									html : previewAreaHtml
								}
							]
						}
					]
				},
				{
					id : 'properties',
					label : editor.lang.chamilovideo.propertiesTab,
					elements :
					[
						{
							type : 'vbox',
							padding : 0,
							children :
							[
								{
									type : 'html',
									html : CKEDITOR.tools.htmlEncode( editor.lang.chamilovideo.videoOptions )
								},
								{
									type : 'checkbox',
									id : 'autosize',
									label : editor.lang.chamilovideo.autoSize,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'autostart',
									label : editor.lang.chamilovideo.autoStart,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'showcontrols',
									label : editor.lang.chamilovideo.showControls,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'showpositioncontrols',
									label : editor.lang.chamilovideo.showPositionControls,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'showtracker',
									label : editor.lang.chamilovideo.showTracker,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'showaudiocontrols',
									label : editor.lang.chamilovideo.showAudioControls,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'showgotobar',
									label : editor.lang.chamilovideo.showGoToBar,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'showstatusbar',
									label : editor.lang.chamilovideo.showStatusBar,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								}
							]
						}
					]
				}
			]
		};
	} );
})();
