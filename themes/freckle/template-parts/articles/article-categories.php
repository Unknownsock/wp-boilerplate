<?php
/**
 * Article partial: renders the sectors list.
 *
 * @package Boilerplate
 */

	$posts = get_posts(
		array(
			'post_type'      => 'sectors',
			'order'          => 'ASC',
			'posts_per_page' => 6,
			'post__not_in'   => array( get_the_ID() ),
		)
	);
	if ( $posts ) {
		echo '<ul>';
		foreach ( $posts as $post ) {
			$link  = get_permalink( $post );
			$title = get_the_title( $post );
			echo '<li>';
				echo '<a href="' . $link . '">';
					echo $title;
				echo '</a>';
			echo '</li>';
		}
		echo '<li>';
			echo '<a href="/projects">' . 'All' . '</a>';
		echo '<li>';
		echo '</ul>';
	}
