<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Boilerplate
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}

if ( is_single() ) {
	$pageurl = get_permalink();
	?>
	<aside id="secondary" class="widget-area">
		<div class="col4">
			<div class="lr-margin">
				<div class="post-date generaltitle">
					<h1>
					<?php
					$post_date = get_the_date( 'jS M Y' );
					echo $post_date;
					?>
					</h1>
				</div>
			</div>
		</div>
		<div class="col4">
			<div class="lr-margin">
				<h4>Tags</h4>
				<?php
					$post_tags = get_the_tags();
				if ( $post_tags ) {
					echo '<div class="tags">';
					foreach ( $post_tags as $tag ) {
						echo '<a href="' . get_tag_link( $tag->term_id ) . '" class="single-tag">' . $tag->name . '</a>';
					}
					echo '</div>';
				}
				?>
			</div>
		</div>
		<div class="col4">
			<div class="lr-margin">
				<div class="related">
					<h4>Related articles</h4>
					<?php
					$posts = get_field( 'related_articles' );
					if ( $posts ) :
						?>
					<ul>
						<?php foreach ( $posts as $post ) : // variable must be called $post (IMPORTANT). ?>
							<?php setup_postdata( $post ); ?>
						<li>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</li>
						<?php endforeach; ?>
					</ul>
						<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly. ?>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="col4">
			<div class="lr-margin">
				<div class="sharing">
					<h4>Share this page</h4>
					<a href="http://twitter.com/intent/tweet?status=<?php echo get_the_title(); ?>+<?php echo $pageurl; ?>" class="social tw transition" target="_blank">Twitter</a>
					<a href="http://www.facebook.com/share.php?u=<?php echo $pageurl; ?>&title=<?php echo get_the_title(); ?>" class="social fb transition" target="_blank">Facebook</a>
					<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $pageurl; ?>&title=<?php echo get_the_title(); ?>&summary=&source=" class="social ln transition" target="_blank">LinkedIn</a>
					<a href="mailto:?subject=Check out - <?php echo get_the_title(); ?>&body=Find%20out%20more%20here%3A%20<?php echo $pageurl; ?>" class="social em transition" target="_self">Email</a>
				</div>
			</div>
		</div>
	</aside><!-- #secondary -->
<?php } ?>
