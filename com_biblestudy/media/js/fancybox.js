/**
 * Created with JetBrains PhpStorm.
 * User: bcordis
 * Date: 9/14/13
 * Time: 11:35 AM
 * To change this template use File | Settings | File Templates.
 */
;
(function (window, document, $) {

	$(document).ready(function () {
		$(".fancybox").fancybox();
	});
	$(".fancybox")
		.attr('rel', 'gallery')
		.fancybox({
			openEffect: 'none',
			closeEffect: 'none',
			nextEffect: 'none',
			prevEffect: 'none',
			padding: 0,
			margin: [20, 60, 20, 60] // Increase left/right margin
		});
	$(document).ready(function () {
		$('.fancybox-media').fancybox({
			openEffect: 'none',
			closeEffect: 'none',
			helpers: {
				media: {}
			}
		});
	});

	$(document).ready(function () {
		$(".fancybox_jwplayer").on("click", function () {
			var tarGet;
			var myVideo = this.href;
			var contentPanelId = $(this).attr("id");
			var id = $("#" + contentPanelId);
			var player = $(".fancybox_jwplayer");
			var height = id.attr('pheight');
			var width = id.attr('pwidth');
			var autostart = id.attr('autostart');
			$.fancybox({
				fitToView: false,
				width: width,
				height: height,
				autoSize: false,
				closeClick: false,
				openEffect: 'none',
				closeEffect: 'none',
				content: '<div id="video_container">Loading the player ... </div> ',
				afterShow: function () {
					jwplayer("video_container").setup({
						//flashplayer: "media/com_biblestudy/player/jwplayer.flash.swf",
						file: myVideo,
						width: width,
						height: height,
						autostart: autostart,
						controls: 'true'
					}); // jwplayer setup
				} // afterShow
			}); // fancybox
			return false; // prevents default
		}); // on
	}); // ready
}(window, document, jQuery));
