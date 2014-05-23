/*
  Custom CKEditor settings for GCMS
*/
CKEDITOR.editorConfig = function(config) {
    config.disableNativeSpellChecker = false;
    config.entities = false;
    config.height = 400;
    config.dataIndentationChars = '  ';
    config.contentsCss = [gcms.options.baseUrl + '/themes/' + gcms.options.theme + '/css/bootstrap.min.css'];
    config.removePlugins = 'save,newpage,flash,forms';
    config.filebrowserImageUploadUrl = gcms.options.baseUrl + '/admin/upload/image';
};
