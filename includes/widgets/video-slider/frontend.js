jQuery(window).on('elementor/frontend/init', function () {
	'use strict';

	class VideoSliderHandler extends elementorModules.frontend.handlers.SwiperBase {
		
		getDefaultSettings() {
			return {
				selectors: {
					wrapper: '.wpz-video-slider-wrapper',
					slide: '.swiper-slide',
					lightboxTrigger: '.wpz-slide-lightbox-trigger',
					videoBackground: '.wpz-video-bg',
					slideInnerContents: '.swiper-slide-inner'
				},
				slidesPerView: {
					desktop: 1,
					tablet: 1,
					mobile: 1
				},
				classes: {
					animated: 'animated'
				}
			};
		}
		
		getDefaultElements() {
			const selectors = this.getSettings('selectors');
			const elements = {
				$swiperContainer: this.$element.find(selectors.wrapper)
			};
			elements.$slides = elements.$swiperContainer.find(selectors.slide);
			return elements;
		}

		getInitialSlide() {
			const editSettings = this.getEditSettings();
			return editSettings.activeItemIndex ? editSettings.activeItemIndex - 1 : 0;
		}

		getSwiperOptions() {
			const elementSettings = this.getElementSettings();
			
			const swiperOptions = {
				slidesPerView: parseInt(elementSettings.slides_to_show) || 1,
				spaceBetween: 0,
				speed: elementSettings.animation_speed || 300,
				loop: elementSettings.loop === 'yes',
				autoplay: elementSettings.autoplay === 'yes' ? {
					delay: elementSettings.autoplay_speed || 3000,
					disableOnInteraction: false
				} : false,
				grabCursor: true,
				initialSlide: this.getInitialSlide(),
				observeParents: true,
				observer: true,
				handleElementorBreakpoints: true,
				// Configure no-swiping for interactive elements
				noSwiping: true,
				noSwipingClass: 'swiper-no-swiping',
				noSwipingSelector: '.wpz-slide-lightbox-trigger, .wpz-slide-button, .wpz-slide-lightbox-wrapper, .wpz-slide-button-wrapper',
				on: {
					init: (swiper) => {
						console.log('Swiper initialized');
						setTimeout(() => {
							this.initVideoBackgrounds();
						}, 100);
					},
					slideChange: (swiper) => {
						console.log('Slide changed to:', swiper.activeIndex);
						setTimeout(() => {
							this.handleVideoBackgrounds();
						}, 100);
					},
					resize: (swiper) => {
						console.log('Swiper resized');
						setTimeout(() => {
							this.handleVideoBackgrounds();
						}, 100);
					}
				}
			};

			// Navigation
			const showArrows = elementSettings.navigation === 'arrow' || elementSettings.navigation === 'both';
			const showDots = elementSettings.navigation === 'dots' || elementSettings.navigation === 'both';

			if (showArrows) {
				swiperOptions.navigation = {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev'
				};
			}

			if (showDots) {
				swiperOptions.pagination = {
					el: '.swiper-pagination',
					clickable: true,
					type: 'bullets'
				};
			}

			// Responsive breakpoints
			if (elementSettings.slides_to_show_tablet || elementSettings.slides_to_show_mobile) {
				swiperOptions.breakpoints = {};
				
				if (elementSettings.slides_to_show_mobile) {
					swiperOptions.breakpoints[320] = {
						slidesPerView: parseInt(elementSettings.slides_to_show_mobile) || 1
					};
				}
				
				if (elementSettings.slides_to_show_tablet) {
					swiperOptions.breakpoints[768] = {
						slidesPerView: parseInt(elementSettings.slides_to_show_tablet) || 1
					};
				}
			}

			return swiperOptions;
		}

		async onInit() {
			super.onInit();
			
			if (this.elements.$slides.length <= 1) {
				return;
			}

			await this.initSwiper();
			this.initVideoLightbox();
			this.preventSliderDragOnInteractiveElements();
			
			// Initialize videos after Swiper is ready
			setTimeout(() => {
				this.initVideoBackgrounds();
			}, 100);
		}

		async initSwiper() {
			const Swiper = elementorFrontend.utils.swiper;
			this.swiper = await new Swiper(this.elements.$swiperContainer, this.getSwiperOptions());
			
			// Expose swiper instance
			this.elements.$swiperContainer.data('swiper', this.swiper);
		}

		onEditSettingsChange(propertyName) {
			if (this.elements.$slides.length <= 1) {
				return;
			}
			
			if ('activeItemIndex' === propertyName) {
				const activeIndex = this.getEditSettings('activeItemIndex') - 1;
				if (this.swiper) {
					if (this.swiper.params.loop) {
						this.swiper.slideToLoop(activeIndex);
					} else {
						this.swiper.slideTo(activeIndex);
					}
					// Stop autoplay when manually navigating in editor
					if (this.swiper.autoplay) {
						this.swiper.autoplay.stop();
					}
				}
			}
		}

		onElementChange(propertyName) {
			if (this.elements.$slides.length <= 1) {
				return;
			}

			// Handle settings changes that require swiper re-initialization
			const reinitSettings = ['slides_to_show', 'slides_to_show_tablet', 'slides_to_show_mobile', 'navigation', 'loop'];
			
			if (reinitSettings.includes(propertyName)) {
				this.swiper.destroy(true, true);
				this.initSwiper();
				return;
			}

			// Handle settings that can be updated without re-initialization
			if (propertyName === 'autoplay') {
				if (this.getElementSettings('autoplay') === 'yes') {
					this.swiper.autoplay.start();
				} else {
					this.swiper.autoplay.stop();
				}
			}

			if (propertyName === 'autoplay_speed') {
				this.swiper.params.autoplay.delay = this.getElementSettings('autoplay_speed') || 3000;
				this.swiper.autoplay.start();
			}

			if (propertyName === 'animation_speed') {
				this.swiper.params.speed = this.getElementSettings('animation_speed') || 300;
			}
		}

		initVideoBackgrounds() {
			const self = this;
			
			try {
				const jQuery = window.jQuery;
				
				// Initialize all video backgrounds with better error handling
				self.elements.$swiperContainer.find('.wpz-video-bg').each(function() {
					const videoContainer = jQuery(this);
					const videoType = videoContainer.data('video-type');
					
					if (!videoType) {
						console.log('No video type found for container:', videoContainer);
						return;
					}
					
					console.log('Initializing video background:', videoType, videoContainer);
					
					if (videoType === 'hosted') {
						self.handleHostedVideo(videoContainer);
					} else {
						self.handleIframeVideo(videoContainer);
					}
				});
				
				// Handle current slide specifically for better initialization
				if (self.swiper && self.swiper.slides && self.swiper.slides[self.swiper.activeIndex]) {
					setTimeout(() => {
						self.handleCurrentSlideVideos();
					}, 200);
				}
				
			} catch (error) {
				console.error('Error in initVideoBackgrounds:', error);
			}
		}

		handleVideoBackgrounds() {
			if (!this.swiper) return;
			
			try {
				this.handleCurrentSlideVideos();
			} catch (error) {
				console.error('Error in handleVideoBackgrounds:', error);
			}
		}

		handleCurrentSlideVideos() {
			if (!this.swiper || !this.swiper.slides) return;
			
			const jQuery = window.jQuery;
			const currentSlide = jQuery(this.swiper.slides[this.swiper.activeIndex]);
			const videoContainer = currentSlide.find('.wpz-video-bg');
			
			if (videoContainer.length) {
				const videoType = videoContainer.data('video-type');
				
				if (videoType === 'hosted') {
					this.handleHostedVideo(videoContainer);
				} else {
					this.handleIframeVideo(videoContainer);
				}
			}
		}

		handleHostedVideo(container) {
			const video = container.find('video')[0];
			if (!video) {
				console.log('No video element found in hosted video container');
				this.handleVideoError(container);
				return;
			}
			
			console.log('Handling hosted video:', video);
			
			const isMobile = window.innerWidth <= 768;
			const playOnMobile = container.data('play-on-mobile');
			
			if (isMobile && !playOnMobile) {
				video.style.display = 'none';
				console.log('Video hidden on mobile (play on mobile disabled)');
				this.handleVideoError(container);
				return;
			}
			
			// Add error handling
			video.addEventListener('error', () => {
				console.log('Video failed to load, showing fallback');
				this.handleVideoError(container);
			});
			
			// Add load handler
			video.addEventListener('loadeddata', () => {
				console.log('Video loaded successfully');
				this.handleVideoSuccess(container);
			});
			
			// Make sure video is visible
			video.style.display = 'block';
			video.style.visibility = 'visible';
			video.style.opacity = '1';
			
			// Ensure video covers the entire background
			this.resizeVideo(container, video);
			
			// Play video if paused
			if (video.paused) {
				video.play().catch((error) => {
					console.log('Video autoplay failed:', error);
					this.handleVideoError(container);
				});
			}
		}

		handleIframeVideo(container) {
			const iframe = container.find('iframe')[0];
			if (!iframe) {
				console.log('No iframe element found in iframe video container');
				this.handleVideoError(container);
				return;
			}
			
			console.log('Handling iframe video:', iframe);
			
			// Add error handling for iframe
			iframe.addEventListener('error', () => {
				console.log('Iframe video failed to load, showing fallback');
				this.handleVideoError(container);
			});
			
			// Add load handler
			iframe.addEventListener('load', () => {
				console.log('Iframe loaded successfully');
				this.handleVideoSuccess(container);
			});
			
			// Make sure iframe is visible
			iframe.style.display = 'block';
			iframe.style.visibility = 'visible';
			iframe.style.opacity = '1';
			
			// Ensure iframe covers the entire background
			this.resizeVideo(container, iframe);
			
			// Handle iframe load event for better sizing
			if (!iframe.dataset.loaded) {
				iframe.addEventListener('load', () => {
					iframe.dataset.loaded = 'true';
					console.log('Iframe loaded, resizing...');
					setTimeout(() => {
						this.resizeVideo(container, iframe);
					}, 100);
				});
			}
		}

		resizeVideo(container, video) {
			if (!container || !video) return;
			
			try {
				const containerElement = container[0] || container;
				const containerRect = containerElement.getBoundingClientRect();
				const containerWidth = containerRect.width;
				const containerHeight = containerRect.height;
				
				if (containerWidth === 0 || containerHeight === 0) {
					console.log('Container has zero dimensions, retrying...');
					setTimeout(() => {
						this.resizeVideo(container, video);
					}, 100);
					return;
				}
				
				console.log('Resizing video:', {
					containerWidth, 
					containerHeight, 
					videoElement: video
				});
				
				const containerRatio = containerWidth / containerHeight;
				const videoRatio = 16 / 9; // Assume 16:9 for most videos
				
				let videoWidth, videoHeight;
				
				if (containerRatio > videoRatio) {
					// Container is wider than video ratio
					videoWidth = containerWidth;
					videoHeight = containerWidth / videoRatio;
				} else {
					// Container is taller than video ratio
					videoHeight = containerHeight;
					videoWidth = containerHeight * videoRatio;
				}
				
				// Center the video
				const left = (containerWidth - videoWidth) / 2;
				const top = (containerHeight - videoHeight) / 2;
				
				// Apply styles
				video.style.position = 'absolute';
				video.style.width = videoWidth + 'px';
				video.style.height = videoHeight + 'px';
				video.style.left = left + 'px';
				video.style.top = top + 'px';
				video.style.objectFit = 'cover';
				video.style.zIndex = '1';
				
				console.log('Video resized:', {
					width: videoWidth,
					height: videoHeight,
					left,
					top
				});
				
			} catch (error) {
				console.error('Error resizing video:', error);
			}
		}

		initVideoLightbox() {
			const self = this;
			const jQuery = window.jQuery;
			
			// Remove any existing global lightbox handlers
			jQuery(document).off('click.wpzLightbox');
			
			// Use event delegation for robust handling
			jQuery(document).on('click.wpzLightbox', '.wpz-slide-lightbox-trigger', function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				const trigger = jQuery(this);
				const videoUrl = trigger.attr('href');
				
				if (!videoUrl) return;
				
				self.openVideoLightbox(videoUrl);
			});
		}

		openVideoLightbox(videoUrl) {
			const jQuery = window.jQuery;
			
			// Remove existing lightbox
			jQuery('.wpz-lightbox').remove();
			
			// Create lightbox with correct structure
			const lightbox = jQuery(`
				<div class="wpz-lightbox">
					<div class="wpz-lightbox-overlay"></div>
					<button class="wpz-lightbox-close" aria-label="Close video">
						<svg width="50" height="50" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</button>
					<div class="wpz-lightbox-container">
						<div class="wpz-lightbox-content">
							${this.getVideoEmbedHtml(videoUrl)}
						</div>
					</div>
				</div>
			`);
			
			// Add to page
			jQuery('body').append(lightbox);
			
			// Show lightbox with correct class name
			setTimeout(() => {
				lightbox.addClass('active');
			}, 10);
			
			// Close handlers
			lightbox.find('.wpz-lightbox-close, .wpz-lightbox-overlay').on('click', () => {
				this.closeLightbox(lightbox);
			});
			
			// ESC key handler
			jQuery(document).on('keydown.wpzLightbox', (e) => {
				if (e.keyCode === 27) {
					this.closeLightbox(lightbox);
				}
			});
		}

		closeLightbox(lightbox) {
			const jQuery = window.jQuery;
			
			lightbox.removeClass('active');
			jQuery(document).off('keydown.wpzLightbox');
			
			setTimeout(() => {
				lightbox.remove();
			}, 300);
		}

		getVideoEmbedHtml(url) {
			// YouTube
			if (url.includes('youtube.com') || url.includes('youtu.be')) {
				const videoId = this.getYouTubeVideoId(url);
				if (videoId) {
					return `<iframe class="wpz-lightbox-video" src="https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0" frameborder="0" allowfullscreen allow="autoplay"></iframe>`;
				}
			}
			
			// Vimeo
			if (url.includes('vimeo.com')) {
				const videoId = this.getVimeoVideoId(url);
				if (videoId) {
					return `<iframe class="wpz-lightbox-video" src="https://player.vimeo.com/video/${videoId}?autoplay=1" frameborder="0" allowfullscreen allow="autoplay"></iframe>`;
				}
			}
			
			// Fallback
			return `<iframe class="wpz-lightbox-video" src="${url}" frameborder="0" allowfullscreen></iframe>`;
		}

		getYouTubeVideoId(url) {
			const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
			const match = url.match(regExp);
			return (match && match[2].length === 11) ? match[2] : null;
		}

		getVimeoVideoId(url) {
			const regExp = /(?:vimeo\.com\/)([0-9]+)/;
			const match = url.match(regExp);
			return match ? match[1] : null;
		}

		handleVideoError(container) {
			const jQuery = window.jQuery;
			const slideItem = container.closest('.wpz-slide-item');
			
			// Add error class to show fallback image
			slideItem.addClass('video-failed');
			console.log('Video failed, fallback image should now be visible');
		}

		handleVideoSuccess(container) {
			const jQuery = window.jQuery;
			const slideItem = container.closest('.wpz-slide-item');
			
			// Remove error class to hide fallback image
			slideItem.removeClass('video-failed');
			console.log('Video loaded successfully, fallback image hidden');
		}

		preventSliderDragOnInteractiveElements() {
			const self = this;
			const jQuery = window.jQuery;
			
			// Prevent slider dragging when interacting with interactive elements
			const interactiveElements = '.wpz-slide-lightbox-trigger, .wpz-slide-button, .wpz-slide-lightbox-wrapper, .wpz-slide-button-wrapper';
			
			// More aggressive prevention for interactive elements
			this.elements.$swiperContainer.find(interactiveElements).each(function() {
				const element = jQuery(this);
				
				// Prevent all touch events from bubbling up
				element.on('touchstart.preventDrag', function(e) {
					console.log('Preventing drag on interactive element:', this.className);
					e.stopPropagation();
					e.stopImmediatePropagation();
					
					// Mark the slide as non-swipeable
					const slide = jQuery(this).closest('.swiper-slide');
					slide.addClass('swiper-no-swiping');
					
					// Remove class after a delay
					setTimeout(() => {
						slide.removeClass('swiper-no-swiping');
					}, 500);
				});
				
				element.on('touchmove.preventDrag', function(e) {
					e.stopPropagation();
					e.stopImmediatePropagation();
				});
				
				element.on('touchend.preventDrag', function(e) {
					e.stopPropagation();
					e.stopImmediatePropagation();
				});
				
				// Also prevent mouse events for desktop
				element.on('mousedown.preventDrag', function(e) {
					e.stopPropagation();
					e.stopImmediatePropagation();
				});
				
				element.on('mousemove.preventDrag', function(e) {
					e.stopPropagation();
					e.stopImmediatePropagation();
				});
				
				element.on('mouseup.preventDrag', function(e) {
					e.stopPropagation();
					e.stopImmediatePropagation();
				});
			});
			
			// Additional Swiper configuration to respect no-swiping class
			if (this.swiper && this.swiper.params) {
				this.swiper.params.noSwiping = true;
				this.swiper.params.noSwipingClass = 'swiper-no-swiping';
				this.swiper.params.noSwipingSelector = interactiveElements;
				
				// Update Swiper with new params
				this.swiper.update();
			}
		}
	}

	// Register the handler
	elementorFrontend.hooks.addAction('frontend/element_ready/wpzoom-elementor-addons-video-slider.default', function($scope) {
		new VideoSliderHandler({ $element: $scope });
	});
});