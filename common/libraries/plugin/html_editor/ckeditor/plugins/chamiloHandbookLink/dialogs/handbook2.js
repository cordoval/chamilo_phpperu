/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function()
{

    var test = 'testtexttttttttttttt';
var mySelection = 'test';

function dialogShow()
{
    var editor = this.getParentEditor();
    var sel = editor.getSelection();
    if (CKEDITOR.env.ie) {
                mySelection.unlock(true);
                selectedText = mySelection.getNative().createRange().text;
            } else {
                selectedText = mySelection.getNative();
            }



    this.mySelection = 'koekoek' ;

}


CKEDITOR.dialog.add('chamiloHandbookLink',function(a){
       

   
     var content = {
                        type : 'vbox',
                        padding : 0,
                        children :
                        [
                            {
                                type:'html',
                                html:'<div> linqsdfsqfsqfqsqdfqsd handboek-topic </div>'
                            },
                            {
                                type : 'hbox',
                                padding : 0,
                                children :
                                [
                                   
                                    {
                                        id : 'uid',
                                        type : 'text',
                                        label : a.lang.chamiloHandbookLink.UniqueIdentifierLabel,
                                        required : true
                                    },
                                    {
                                        type:'button',
                                        hidden: true,
                                        id: 'browse',
                                        filebrowser:
                                            {
                                                action: 'Browse',
                                                target: 'tab1:uid',
                                                params:
                                                    {
                                                        parameter1: mySelection
                                                    }
                                            },
                                        label : a.lang.chamiloHandbookLink.filebrowserLabel
                                        
                                    },
                                    {
                                        type:'button',
                                        id: 'tweedeknop',

                                       label : 'tweede knop',
                                       onClick: function()
                                       {
                                           mySelection = 'test2';
                                       }



                                    }
                                    
                                ]
                            }
                        ]


                };
    return{
        title: a.lang.chamiloHandbookLink.title,
        minWidth:390,
        minHeight:230,
       
        contents:[{
            id:'tab1',
            label:'',
            title:'',
            expand:true,
            padding:0,
            elements:[content]
            }],
        buttons:[CKEDITOR.dialog.okButton]
        };

});
})();
