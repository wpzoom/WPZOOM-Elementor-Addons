jQuery( function( $ ) {
	var frame;
	var videoFrame;

	$( '#wpz_category_cover_image_btnwrap' )
		.on( 'click', '#wpz_category_cover_image_selbtn', function( e ) {
			e.preventDefault();

			var $el = $( this );

			if ( frame ) {
				frame.open();
				return;
			}

			frame = wp.media( {
				title: WPZoomElementorAddons.selectImageLabel,
				library: {
					type: 'image'
				},
				multiple: false,
				button: {
					text: WPZoomElementorAddons.chooseImageLabel
				}
			} );

			frame.on( 'open', function() {
				if ( $.trim( $('#wpz_category_cover_image_id').val() ) == '' ) return;

				attachment = wp.media.attachment( parseInt( $( '#wpz_category_cover_image_id' ).val(), 10 ) );
				attachment.fetch();
				frame.state().get( 'selection' ).add( attachment ? [attachment] : [] );
			} );

			frame.on( 'select', function() {
				var imgdata = frame.state().get( 'selection' ).first().attributes;
				$( '#wpz_category_cover_image_id' ).val( parseInt( imgdata.id, 10 ) );
				$( '#wpz_category_cover_image_preview' ).css( 'background-image', 'url("' + imgdata.url + '")' );

				// Reset focal point to center when new image is selected
				$( '.wpz-focal-point-picker' ).css( { top: '50%', left: '50%' } );
				$( '#wpz_category_cover_image_pos' ).val( '50% 50%' );
				$( '#wpz_category_cover_image_preview' ).css( 'background-position', '50% 50%' );

				$( '#wpz_category_cover_image_preview:not(.has-image)' ).addClass( 'has-image' );
				$( '#wpz_category_cover_image_clrbtn.disabled' ).removeClass( 'disabled' );
			} );

			frame.open();
		} )
		.on( 'click', '#wpz_category_cover_image_clrbtn:not(.disabled)', function( e ) {
			e.preventDefault();

			if ( frame ) frame.state().get( 'selection' ).reset();

			$( '#wpz_category_cover_image_id' ).val( '' );
			$( '#wpz_category_cover_image_preview' ).attr( 'style', '' );
			$( '#wpz_category_cover_image_preview.has-image' ).removeClass( 'has-image' );
			$( this ).addClass( 'disabled' );

			// Reset focal point to center
			$( '.wpz-focal-point-picker' ).css( { top: '50%', left: '50%' } );
			$( '#wpz_category_cover_image_pos' ).val( '50% 50%' );
		} );

	// Focal Point Picker functionality
	$( '#wpz_category_cover_image_preview' ).on( 'click', function( e ) {
		// Only work if image is set
		if ( ! $( this ).hasClass( 'has-image' ) ) return;
		// Ignore clicks on buttons
		if ( $( e.target ).is( 'input' ) ) return;

		var $preview = $( this );
		var offset = $preview.offset();
		var x = e.pageX - offset.left;
		var y = e.pageY - offset.top;
		var width = $preview.outerWidth();
		var height = $preview.outerHeight();

		// Calculate percentage (0-100)
		var xPercent = Math.round( ( x / width ) * 100 );
		var yPercent = Math.round( ( y / height ) * 100 );

		// Clamp values
		xPercent = Math.max( 0, Math.min( 100, xPercent ) );
		yPercent = Math.max( 0, Math.min( 100, yPercent ) );

		// Update focal point indicator position
		$( '.wpz-focal-point-picker' ).css( {
			left: xPercent + '%',
			top: yPercent + '%'
		} );

		// Update hidden input
		$( '#wpz_category_cover_image_pos' ).val( xPercent + '% ' + yPercent + '%' );

		// Update background position to show the effect
		$preview.css( 'background-position', xPercent + '% ' + yPercent + '%' );
	} );

	// Initialize focal point position from saved value
	function initFocalPoint() {
		var posVal = $( '#wpz_category_cover_image_pos' ).val();
		if ( posVal && posVal.indexOf( '%' ) !== -1 ) {
			var parts = posVal.split( /\s+/ );
			if ( parts.length >= 2 ) {
				var xVal = parseFloat( parts[0] );
				var yVal = parseFloat( parts[1] );
				if ( ! isNaN( xVal ) && ! isNaN( yVal ) ) {
					$( '.wpz-focal-point-picker' ).css( {
						left: xVal + '%',
						top: yVal + '%'
					} );
				}
			}
		}
	}
	initFocalPoint();

	// Video Background functionality
	function wpzCatVideoTypeSwitch() {
		var selectedType = $( 'input[name="wpz_category_video_type"]:checked' ).val();
		$( '.wpz-cat-video-field' ).addClass( 'hidden' );
		if ( selectedType === 'self_hosted' ) {
			$( '.wpz-cat-video-mp4-field' ).removeClass( 'hidden' );
		} else if ( selectedType === 'external_hosted' ) {
			$( '.wpz-cat-video-youtube-field' ).removeClass( 'hidden' );
		} else if ( selectedType === 'vimeo_pro' ) {
			$( '.wpz-cat-video-vimeo-field' ).removeClass( 'hidden' );
		}
	}

	// Initialize on load
	wpzCatVideoTypeSwitch();

	// Switch on radio change
	$( 'input[name="wpz_category_video_type"]' ).on( 'change', wpzCatVideoTypeSwitch );

	// Video upload button
	$( '.wpz-cat-video-upload-btn' ).on( 'click', function( e ) {
		e.preventDefault();
		var $input = $( this ).siblings( 'input[type="text"]' );
		if ( videoFrame ) {
			videoFrame.open();
			return;
		}
		videoFrame = wp.media( {
			title: WPZoomElementorAddons.selectVideoLabel || 'Select Video',
			library: { type: 'video' },
			multiple: false,
			button: { text: WPZoomElementorAddons.chooseVideoLabel || 'Choose Video' }
		} );
		videoFrame.on( 'select', function() {
			var videodata = videoFrame.state().get( 'selection' ).first().attributes;
			$input.val( videodata.url );
		} );
		videoFrame.open();
	} );
} );
