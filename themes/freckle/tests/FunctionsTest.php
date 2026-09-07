<?php
/**
 * Tests for pure helper functions in includes/functions.php.
 *
 * @package Boilerplate
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests for the video-ID extraction helpers.
 */
final class FunctionsTest extends TestCase {

	/**
	 * Standard watch URL should yield the video ID.
	 */
	public function test_get_youtube_id_extracts_id_from_watch_url() {
		$this->assertSame( 'dQw4w9WgXcQ', get_youtube_id( 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' ) );
	}

	/**
	 * Shortened youtu.be URL should yield the video ID.
	 */
	public function test_get_youtube_id_extracts_id_from_short_url() {
		$this->assertSame( 'dQw4w9WgXcQ', get_youtube_id( 'https://youtu.be/dQw4w9WgXcQ' ) );
	}

	/**
	 * Shorts URL should yield the video ID.
	 */
	public function test_get_youtube_id_extracts_id_from_shorts_url() {
		$this->assertSame( 'dQw4w9WgXcQ', get_youtube_id( 'https://www.youtube.com/shorts/dQw4w9WgXcQ' ) );
	}

	/**
	 * Empty input should return null.
	 */
	public function test_get_youtube_id_returns_null_for_empty_url() {
		$this->assertNull( get_youtube_id( '' ) );
	}

	/**
	 * Non-YouTube URL should return null.
	 */
	public function test_get_youtube_id_returns_null_for_non_youtube_url() {
		$this->assertNull( get_youtube_id( 'https://example.com/video' ) );
	}

	/**
	 * Standard vimeo.com URL should yield the video ID.
	 */
	public function test_get_vimeo_id_extracts_id_from_url() {
		$this->assertSame( '76979871', get_vimeo_id( 'https://vimeo.com/76979871' ) );
	}

	/**
	 * Empty input should return null.
	 */
	public function test_get_vimeo_id_returns_null_for_empty_url() {
		$this->assertNull( get_vimeo_id( '' ) );
	}

	/**
	 * Non-Vimeo URL should return null.
	 */
	public function test_get_vimeo_id_returns_null_for_non_vimeo_url() {
		$this->assertNull( get_vimeo_id( 'https://example.com/video' ) );
	}
}
