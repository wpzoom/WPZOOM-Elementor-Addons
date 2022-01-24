jQuery( function( $ ) {
	var frame;

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

				var maxWidth = $( '#wpz_category_cover_image_preview' ).width();
				var width = parseInt( imgdata.width, 10 );
				$( '#wpz_category_cover_image_preview' ).css( 'height', ( parseInt( imgdata.height, 10 ) * ( maxWidth / width ) ) );

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
		} );
} );
