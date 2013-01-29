/*
  Custom CKEditor settings for GCMS
*/

CKEDITOR.editorConfig = function(config) {
    config.docType = '<!DOCTYPE html>';
    config.entities = false;
    config.height = 400;
    config.contentsCss = [gcms.options.baseUrl + '/themes/' + gcms.options.theme + '/css/bootstrap.min.css'];
    config.toolbar = 
    [
        { name: 'document', items : [ 'Source','-','Preview','Print','-','Templates' ] },
        { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
        { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
        { name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
        '/',
        { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
        { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
        { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
        { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','Iframe' ] },
        '/',
        { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
        { name: 'colors', items : [ 'TextColor','BGColor' ] },
        { name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
    ];
    config.filebrowserImageUploadUrl = gcms.options.baseUrl + '/admin/upload/image';
};

CKEDITOR.on('instanceReady', function(ev) {
    ev.editor.dataProcessor.writer.indentationChars = '';
});

