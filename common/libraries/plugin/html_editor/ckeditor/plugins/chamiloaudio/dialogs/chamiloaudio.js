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
		classid : [ { type : ATTRTYPE_OBJECT, name : 'classid' } ],
		codebase : [  { type : ATTRTYPE_PARAM, name : 'codebase'}, { type : ATTRTYPE_EMBED, name : 'codebase'} ],
		pluginspage : [ { type : ATTRTYPE_PARAM, name : 'pluginspage' }, { type : ATTRTYPE_EMBED, name : 'pluginspage' } ],
		src : [ { type : ATTRTYPE_PARAM, name : 'url' }, { type : ATTRTYPE_EMBED, name : 'src' } ],
		
		width : [ { type : ATTRTYPE_PARAM, name : 'width' }, { type : ATTRTYPE_EMBED, name : 'width' } ],
		height : [ { type : ATTRTYPE_PARAM, name : 'height' }, { type : ATTRTYPE_EMBED, name : 'height' } ],
		
		autostart : [ { type : ATTRTYPE_PARAM, name : 'autostart' }, { type : ATTRTYPE_EMBED, name : 'autostart' } ],
		showcontrols : [ { type : ATTRTYPE_PARAM, name : 'showcontrols' }, { type : ATTRTYPE_EMBED, name : 'showcontrols' } ],
		showpositioncontrols : [ { type : ATTRTYPE_PARAM, name : 'showpositioncontrols' }, { type : ATTRTYPE_EMBED, name : 'showpositioncontrols' } ],
		showaudiocontrols : [ { type : ATTRTYPE_PARAM, name : 'showaudiocontrols' }, { type : ATTRTYPE_EMBED, name : 'showaudiocontrols' } ],
		showstatusbar : [ { type : ATTRTYPE_PARAM, name : 'showstatusbar' }, { type : ATTRTYPE_EMBED, name : 'showstatusbar' } ],
		
		type : [ { type : ATTRTYPE_EMBED, name : 'type' } ]
	};

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

	CKEDITOR.dialog.add( 'chamiloaudio', function( editor )
	{
		var makeObjectTag = !editor.config.chamiloaudioEmbedTagOnly,
			makeEmbedTag = editor.config.chamiloaudioAddEmbedTag || editor.config.chamiloaudioEmbedTagOnly;

		var previewPreloader,
			previewAreaHtml = '<div>' + CKEDITOR.tools.htmlEncode( editor.lang.common.preview ) +'<br>' +
			'<div id="ChamiloaudioPreviewLoader" style="display:none"><div class="loading">&nbsp;</div></div>' +
			'<div id="ChamiloaudioPreviewBox"></div></div>';

		return {
			title : editor.lang.chamiloaudio.title,
			minWidth : 420,
			minHeight : 310,
			onShow : function()
			{
				// Clear previously saved elements.
				this.fakeImage = this.objectNode = this.embedNode = null;
				previewPreloader = new CKEDITOR.dom.element( 'embeded', editor.document );

				// Try to detect any embed or object tag that has Audio parameters.
				var fakeImage = this.getSelectedElement();
				if ( fakeImage && fakeImage.getAttribute( '_cke_real_element_type' ) && fakeImage.getAttribute( '_cke_real_element_type' ) == 'chamiloaudio' )
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
//							classid : 'CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95',
							codebase : 'http://www.videolan.org/'
						};
						objectNode.setAttributes( attributes );
					}
					if ( makeEmbedTag )
					{
						embedNode = CKEDITOR.dom.element.createFromHtml( '<cke:embed></cke:embed>', editor.document );
						embedNode.setAttributes(
							{
								pluginspage : 'http://www.videolan.org/'
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

				// Apply or remove chamiloaudio parameters.
				var extraStyles = {};
				this.commitContent( objectNode, embedNode, paramMap, extraStyles );

				// Refresh the fake image.
				var newFakeImage = editor.createFakeElement( objectNode || embedNode, 'cke_chamiloaudio', 'chamiloaudio', true );
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
											validate : CKEDITOR.dialog.validate.notEmpty( editor.lang.chamiloaudio.validateSrc ),
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
														+ '" type="audio/x-msaudio"></embed>' );
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
							type : 'hbox',
							widths : [ '100%' ],
							children :
							[
								{
									type : 'select',
									id : 'type',
//									style : 'width:125px',
									label : editor.lang.chamiloaudio.type,
									validate : CKEDITOR.dialog.validate.notEmpty( editor.lang.chamiloaudio.validateType ),
									style : 'width : 100%;',
									items :
									[
										[ 'Mp3', 'audio/mpeg' ],
										[ 'Windows Media Audio', 'video/x-ms-wma' ],
										[ 'Ogg', 'audio/ogg' ],
										[ 'Aac', 'audio/aac' ],
										[ 'M4a', 'audio/x-m4a' ],
										[ 'Midi', 'audio/midi' ],
										[ 'Wav', 'audio/x-wav' ]
									],
									'default' : 'audio/mpeg',
									setup : loadValue,
									commit : commitValue
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
					label : editor.lang.chamiloaudio.propertiesTab,
					elements :
					[
						{
							type : 'vbox',
							padding : 0,
							children :
							[
								{
									type : 'html',
									html : CKEDITOR.tools.htmlEncode( editor.lang.chamiloaudio.audioOptions )
								},
								{
									type : 'checkbox',
									id : 'autosize',
									label : editor.lang.chamiloaudio.autoSize,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'autostart',
									label : editor.lang.chamiloaudio.autoStart,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'showcontrols',
									label : editor.lang.chamiloaudio.showControls,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'showpositioncontrols',
									label : editor.lang.chamiloaudio.showPositionControls,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'showtracker',
									label : editor.lang.chamiloaudio.showTracker,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'showaudiocontrols',
									label : editor.lang.chamiloaudio.showAudioControls,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'showgotobar',
									label : editor.lang.chamiloaudio.showGoToBar,
									'default' : true,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'showstatusbar',
									label : editor.lang.chamiloaudio.showStatusBar,
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
