
;(function ($) {
	'use strict';

	var $window = $(window);

	function debounce( func, wait, immediate ) {
		var timeout;

		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if ( !immediate ) func.apply( context, args );
			};
			var callNow = immediate && !timeout;

			clearTimeout( timeout );

			timeout = setTimeout( later, wait );

			if ( callNow ) func.apply( context, args );
		};
	}

	$window.on( 'elementor/frontend/init', function() {
		var ModuleHandler = elementorModules.frontend.handlers.Base;

		var SliderBase = ModuleHandler.extend( {
			bindEvents: function() {
				this.removeArrows();
				this.run();
			},

			removeArrows: function() {
				var _this = this;

				this.elements.$container.on( 'init', function() {
					_this.elements.$container.siblings().hide();
				} );
			},

			getDefaultSettings: function() {
				return {
					autoplay    : true,
					arrows      : false,
					checkVisible: false,
					container   : '.wpzjs-slick',
					dots        : false,
					infinite    : true,
					rows        : 0,
					slidesToShow: 1,
					prevArrow   : $( '<div />' ).append( this.findElement( '.slick-prev' ).clone().show() ).html(),
					nextArrow   : $( '<div />' ).append( this.findElement( '.slick-next' ).clone().show() ).html(),
					selectors   : {
						video       : '.elementor-video',
						videoIframe : '.elementor-video-iframe',
						videoWrapper: '.wpz-video-wrapper'
					}
				}
			},

			getDefaultElements: function () {
				return {
					$container   : this.findElement( this.getSettings( 'container' ) ),
					$video       : this.$element.find( this.getSettings( 'selectors.video' ) ),
					$videoIframe : this.$element.find( this.getSettings( 'selectors.videoIframe' ) ),
					$videoWrapper: this.$element.find( this.getSettings( 'selectors.videoWrapper' ) )
				};
			},

			handleVideo: function () {
				if ( 'youtube' === this.getElementSettings( 'video_type' ) ) {
					this.apiProvider.onApiReady( ( apiObject ) => {
						this.prepareYTVideo( apiObject );
					} );
				} else {
					this.playVideo();
				}
			},

			playVideo: function () {
				if ( this.elements.$video.length ) {
					// this.youtubePlayer exists only for YouTube videos, and its play function is different.
					if ( this.youtubePlayer ) {
						this.youtubePlayer.playVideo();
					} else {
						this.elements.$video[ 0 ].play();
					}

					return;
				}

				const $videoIframe = this.elements.$videoIframe;
				const newSourceUrl = $videoIframe[ 0 ].src.replace( '&autoplay=0', '' );

				$videoIframe[ 0 ].src = newSourceUrl + '&autoplay=1';

				if ( $videoIframe[ 0 ].src.includes( 'vimeo.com' ) ) {
					const videoSrc = $videoIframe[ 0 ].src,
					      timeMatch = /#t=[^&]*/.exec( videoSrc );

					// Param '#t=' must be last in the URL
					$videoIframe[ 0 ].src = videoSrc.slice( 0, timeMatch.index ) + videoSrc.slice( timeMatch.index + timeMatch[ 0 ].length ) + timeMatch[ 0 ];
				}
			},

			prepareYTVideo: function( YT ) {
				const elementSettings = this.getElementSettings(),
				      playerOptions = {
						videoId: this.videoID,
						events: {
							onReady: () => {
								if ( elementSettings.mute ) {
									this.youtubePlayer.mute();
								}
		
								if ( elementSettings.autoplay ) {
									this.youtubePlayer.playVideo();
								}
							},
							onStateChange: ( event ) => {
								if ( event.data === YT.PlayerState.ENDED && elementSettings.video_loop ) {
									this.youtubePlayer.seekTo( 0 );
								}
							},
						},
						playerVars: {
							controls: elementSettings.video_controls ? 1 : 0,
							rel: elementSettings.video_rel ? 1 : 0,
							playsinline: elementSettings.play_on_mobile ? 1 : 0,
							modestbranding: elementSettings.video_modestbranding ? 1 : 0,
							autoplay: elementSettings.video_autoplay ? 1 : 0
						},
					};
		
				// To handle CORS issues, when the default host is changed, the origin parameter has to be set.
				if ( elementSettings.yt_privacy ) {
					playerOptions.host = 'https://www.youtube-nocookie.com';
					playerOptions.origin = window.location.hostname;
				}
		
				this.youtubePlayer = new YT.Player( this.elements.$video[ 0 ], playerOptions );
			},

			onInit: function () {
				ModuleHandler.prototype.onInit.apply( this, arguments );

				var self = this;

				this.elements.$videoWrapper.each( function() {
					var videoType = $( this ).data( 'video-type' );
					var videoUrl = $( this ).data( 'video-url' );

					if ( 'youtube' !== videoType || '' == videoUrl.trim() ) {
						// Currently the only API integration in the Video widget is for the YT API
						return;
					}

					this.apiProvider = elementorFrontend.utils.youtube;

					this.videoID = this.apiProvider.getVideoIDFromURL( videoUrl );

					if ( ! this.videoID ) {
						return;
					}

					// When Optimized asset loading is set to off, the video type is set to 'Youtube', and 'Privacy Mode' is set
					// to 'On', there might be a conflict with other videos that are loaded WITHOUT privacy mode, such as a
					// video background in a section. In these cases, to avoid the conflict, a timeout is added to postpone the
					// initialization of the Youtube API object.
					if ( ! elementorFrontend.config.experimentalFeatures[ 'e_optimized_assets_loading' ] ) {
						setTimeout( () => {
							this.apiProvider.onApiReady( ( apiObject ) => self.prepareYTVideo( apiObject ) );
						}, 0 );
					} else {
						this.apiProvider.onApiReady( ( apiObject ) => self.prepareYTVideo( apiObject ) );
					}
				} );
			},

			onElementChange: debounce( function() {
				this.elements.$container.slick( 'unslick' );
				this.run();
			}, 200 ),

			getSlickSettings: function() {
				var settings = {
					infinite: !! this.getElementSettings( 'loop' ),
					autoplay: !! this.getElementSettings( 'autoplay' ),
					autoplaySpeed: this.getElementSettings( 'autoplay_speed' ),
					speed: this.getElementSettings( 'animation_speed' ),
					centerMode: !! this.getElementSettings( 'center' ),
					vertical: !! this.getElementSettings( 'vertical' ),
					slidesToScroll: 1,
				};

				switch ( this.getElementSettings( 'navigation' ) ) {
					case 'arrow':
						settings.arrows = true;
						break;
					case 'dots':
						settings.dots = true;
						break;
					case 'both':
						settings.arrows = true;
						settings.dots = true;
						break;
				}

				settings.slidesToShow = parseInt( this.getElementSettings( 'slides_to_show' ) ) || 1;
				settings.responsive = [
					{
						breakpoint: elementorFrontend.config.breakpoints.lg,
						settings: {
							slidesToShow: ( parseInt( this.getElementSettings( 'slides_to_show_tablet' )) || settings.slidesToShow ),
						}
					},
					{
						breakpoint: elementorFrontend.config.breakpoints.md,
						settings: {
							slidesToShow: ( parseInt( this.getElementSettings( 'slides_to_show_mobile' )) || parseInt( this.getElementSettings( 'slides_to_show_tablet' ) ) ) || settings.slidesToShow,
						}
					}
				];

				return $.extend( {}, this.getSettings(), settings );
			},

			run: function() {
				this.elements.$container.slick( this.getSlickSettings() );
			}
		} );

		elementorFrontend.hooks.addAction(
			'frontend/element_ready/wpzoom-elementor-addons-slider.default',
			function ( $scope ) {
				elementorFrontend.elementsHandler.addHandler( SliderBase, {
					$element: $scope
				} );
			}
		);
	} );
} (jQuery));