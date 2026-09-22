<?php
/**
 * Layout partial: sidebar.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// imports.
	$sidebar_type   = $args['sidebar_type'] ?? null;
	$enable_sidebar = $args['enable_sidebar'] ?? null;

	// groups.
	$sidebar_options = get_field( 'sidebar_options', $sidebar_type );

	// singles.
	$sticky_menu_title = $sidebar_options['sticky_menu_title'] ?? null;
	$wysiwyg           = $sidebar_options['wysiwyg'] ?? null;
	$button_group      = $sidebar_options['button_group'] ?? null;

?>
<div class="sticky-mob">
	<h4><?php echo $sticky_menu_title; ?></h4>
	<h4 class="filtered"></h4>
</div>
<div class="site-sidebar">
	<div class="overlay"></div>
	<div class="sticky">
		<div class="row">
			<div class="col-12">
				<div class="content">
					<?php
						echo $wysiwyg;
						get_template_part(
							'template-parts/components/component',
							'button-group',
							array(
								'button_group' => $button_group,
							)
						);
						?>
				</div>
			</div>
		</div>
	</div>
	<!-- Add class to body to identify if a sidebar is in use -->
	<?php if ( 'yes' === $enable_sidebar ) { ?>
		<script>
			const sidebar = document.querySelectorAll('body');
			sidebar[0].classList.add('has-sidebar');
		</script>
	<?php } ?>
</div>
