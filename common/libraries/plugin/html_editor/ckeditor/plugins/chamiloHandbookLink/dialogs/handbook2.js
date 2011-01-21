/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function()
{


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

}


CKEDITOR.dialog.add('chamiloHandbookLink',function(a){

    var getData = new Array();
    var getVariables = window.location.search;
    if(getVariables)
        {
            getVariables = getVariables.substr(1);
            var pairs = getVariables.split("&");

            for(var i = 0; i < pairs.length; i++)
                {
                    var nvArray = pairs[i].split("=");
                    var name = nvArray[0];
                    var value = nvArray[1];

                    getData[name]= value;
                }
        }
        var application = getData['application'];
        var pid;
        if(application == 'handbook')
            {
                pid = getData['hpid'];
            }
            else
                {
                    pid = 0;
                }


  

   
     var content = {
                        type : 'vbox',
                        padding : 0,
                        children :
                        [
                            {
                                type:'html',
                                html:'<div> qsdqs handboek-topic' + ' test '+ application + '</div>'
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

                                                        parent_application: application,
                                                        pub: pid
                                                    }
                                            },
                                        label : a.lang.chamiloHandbookLink.filebrowserLabel
                                        
                                    }
                                    
                                ]
                            }
                        ]


                };
    return{
        title: a.lang.chamiloHandbookLink.title,
        minWidth:390,
        minHeight:230,
        onOk: function() {
            var mySelection = this.getParentEditor().getSelection();

            if (CKEDITOR.env.ie) {
                mySelection.unlock(true);
                selectedText = mySelection.getNative().createRange().text;
            } else {
                selectedText = mySelection.getNative();
            }

            var selected_uid = this.getContentElement('tab1','uid')

	    this.getParentEditor().insertHtml('<a href="' + web_path + '/run.php?go=handbook_light_viewer&amp;application=handbook&amp;uid='+ selected_uid.getValue() +'&blabla=blabla'  +'">'+selectedText+'</a>');
        },
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
