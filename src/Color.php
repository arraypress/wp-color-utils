<?php
/**
 * Color Utilities
 *
 * This class provides essential color utility functions for common web development tasks.
 * Focuses on practical, frequently-used operations with self-contained methods that have
 * no external dependencies. Each method handles its own validation and edge cases.
 *
 * @package     ArrayPress\ColorUtils
 * @copyright   Copyright (c) 2025, ArrayPress Limited
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

namespace ArrayPress\ColorUtils;

/**
 * Class Color
 *
 * Utility functions for color manipulation and validation.
 */
class Color {

	/**
	 * Convert hexadecimal color to RGB values.
	 *
	 * @param string $hex Hexadecimal color code (with or without #).
	 *
	 * @return array|null Array with 'red', 'green', 'blue' keys or null if invalid.
	 */
	public static function hex_to_rgb( string $hex ): ?array {
		$hex = ltrim( $hex, '#' );

		// Handle 3-character hex codes
		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		if ( strlen( $hex ) !== 6 || ! ctype_xdigit( $hex ) ) {
			return null;
		}

		return [
			'red'   => hexdec( substr( $hex, 0, 2 ) ),
			'green' => hexdec( substr( $hex, 2, 2 ) ),
			'blue'  => hexdec( substr( $hex, 4, 2 ) )
		];
	}

	/**
	 * Convert RGB values to hexadecimal color.
	 *
	 * @param int $red   Red value (0-255).
	 * @param int $green Green value (0-255).
	 * @param int $blue  Blue value (0-255).
	 *
	 * @return string Hexadecimal color code.
	 */
	public static function rgb_to_hex( int $red, int $green, int $blue ): string {
		$red   = max( 0, min( 255, $red ) );
		$green = max( 0, min( 255, $green ) );
		$blue  = max( 0, min( 255, $blue ) );

		return sprintf( "#%02x%02x%02x", $red, $green, $blue );
	}

	/**
	 * Convert hex color to RGBA string for CSS.
	 *
	 * @param string $hex   Hexadecimal color code (with or without #).
	 * @param float  $alpha Alpha value between 0 and 1.
	 *
	 * @return string|null CSS rgba() string or null if invalid hex.
	 */
	public static function hex_to_rgba( string $hex, float $alpha = 1.0 ): ?string {
		$rgb = self::hex_to_rgb( $hex );
		if ( $rgb === null ) {
			return null;
		}

		$alpha = max( 0, min( 1, $alpha ) );

		return sprintf( 'rgba(%d, %d, %d, %.2f)', $rgb['red'], $rgb['green'], $rgb['blue'], $alpha );
	}

	/**
	 * Get contrasting text color (black or white) for a background color.
	 *
	 * @param string $hex_color Background color in hex format.
	 *
	 * @return string Either '#000000' or '#ffffff' for optimal contrast.
	 */
	public static function get_contrast_color( string $hex_color ): string {
		$rgb = self::hex_to_rgb( $hex_color );
		if ( $rgb === null ) {
			return '#000000';
		}

		// Calculate relative luminance using WCAG formula
		$luminance = ( 0.299 * $rgb['red'] + 0.587 * $rgb['green'] + 0.114 * $rgb['blue'] ) / 255;

		return $luminance > 0.5 ? '#000000' : '#ffffff';
	}

	/**
	 * Lighten a hex color by a percentage.
	 *
	 * @param string $hex_color Hexadecimal color code.
	 * @param int    $percent   Percentage to lighten (0-100).
	 *
	 * @return string|null Lightened hex color or null if invalid.
	 */
	public static function lighten( string $hex_color, int $percent = 10 ): ?string {
		$rgb = self::hex_to_rgb( $hex_color );
		if ( $rgb === null ) {
			return null;
		}

		$percent = max( 0, min( 100, $percent ) );
		$factor  = $percent / 100;

		$red   = min( 255, $rgb['red'] + ( 255 - $rgb['red'] ) * $factor );
		$green = min( 255, $rgb['green'] + ( 255 - $rgb['green'] ) * $factor );
		$blue  = min( 255, $rgb['blue'] + ( 255 - $rgb['blue'] ) * $factor );

		return self::rgb_to_hex( (int) round( $red ), (int) round( $green ), (int) round( $blue ) );
	}

	/**
	 * Darken a hex color by a percentage.
	 *
	 * @param string $hex_color Hexadecimal color code.
	 * @param int    $percent   Percentage to darken (0-100).
	 *
	 * @return string|null Darkened hex color or null if invalid.
	 */
	public static function darken( string $hex_color, int $percent = 10 ): ?string {
		$rgb = self::hex_to_rgb( $hex_color );
		if ( $rgb === null ) {
			return null;
		}

		$percent = max( 0, min( 100, $percent ) );
		$factor  = 1 - ( $percent / 100 );

		$red   = $rgb['red'] * $factor;
		$green = $rgb['green'] * $factor;
		$blue  = $rgb['blue'] * $factor;

		return self::rgb_to_hex( (int) round( $red ), (int) round( $green ), (int) round( $blue ) );
	}

	/**
	 * Convert color to grayscale.
	 *
	 * Uses the luminance formula for perceptually accurate grayscale conversion.
	 *
	 * @param string $hex Hexadecimal color code.
	 *
	 * @return string|null Grayscale hex color or null if invalid.
	 */
	public static function grayscale( string $hex ): ?string {
		$rgb = self::hex_to_rgb( $hex );
		if ( $rgb === null ) {
			return null;
		}

		// Use luminance formula for perceptually accurate grayscale
		$gray = (int) round( 0.299 * $rgb['red'] + 0.587 * $rgb['green'] + 0.114 * $rgb['blue'] );

		return self::rgb_to_hex( $gray, $gray, $gray );
	}

	/**
	 * Check if a hex color is considered "dark".
	 *
	 * @param string $hex_color Hexadecimal color code.
	 *
	 * @return bool True if dark, false if light or invalid.
	 */
	public static function is_dark( string $hex_color ): bool {
		$rgb = self::hex_to_rgb( $hex_color );
		if ( $rgb === null ) {
			return false;
		}

		// Calculate brightness using perceived lightness formula
		$brightness = ( $rgb['red'] * 299 + $rgb['green'] * 587 + $rgb['blue'] * 114 ) / 1000;

		return $brightness < 128;
	}

	/**
	 * Check if a hex color is considered "light".
	 *
	 * @param string $hex_color Hexadecimal color code.
	 *
	 * @return bool True if light, false if dark or invalid.
	 */
	public static function is_light( string $hex_color ): bool {
		return ! self::is_dark( $hex_color );
	}

	/**
	 * Check if a string is a valid hex color.
	 *
	 * @param string $hex Color string to validate.
	 *
	 * @return bool True if valid hex color.
	 */
	public static function is_valid_hex( string $hex ): bool {
		return self::sanitize_hex( $hex ) !== null;
	}

	/**
	 * Sanitize a hex color code.
	 *
	 * @param string $hex_color Input color string.
	 *
	 * @return string|null Valid hex color with # prefix or null if invalid.
	 */
	public static function sanitize_hex( string $hex_color ): ?string {
		$hex = ltrim( $hex_color, '#' );

		// Handle 3-character hex codes
		if ( strlen( $hex ) === 3 && ctype_xdigit( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		if ( strlen( $hex ) === 6 && ctype_xdigit( $hex ) ) {
			return '#' . strtolower( $hex );
		}

		return null;
	}

	/**
	 * Generate a random hex color.
	 *
	 * @return string Random hex color code.
	 */
	public static function random(): string {
		return sprintf( "#%06x", wp_rand( 0, 0xFFFFFF ) );
	}

	/**
	 * Mix two hex colors together.
	 *
	 * @param string $color1 First hex color.
	 * @param string $color2 Second hex color.
	 * @param float  $ratio  Mix ratio (0.0 = all color1, 1.0 = all color2).
	 *
	 * @return string|null Mixed hex color or null if either input is invalid.
	 */
	public static function mix( string $color1, string $color2, float $ratio = 0.5 ): ?string {
		$rgb1 = self::hex_to_rgb( $color1 );
		$rgb2 = self::hex_to_rgb( $color2 );

		if ( $rgb1 === null || $rgb2 === null ) {
			return null;
		}

		$ratio = max( 0, min( 1, $ratio ) );

		$red   = $rgb1['red'] + ( $rgb2['red'] - $rgb1['red'] ) * $ratio;
		$green = $rgb1['green'] + ( $rgb2['green'] - $rgb1['green'] ) * $ratio;
		$blue  = $rgb1['blue'] + ( $rgb2['blue'] - $rgb1['blue'] ) * $ratio;

		return self::rgb_to_hex( (int) round( $red ), (int) round( $green ), (int) round( $blue ) );
	}

	/**
	 * Get complementary color (opposite on color wheel).
	 *
	 * @param string $hex Hexadecimal color code.
	 *
	 * @return string|null Complementary hex color or null if invalid.
	 */
	public static function get_complementary( string $hex ): ?string {
		$rgb = self::hex_to_rgb( $hex );
		if ( $rgb === null ) {
			return null;
		}

		return self::rgb_to_hex(
			255 - $rgb['red'],
			255 - $rgb['green'],
			255 - $rgb['blue']
		);
	}

	/**
	 * Calculate WCAG contrast ratio between two colors.
	 *
	 * @param string $color1 First hex color.
	 * @param string $color2 Second hex color.
	 *
	 * @return float|null Contrast ratio (1-21) or null if invalid colors.
	 */
	public static function get_contrast_ratio( string $color1, string $color2 ): ?float {
		$rgb1 = self::hex_to_rgb( $color1 );
		$rgb2 = self::hex_to_rgb( $color2 );

		if ( $rgb1 === null || $rgb2 === null ) {
			return null;
		}

		// Calculate relative luminance for each color
		$l1 = self::get_relative_luminance( $rgb1['red'], $rgb1['green'], $rgb1['blue'] );
		$l2 = self::get_relative_luminance( $rgb2['red'], $rgb2['green'], $rgb2['blue'] );

		// Ensure L1 is the lighter color
		if ( $l2 > $l1 ) {
			$temp = $l1;
			$l1   = $l2;
			$l2   = $temp;
		}

		// Calculate contrast ratio
		return ( $l1 + 0.05 ) / ( $l2 + 0.05 );
	}

	/**
	 * Calculate relative luminance according to WCAG 2.1.
	 *
	 * @param int $red   Red value (0-255).
	 * @param int $green Green value (0-255).
	 * @param int $blue  Blue value (0-255).
	 *
	 * @return float Relative luminance value.
	 */
	private static function get_relative_luminance( int $red, int $green, int $blue ): float {
		// Convert RGB to sRGB
		$rgb = [ $red / 255, $green / 255, $blue / 255 ];

		// Apply gamma correction
		foreach ( $rgb as $key => $val ) {
			if ( $val <= 0.03928 ) {
				$rgb[ $key ] = $val / 12.92;
			} else {
				$rgb[ $key ] = pow( ( $val + 0.055 ) / 1.055, 2.4 );
			}
		}

		// Calculate luminance using WCAG formula
		return 0.2126 * $rgb[0] + 0.7152 * $rgb[1] + 0.0722 * $rgb[2];
	}

	/**
	 * Check if contrast ratio meets WCAG AA standard.
	 *
	 * @param string $color1     First hex color.
	 * @param string $color2     Second hex color.
	 * @param bool   $large_text Whether checking for large text (false = normal text).
	 *
	 * @return bool True if meets AA standard, false otherwise.
	 */
	public static function meets_wcag_aa( string $color1, string $color2, bool $large_text = false ): bool {
		$ratio = self::get_contrast_ratio( $color1, $color2 );

		if ( $ratio === null ) {
			return false;
		}

		// AA standards: 4.5:1 for normal text, 3:1 for large text
		$required_ratio = $large_text ? 3.0 : 4.5;

		return $ratio >= $required_ratio;
	}

	/**
	 * Check if contrast ratio meets WCAG AAA standard.
	 *
	 * @param string $color1     First hex color.
	 * @param string $color2     Second hex color.
	 * @param bool   $large_text Whether checking for large text (false = normal text).
	 *
	 * @return bool True if meets AAA standard, false otherwise.
	 */
	public static function meets_wcag_aaa( string $color1, string $color2, bool $large_text = false ): bool {
		$ratio = self::get_contrast_ratio( $color1, $color2 );

		if ( $ratio === null ) {
			return false;
		}

		// AAA standards: 7:1 for normal text, 4.5:1 for large text
		$required_ratio = $large_text ? 4.5 : 7.0;

		return $ratio >= $required_ratio;
	}

	/**
	 * Adjust a color to meet minimum contrast ratio with another color.
	 *
	 * @param string $adjustable_color Color to adjust.
	 * @param string $fixed_color      Color to contrast against.
	 * @param float  $min_ratio        Minimum contrast ratio (default 4.5 for WCAG AA).
	 *
	 * @return string|null Adjusted hex color or null if cannot meet ratio.
	 */
	public static function adjust_for_contrast( string $adjustable_color, string $fixed_color, float $min_ratio = 4.5 ): ?string {
		$current_ratio = self::get_contrast_ratio( $adjustable_color, $fixed_color );

		if ( $current_ratio === null ) {
			return null;
		}

		// Already meets requirement
		if ( $current_ratio >= $min_ratio ) {
			return $adjustable_color;
		}

		// Determine if we should lighten or darken
		$should_darken = self::is_light( $fixed_color );

		// Try adjusting in steps
		for ( $i = 10; $i <= 100; $i += 10 ) {
			$adjusted = $should_darken
				? self::darken( $adjustable_color, $i )
				: self::lighten( $adjustable_color, $i );

			if ( $adjusted === null ) {
				continue;
			}

			$ratio = self::get_contrast_ratio( $adjusted, $fixed_color );

			if ( $ratio !== null && $ratio >= $min_ratio ) {
				return $adjusted;
			}
		}

		// If we can't meet the ratio, return the maximum adjustment
		return $should_darken
			? self::darken( $adjustable_color, 100 )
			: self::lighten( $adjustable_color, 100 );
	}

}