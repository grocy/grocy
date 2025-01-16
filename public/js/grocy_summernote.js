$("textarea.wysiwyg-editor").summernote({
	minHeight: "300px",
	lang: __t("summernote_locale"),
	callbacks: {
		onImageLinkInsert: function(url)
		{
			// Summernote workaround: Make images responsive
			// By adding the "img-fluid" class to the img tag
			$img = $('<img>').attr({ src: url, class: "img-fluid", loading: "lazy" })
			$(this).summernote("insertNode", $img[0]);
		}
	},
	toolbar: [
		['fontsize', ['fontsize']],
		['font', ['bold', 'underline', 'clear']],
		['color', ['color']],
		['para', ['ul', 'ol', 'paragraph']],
		['table', ['table']],
		['insert', ['link', 'picture', 'video']],
		['view', ['codeview', 'fullscreen']]
	]
});

// Summernote workaround: Make embeds responsive
// By wrapping any embeded video in a container with class "embed-responsive"
$(".note-video-clip").each(function()
{
	$(this).parent().html('<div class="embed-responsive embed-responsive-16by9">' + $(this).wrap("<p/>").parent().html() + "</div>");
});
