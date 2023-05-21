$("textarea.wysiwyg-editor").summernote({
	minHeight: "300px",
	lang: __t("summernote_locale"),
	callbacks: {
		onImageLinkInsert: function(url)
		{
			// Summernote workaround: Make images responsive
			// By adding the "img-fluid" class to the img tag
			$img = $('<img>').attr({ src: url, class: "img-fluid" })
			$(this).summernote("insertNode", $img[0]);
		}
	}
});
