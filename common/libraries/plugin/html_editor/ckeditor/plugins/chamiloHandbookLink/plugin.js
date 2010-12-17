CKEDITOR.plugins.add('chamiloHandbookLink',   
  {    
    init:function(editor) {
		var editorname = "chamiloHandbookLink";
		var c = editor.addCommand(editorname,new CKEDITOR.dialogCommand(editorname));
		c.modes={wysiwyg:1,source:0};
		c.canUndo=false;
		editor.ui.addButton("chamiloHandbookLink",{
			label:'add a link to another handbook topic',
			command:editorname,
			icon:this.path+"icon.gif"
		});
	CKEDITOR.dialog.add(editorname,this.path+"dialogs/handbook.js")}
});

