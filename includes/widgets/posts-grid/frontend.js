!(function ($) {
	'use strict';

	function WPZoomPostsGrid($scope, $) {
		$scope.find('.wpz-grid').each(function () {
			var $this = $(this),
				$isoGrid = $this.children('.wpz-grid-container');

			// $this.imagesLoaded( function() {
			// 	$isoGrid.isotope({
			// 		itemSelector: '.wpz-post',
			// 		percentPosition: true,
			// 		masonry: {
			// 			columnWidth: '.wpz-posts-grid-sizer',
			// 		}
			// 	});
			// });

			var uid = $this.data('uid'),
				postsData = $this.data('posts-grid'),
				totalPosts = postsData.total,
				postsNum = postsData.posts_per_page,
				nonce    = $this.find( '#wpz_posts_grid_load_more' + uid ),
				loadMore = $this.find('.wpz-posts-grid-load-more'),
				btn = loadMore.children('.wpz-posts-grid-load-more-btn'),
				btnText = btn.html();

			btn.on('click', function(e) {
				e.preventDefault();
				var offset = $this.data('offset');
				btn.html( WPZoomElementorAddons.loadingString );
				$.post(
					WPZoomElementorAddons.ajaxURL,
					{
						action: 'wpz_posts_grid_load_more',
						posts_data: JSON.stringify( postsData ),
						offset: offset,
						nonce: nonce.val(),
					},
					function( data, status, code ) {
						if ( status == 'success' ) {
							var $items = $(data).find('.wpz-post');
							//console.log( data );
							$isoGrid.append($items);
							// imagesLoaded($isoGrid, function() {
							//     $isoGrid.isotope('appended', $items);
							// });

							if ( offset >= (totalPosts - postsNum) ) {
								loadMore.remove()
							}

							btn.html(btnText);
							$this.data('offset', offset + postsNum);

							//console.log( nonce );
						}
					}
				);
			});

		});
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/wpzoom-elementor-addons-posts-grid.default', WPZoomPostsGrid);
	});

})(jQuery);