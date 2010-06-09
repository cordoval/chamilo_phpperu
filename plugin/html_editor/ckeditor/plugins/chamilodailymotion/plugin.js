/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function()
{
	var chamilodailymotionFilenameRegex = /\.dailymotion\./,
		numberRegex = /^\d+(?:\.\d+)?$/;

	function cssifyLength( length )
	{
		if ( numberRegex.test( length ) )
			return length + 'px';
		return length;
	}

	function isChamilodailymotionEmbed( element )
	{
		var attributes = element.attributes;

		return chamilodailymotionFilenameRegex.test( attributes.src );
	}

	function createFakeElement( editor, realElement )
	{
		var fakeElement = editor.createFakeParserElement( realElement, 'cke_chamilodailymotion', 'chamilodailymotion', true ),
			fakeStyle = fakeElement.attributes.style || '';

		var width = realElement.attributes.width,
			height = realElement.attributes.height;

		if ( typeof width != 'undefined' )
			fakeStyle = fakeElement.attributes.style = fakeStyle + 'width:' + cssifyLength( width ) + ';';

		if ( typeof height != 'undefined' )
			fakeStyle = fakeElement.attributes.style = fakeStyle + 'height:' + cssifyLength( height ) + ';';

		return fakeElement;
	}

	CKEDITOR.plugins.add( 'chamilodailymotion',
	{
		init : function( editor )
		{
			editor.addCommand( 'chamilodailymotion', new CKEDITOR.dialogCommand( 'chamilodailymotion' ) );
			editor.ui.addButton( 'Chamilodailymotion',
				{
					label : editor.lang.common.chamilodailymotion,
					command : 'chamilodailymotion',
					icon: this.path + 'chamilodailymotion.png'
				});
			CKEDITOR.dialog.add( 'chamilodailymotion', this.path + 'dialogs/chamilodailymotion.js' );

			editor.addCss(
				'img.cke_chamilodailymotion' +
				'{' +
					'background-image: url(' + CKEDITOR.getUrl( this.path + 'images/placeholder.png' ) + ');' +
					'background-position: center center;' +
					'background-repeat: no-repeat;' +
					'border: 1px solid #a9a9a9;' +
					'width: 80px;' +
					'height: 80px;' +
				'}'
				);

			// If the "menu" plugin is loaded, register the menu items.
			if ( editor.addMenuItems )
			{
				editor.addMenuItems(
					{
						chamilodailymotion :
						{
							label : editor.lang.chamilodailymotion.properties,
							command : 'chamilodailymotion',
							group : 'chamilodailymotion'
						}
					});
			}

			// If the "contextmenu" plugin is loaded, register the listeners.
			if ( editor.contextMenu )
			{
				editor.contextMenu.addListener( function( element, selection )
					{
						if ( element && element.is( 'img' ) && element.getAttribute( '_cke_real_element_type' ) == 'chamilodailymotion' )
							return { chamilodailymotion : CKEDITOR.TRISTATE_OFF };
					});
			}
		},

		afterInit : function( editor )
		{
			var dataProcessor = editor.dataProcessor,
				dataFilter = dataProcessor && dataProcessor.dataFilter;

			if ( dataFilter )
			{
				dataFilter.addRules(
					{
						elements :
						{
							'cke:object' : function( element )
							{
								var attributes = element.attributes,
									classId = attributes.classid && String( attributes.classid ).toLowerCase();

								if ( !classId )
								{
									// Look for the inner <embed>
									for ( var i = 0 ; i < element.children.length ; i++ )
									{
										if ( element.children[ i ].name == 'cke:embed' )
										{
											if ( !isChamilodailymotionEmbed( element.children[ i ] ) )
												return null;

											return createFakeElement( editor, element );
										}
									}
									return null;
								}
								
								if ( isChamilodailymotionEmbed( element ) )
									return createFakeElement( editor, element );
							},

							'cke:embed' : function( element )
							{
								if (isChamilodailymotionEmbed( element ) )
									return createFakeElement( editor, element );
							}
						}
					},
					5);
			}
		},

		requires : [ 'fakeobjects' ]
	});
})();

CKEDITOR.tools.extend( CKEDITOR.config,
{
	/**
	 * Save as EMBED tag only. This tag is unrecommended.
	 * @type Boolean
	 * @default false
	 */
	chamilodailymotionEmbedTagOnly : false,

	/**
	 * Add EMBED tag as alternative: &lt;object&gt&lt;embed&gt&lt;/embed&gt&lt;/object&gt
	 * @type Boolean
	 * @default false
	 */
	chamilodailymotionAddEmbedTag : true,

	/**
	 * Use embedTagOnly and addEmbedTag values on edit.
	 * @type Boolean
	 * @default false
	 */
	chamilodailymotionConvertOnEdit : false
} );
