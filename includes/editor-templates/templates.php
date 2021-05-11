<?php
//namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<script type="text/template" id="tmpl-wpzoom-elementor-template-library-header-actions">
	<div id="wpzoom-elementor-template-library-header-import" class="elementor-templates-modal__header__item">
		<i class="eicon-upload-circle-o" aria-hidden="true" title="<?php esc_attr_e( 'Import Template', 'elementor' ); ?>"></i>
		<span class="elementor-screen-only"><?php echo __( 'Import Template', 'elementor' ); ?></span>
	</div>
	<div id="wpzoom-elementor-template-library-header-sync" class="elementor-templates-modal__header__item">
		<i class="eicon-sync" aria-hidden="true" title="<?php esc_attr_e( 'Sync Library', 'elementor' ); ?>"></i>
		<span class="elementor-screen-only"><?php echo __( 'Sync Library', 'elementor' ); ?></span>
	</div>
	<div id="wpzoom-elementor-template-library-header-save" class="elementor-templates-modal__header__item">
		<i class="eicon-save-o" aria-hidden="true" title="<?php esc_attr_e( 'Save', 'elementor' ); ?>"></i>
		<span class="elementor-screen-only"><?php echo __( 'Save', 'elementor' ); ?></span>
	</div>
</script>

<script type="text/template" id="tmpl-wpzoom--elementor-template-library-header-menu">
	<# jQuery.each( tabs, ( tab, args ) => { #>
		<div class="elementor-component-tab elementor-template-library-menu-item" data-tab="{{{ tab }}}">{{{ args.title }}}</div>
	<# } ); #>
</script>

<script type="text/template" id="tmpl-wpzoom-elementor-template-library-header-preview">
	<div id="wpzoom-elementor-template-library-header-preview-insert-wrapper" class="elementor-templates-modal__header__item">
		{{{ elementor.templates.layout.getTemplateActionButton( obj ) }}}
	</div>
</script>

<script type="text/template" id="tmpl-wpzoom-elementor-template-library-header-back">
	<i class="eicon-" aria-hidden="true"></i>
	<span><?php echo __( 'Back to Library', 'elementor' ); ?></span>
</script>

<script type="text/template" id="tmpl-wpzoom-elementor-template-library-loading">
	<div class="elementor-loader-wrapper">
		<div class="elementor-loader">
			<div class="elementor-loader-boxes">
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
			</div>
		</div>
		<div class="elementor-loading-title"><?php echo __( 'Loading', 'elementor' ); ?></div>
	</div>
</script>

<script type="text/template" id="tmpl-wpzoom-elementor-template-library-templates">
	<#
		var activeSource = elementor.templates.getFilter('source');
	#>
	<div id="wpzoom-elementor-template-library-toolbar">
		<# if ( 'remote' === activeSource ) {
			var activeType = elementor.templates.getFilter('type');
			#>
			<div id="wpzoom-elementor-template-library-filter-toolbar-remote" class="elementor-template-library-filter-toolbar">
				<# if ( 'page' === activeType ) { #>
					<div id="wpzoom-elementor-template-library-order">
						<input type="radio" id="elementor-template-library-order-new" class="elementor-template-library-order-input" name="elementor-template-library-order" value="date">
						<label for="elementor-template-library-order-new" class="elementor-template-library-order-label"><?php echo __( 'New', 'elementor' ); ?></label>
						<input type="radio" id="elementor-template-library-order-trend" class="elementor-template-library-order-input" name="elementor-template-library-order" value="trendIndex">
						<label for="elementor-template-library-order-trend" class="elementor-template-library-order-label"><?php echo __( 'Trend', 'elementor' ); ?></label>
						<input type="radio" id="elementor-template-library-order-popular" class="elementor-template-library-order-input" name="elementor-template-library-order" value="popularityIndex">
						<label for="elementor-template-library-order-popular" class="elementor-template-library-order-label"><?php echo __( 'Popular', 'elementor' ); ?></label>
					</div>
				<# } else {
					var config = elementor.templates.getConfig( activeType );
					if ( config.categories ) { #>
						<div id="wpzoom-elementor-template-library-filter">
							<select id="wpzoom-elementor-template-library-filter-subtype" class="elementor-template-library-filter-select" data-elementor-filter="subtype">
								<option></option>
								<# config.categories.forEach( function( category ) {
									var selected = category === elementor.templates.getFilter( 'subtype' ) ? ' selected' : '';
									#>
									<option value="{{ category }}"{{{ selected }}}>{{{ category }}}</option>
								<# } ); #>
							</select>
						</div>
					<# }
				} #>
				<div id="wpzoom-elementor-template-library-my-favorites">
					<# var checked = elementor.templates.getFilter( 'favorite' ) ? ' checked' : ''; #>
					<input id="wpzoom-elementor-template-library-filter-my-favorites" type="checkbox"{{{ checked }}}>
					<label id="wpzoom-elementor-template-library-filter-my-favorites-label" for="wpzoom-elementor-template-library-filter-my-favorites">
						<i class="eicon" aria-hidden="true"></i>
						<?php echo __( 'My Favorites', 'elementor' ); ?>
					</label>
				</div>
			</div>
		<# } else { #>
			<div id="wpzoom-elementor-template-library-filter-toolbar-local" class="elementor-template-library-filter-toolbar"></div>
		<# } #>
		<div id="wpzoom-elementor-template-library-filter-text-wrapper">
			<label for="wpzoom-elementor-template-library-filter-text" class="elementor-screen-only"><?php echo __( 'Search Templates:', 'elementor' ); ?></label>
			<input id="wpzoom-elementor-template-library-filter-text" placeholder="<?php echo esc_attr__( 'Search', 'elementor' ); ?>">
			<i class="eicon-search"></i>
		</div>
	</div>
	<# if ( 'local' === activeSource ) { #>
		<div id="wpzoom-elementor-template-library-order-toolbar-local">
			<div class="elementor-template-library-local-column-1">
				<input type="radio" id="elementor-template-library-order-local-title" class="elementor-template-library-order-input" name="elementor-template-library-order-local" value="title" data-default-ordering-direction="asc">
				<label for="wpzoom-elementor-template-library-order-local-title" class="elementor-template-library-order-label"><?php echo __( 'Name', 'elementor' ); ?></label>
			</div>
			<div class="elementor-template-library-local-column-2">
				<input type="radio" id="wpzoom-elementor-template-library-order-local-type" class="elementor-template-library-order-input" name="elementor-template-library-order-local" value="type" data-default-ordering-direction="asc">
				<label for="wpzoom-elementor-template-library-order-local-type" class="elementor-template-library-order-label"><?php echo __( 'Type', 'elementor' ); ?></label>
			</div>
			<div class="elementor-template-library-local-column-3">
				<input type="radio" id="wpzoom-elementor-template-library-order-local-author" class="elementor-template-library-order-input" name="elementor-template-library-order-local" value="author" data-default-ordering-direction="asc">
				<label for="wpzoom-elementor-template-library-order-local-author" class="elementor-template-library-order-label"><?php echo __( 'Created By', 'elementor' ); ?></label>
			</div>
			<div class="elementor-template-library-local-column-4">
				<input type="radio" id="wpzoom-elementor-template-library-order-local-date" class="elementor-template-library-order-input" name="elementor-template-library-order-local" value="date">
				<label for="wpzoom-elementor-template-library-order-local-date" class="elementor-template-library-order-label"><?php echo __( 'Creation Date', 'elementor' ); ?></label>
			</div>
			<div class="elementor-template-library-local-column-5">
				<div class="elementor-template-library-order-label"><?php echo __( 'Actions', 'elementor' ); ?></div>
			</div>
		</div>
	<# } #>
	<div id="wpzoom-elementor-template-library-templates-container"></div>
	<# if ( 'remote' === activeSource ) { #>
		<div id="wpzoom-elementor-template-library-footer-banner">
			<img class="elementor-nerd-box-icon" src="<?php echo ELEMENTOR_ASSETS_URL . 'images/information.svg'; ?>" />
			<div class="elementor-excerpt"><?php echo __( 'Stay tuned! More awesome templates coming real soon.', 'elementor' ); ?></div>
		</div>
	<# } #>
</script>