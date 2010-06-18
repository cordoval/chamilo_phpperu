/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function()
{


	var latexDialog = function( editor, dialogType )
	{
		var onImgLoadEvent = function()
		{
			// Image is ready.
			var original = this.originalElement;
			original.setCustomData( 'isReady', 'true' );
			original.removeListener( 'load', onImgLoadEvent );
			

			// Hide loader
			CKEDITOR.document.getById( 'ImagePreviewLoader' ).setStyle( 'display', 'none' );

			

			
		
		};

		
		return {
			title : ( dialogType == 'image' ) ? editor.lang.image.title : editor.lang.image.titleButton,
			minWidth : 420,
			minHeight : 310,
			onShow : function()
			{
				
				// Preview
				this.preview = CKEDITOR.document.getById( 'previewImage' );
				window.preImageSrc = "";
				var editor = this.getParentEditor(),
					sel = this.getParentEditor().getSelection(),
					element = sel.getSelectedElement();
					if(element != null)
					{
						var imgSrc = element.$.src;
						if(imgSrc != null && imgSrc != "")
						{
							// tao bien preImageSrc se duoc dung trong iframe;
							window.preImageSrc = imgSrc;
							// goi ham set input value ban dau cho iframe khi nguoi dung muon sua lai cong thuc cu
							//window.frames['iframelatex'].getPreImageCode(); 
						}
					}
					
				// Copy of the image
				
			},
			onOk : function(obj)
			{
				
				
				var frame = null;
				for(var i=0; i<window.frames.length; i++)// sao vong lap thi duoc, ma viet thang bang window.frames['iframelatex'] loi??
				{
					
					if(window.frames[i].name == "iframelatex")
					{
						frame = window.frames[i];
						var doc = frame.document;
						var  content = doc.getElementById("equationview");
								 
						if(content)
						{
							//var tmpEditor = obj.sender._.editor;
							var tempImage = editor.document.createElement('img');
							
							tempImage.setAttribute ( 'src' ,content.src );
							obj.sender._.editor.insertElement(tempImage);
						}
						break;
					}
				}
				
				
				
			},
			onLoad : function()
			{

				//var doc = this._.element.getDocument();


				//this.commitContent = commitContent;
			

			},
			onHide : function()
			{
			
			},
			contents : [
				{
					id : 'info',
					label : editor.lang.image.infoTab,
					accessKey : 'I',
					elements :
					[
						
						{
							type : 'hbox',
							widths : [ '140px', '240px' ],
							children :
							[
								
								{
									type : 'vbox',
									height : '250px',
									children :
									[
										{
											type : 'html',
											style : 'width:95%;',
											html :'<div style="width:600px; height:400px"><iframe src="' + editor.config.latexDialogUrl + '" frameborder="0" name="iframelatex" name="iframelatex" id="iframelatex" allowtransparency="1" style="width:100%;height:400px;margin:0;padding:0;"></iframe></div>'
										}
									]
								}
							]
						}
					]
				}

			]
		};
	};

	CKEDITOR.dialog.add( 'latex', function( editor )
		{
			return latexDialog( editor, 'latex' );
		});

	CKEDITOR.dialog.add( 'imagebutton', function( editor )
		{
			return latexDialog( editor, 'imagebutton' );
		});
})();
