<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<script type="text/template" id="tmpl-wpzoom-elementor-templates-modal__header">
	<div class="elementor-templates-modal__header">
		<div class="elementor-templates-modal__header__logo-area">
			<div class="elementor-templates-modal__header__logo">
				<span class="elementor-templates-modal__header__logo__icon-wrapper e-logo-wrapper">
					<i class="eicon-elementor"></i>
				</span>
				<span class="elementor-templates-modal__header__logo__title">WPZOOM Library</span>
			</div>
			<div id="elementor-template-library-header-preview-back" class="wpzoom-header-back-button" style="display:none;">
				<i class="eicon-" aria-hidden="true"></i>
				<span><?php echo __( 'Back to Library', 'wpzoom-elementor-addons' ); ?></span>
			</div>
		</div>
		<div class="elementor-templates-modal__header__items-area">
			<div class="elementor-templates-modal__header__close elementor-templates-modal__header__close--normal elementor-templates-modal__header__item">
				<i class="eicon-close" aria-hidden="true" title="Close"></i>
				<span class="elementor-screen-only">Close</span>
			</div>
			<div id="wpzoom-elementor-template-library-header-preview" style="display:none;">
				<div id="elementor-template-library-header-preview-insert-wrapper" class="elementor-templates-modal__header__item">
					<a class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button" data-template-name="">
						<i class="eicon-file-download" aria-hidden="true"></i>
						<span class="elementor-button-title">Insert</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</script>
<script type="text/template" id="tmpl-wpzoom-elementor-template-library-loading">
	<div id="elementor-template-library-loading">
		<div class="elementor-loader-wrapper">
			<div class="elementor-loader">
				<div class="elementor-loader-boxes">
					<div class="elementor-loader-box"></div>
					<div class="elementor-loader-box"></div>
					<div class="elementor-loader-box"></div>
					<div class="elementor-loader-box"></div>
				</div>
			</div>
			<div class="elementor-loading-title"><?php echo __( 'Loading', 'wpzoom-elementor-addons' ); ?></div>
		</div>
	</div>
</script>
<script type="text/template" id="tmpl-wpzoom-elementor-template-library-tools">
	<div id="wpzoom-elementor-template-library-toolbar">
		<div id="elementor-template-library-filter-toolbar-remote" class="elementor-template-library-filter-toolbar">				
			<div id="elementor-template-library-filter">
				<select id="wpzoom-elementor-template-library-filter-theme" class="elementor-template-library-filter-select" name="theme" data-filter="theme">
					<option value = ''>Select a theme</option>
					<option value ='cookbook'>CookBook</option>
                    <option value ='foodica'>Foodica (PRO)</option>
                    <option value ='inspiro-pro'>Inspiro PRO</option>
                    <option value ='inspiro-classic'>Inspiro Classic</option>
                    <option value ='inspiro-lite'>Inspiro Lite</option>
				</select>
			</div>
		</div>
		<!-- <div id="elementor-template-library-filter-text-wrapper">
			<label for="elementor-template-library-filter-text" class="elementor-screen-only">Search Templates:</label>
			<input id="wpzoom-elementor-template-library-filter-text" placeholder="Search">
			<div class='wpzoom__search'><i class="eicon-search"></i></div>
		</div> -->
	</div>
</script>