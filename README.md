# WordPress Color Utils

A lean WordPress library for essential color operations. Provides practical utilities for color conversions, adjustments, and accessibility helpers with self-contained methods and robust error handling.

## Installation

```bash
composer require arraypress/wp-color-utils
```

## Quick Start

```php
use ArrayPress\ColorUtils\Color;

// Convert colors
$rgb  = Color::hex_to_rgb( '#ff0000' ); // ['red' => 255, 'green' => 0, 'blue' => 0]
$hex  = Color::rgb_to_hex( 255, 0, 0 ); // "#ff0000"
$rgba = Color::hex_to_rgba( '#ff0000', 0.5 ); // "rgba(255, 0, 0, 0.50)"

// Get contrast color for accessibility
$text_color = Color::get_contrast_color( '#ff0000' ); // "#ffffff"

// Adjust brightness
$lighter = Color::lighten( '#ff0000', 20 ); // "#ff3333"
$darker  = Color::darken( '#ff0000', 20 ); // "#cc0000"

// WCAG compliance
$meets_aa = Color::meets_wcag_aa( '#333333', '#ffffff' ); // true
```

## Features

### Core Conversions
- **Hex ↔ RGB**: Convert between hex codes and RGB values
- **Hex to RGBA**: Generate CSS rgba() strings with alpha transparency
- **Color Validation**: Sanitize and validate hex color codes

### Accessibility & WCAG
- **Contrast Colors**: Get optimal black/white text color for any background
- **Contrast Ratio**: Calculate WCAG contrast ratios between colors
- **WCAG AA/AAA**: Check compliance with accessibility standards
- **Auto-Adjust**: Automatically adjust colors to meet contrast requirements

### Color Adjustments
- **Lighten/Darken**: Adjust color brightness by percentage
- **Grayscale**: Convert colors to grayscale
- **Color Mixing**: Blend two colors together
- **Complementary**: Get the opposite color on the color wheel
- **Random Colors**: Generate random hex colors

### Self-Contained & Robust
- Each method handles its own validation
- Graceful error handling with null returns
- No external dependencies
- Support for 3-character hex codes (#f00 → #ff0000)

## API Reference

### Color Conversions

#### `hex_to_rgb(string $hex): ?array`
Convert hexadecimal color to RGB array.

```php
$rgb = Color::hex_to_rgb( '#ff0000' );  // ['red' => 255, 'green' => 0, 'blue' => 0]
$rgb = Color::hex_to_rgb( 'f00' );      // ['red' => 255, 'green' => 0, 'blue' => 0]
$rgb = Color::hex_to_rgb( 'invalid' );  // null
```

#### `rgb_to_hex(int $red, int $green, int $blue): string`
Convert RGB values to hexadecimal color.

```php
$hex = Color::rgb_to_hex( 255, 0, 0 );     // "#ff0000"
$hex = Color::rgb_to_hex( 128, 128, 128 ); // "#808080"
$hex = Color::rgb_to_hex( 300, -10, 0 );   // "#ff0000" (clamped to 0-255)
```

#### `hex_to_rgba(string $hex, float $alpha = 1.0): ?string`
Convert hex color to CSS rgba() string.

```php
$rgba = Color::hex_to_rgba( '#ff0000', 0.5 );   // "rgba(255, 0, 0, 0.50)"
$rgba = Color::hex_to_rgba( '#0000ff' );        // "rgba(0, 0, 255, 1.00)"
$rgba = Color::hex_to_rgba( 'invalid', 0.5 );   // null
```

### Accessibility Helpers

#### `get_contrast_color(string $hex_color): string`
Get optimal text color (black or white) for a background color.

```php
$text = Color::get_contrast_color( '#ffffff' ); // "#000000"
$text = Color::get_contrast_color( '#000000' ); // "#ffffff"
$text = Color::get_contrast_color( '#ff0000' ); // "#ffffff"
$text = Color::get_contrast_color( '#ffff00' ); // "#000000"
```

#### `get_contrast_ratio(string $color1, string $color2): ?float`
Calculate WCAG contrast ratio between two colors. Returns value between 1 and 21.

```php
$ratio = Color::get_contrast_ratio( '#000000', '#ffffff' ); // 21.0
$ratio = Color::get_contrast_ratio( '#777777', '#ffffff' ); // ~4.48
$ratio = Color::get_contrast_ratio( 'invalid', '#ffffff' ); // null
```

#### `meets_wcag_aa(string $color1, string $color2, bool $large_text = false): bool`
Check if contrast ratio meets WCAG AA standard (4.5:1 normal, 3:1 large text).

```php
$passes = Color::meets_wcag_aa( '#333333', '#ffffff' );        // true
$passes = Color::meets_wcag_aa( '#777777', '#ffffff' );        // false
$passes = Color::meets_wcag_aa( '#777777', '#ffffff', true );  // true (large text)
```

#### `meets_wcag_aaa(string $color1, string $color2, bool $large_text = false): bool`
Check if contrast ratio meets WCAG AAA standard (7:1 normal, 4.5:1 large text).

```php
$passes = Color::meets_wcag_aaa( '#000000', '#ffffff' );       // true
$passes = Color::meets_wcag_aaa( '#333333', '#ffffff' );       // true
$passes = Color::meets_wcag_aaa( '#555555', '#ffffff' );       // false
```

#### `adjust_for_contrast(string $adjustable_color, string $fixed_color, float $min_ratio = 4.5): ?string`
Automatically adjust a color to meet minimum contrast ratio.

```php
// Adjust gray to meet AA standard against white
$adjusted = Color::adjust_for_contrast( '#888888', '#ffffff' ); // darker gray

// Adjust for custom ratio
$adjusted = Color::adjust_for_contrast( '#888888', '#000000', 7.0 ); // lighter gray
```

#### `is_dark(string $hex_color): bool`
Check if a color is considered dark.

```php
$dark = Color::is_dark( '#000000' ); // true
$dark = Color::is_dark( '#ffffff' ); // false
$dark = Color::is_dark( '#ff0000' ); // true
$dark = Color::is_dark( 'invalid' ); // false
```

#### `is_light(string $hex_color): bool`
Check if a color is considered light.

```php
$light = Color::is_light( '#ffffff' ); // true
$light = Color::is_light( '#000000' ); // false
$light = Color::is_light( '#ffff00' ); // true
```

### Color Adjustments

#### `lighten(string $hex_color, int $percent = 10): ?string`
Lighten a color by percentage.

```php
$lighter = Color::lighten( '#ff0000', 20 );  // "#ff3333"
$lighter = Color::lighten( '#000000', 50 );  // "#808080"
$lighter = Color::lighten( 'invalid', 20 );  // null
```

#### `darken(string $hex_color, int $percent = 10): ?string`
Darken a color by percentage.

```php
$darker = Color::darken( '#ff0000', 20 );   // "#cc0000"
$darker = Color::darken( '#ffffff', 50 );   // "#808080"
$darker = Color::darken( 'invalid', 20 );   // null
```

#### `grayscale(string $hex): ?string`
Convert a color to grayscale using perceptual luminance.

```php
$gray = Color::grayscale( '#ff0000' ); // "#4c4c4c"
$gray = Color::grayscale( '#00ff00' ); // "#969696"
$gray = Color::grayscale( '#0000ff' ); // "#1d1d1d"
$gray = Color::grayscale( 'invalid' ); // null
```

#### `mix(string $color1, string $color2, float $ratio = 0.5): ?string`
Mix two colors together.

```php
$mixed = Color::mix( '#ff0000', '#0000ff', 0.5 ); // "#800080" (purple)
$mixed = Color::mix( '#000000', '#ffffff', 0.3 ); // "#4d4d4d"
$mixed = Color::mix( '#ff0000', 'invalid', 0.5 ); // null
```

#### `get_complementary(string $hex): ?string`
Get the complementary color (opposite on color wheel).

```php
$complement = Color::get_complementary( '#ff0000' ); // "#00ffff" (cyan)
$complement = Color::get_complementary( '#00ff00' ); // "#ff00ff" (magenta)
$complement = Color::get_complementary( 'invalid' ); // null
```

### Validation & Utilities

#### `is_valid_hex(string $hex): bool`
Check if a string is a valid hex color.

```php
$valid = Color::is_valid_hex( '#ff0000' ); // true
$valid = Color::is_valid_hex( 'f00' );     // true
$valid = Color::is_valid_hex( 'invalid' ); // false
$valid = Color::is_valid_hex( '#gggggg' ); // false
```

#### `sanitize_hex(string $hex_color): ?string`
Clean and validate hex color codes.

```php
$clean = Color::sanitize_hex( 'ff0000' );   // "#ff0000"
$clean = Color::sanitize_hex( '#FF0000' );  // "#ff0000"
$clean = Color::sanitize_hex( 'f00' );      // "#ff0000"
$clean = Color::sanitize_hex( 'invalid' );  // null
```

#### `random(): string`
Generate a random hex color.

```php
$random = Color::random(); // "#a3b2c1" (example)
```

## Use Cases

### Theme Customization
```php
use ArrayPress\ColorUtils\Color;

// Get user's primary color
$primary = get_theme_mod( 'primary_color', '#007cba' );

// Generate theme color variations
$theme_colors = [
    'primary'         => $primary,
    'primary_dark'    => Color::darken( $primary, 15 ),
    'primary_light'   => Color::lighten( $primary, 15 ),
    'primary_gray'    => Color::grayscale( $primary ),
    'text_on_primary' => Color::get_contrast_color( $primary )
];

// Use in CSS
echo "
.button {
    background: {$theme_colors['primary']};
    color: {$theme_colors['text_on_primary']};
}
.button:hover {
    background: {$theme_colors['primary_dark']};
}
.button:disabled {
    background: {$theme_colors['primary_gray']};
}
";
```

### Accessibility Compliance
```php
// Ensure text meets WCAG AA standard
$bg_color   = get_option( 'section_background', '#ffffff' );
$text_color = get_option( 'section_text', '#777777' );

// Check if current colors pass
if ( ! Color::meets_wcag_aa( $text_color, $bg_color ) ) {
    // Auto-adjust text color to meet requirements
    $text_color = Color::adjust_for_contrast( $text_color, $bg_color );
}

echo "<div style='background: {$bg_color}; color: {$text_color};'>";
echo "This text is guaranteed to be accessible!";
echo "</div>";
```

### Dynamic CSS Generation
```php
// Generate CSS with transparency
$accent_color  = '#e74c3c';
$overlay_color = Color::hex_to_rgba( $accent_color, 0.8 );

echo "
.hero-overlay {
    background: {$overlay_color};
}
";
```

### Form Validation
```php
// Sanitize and validate color input
$user_color = Color::sanitize_hex( $_POST['brand_color'] ?? '' );

if ( $user_color ) {
    update_option( 'site_accent_color', $user_color );
} else {
    wp_die( 'Invalid color format' );
}

// Or use is_valid_hex for simple validation
if ( Color::is_valid_hex( $_POST['brand_color'] ?? '' ) ) {
    // Process color
}
```

### Color Palette Generation
```php
// Create a cohesive color scheme
$base_color = '#3498db';

$palette = [
    'primary'     => $base_color,
    'secondary'   => Color::mix( $base_color, '#e74c3c', 0.3 ),
    'complement'  => Color::get_complementary( $base_color ),
    'success'     => Color::mix( $base_color, '#27ae60', 0.4 ),
    'light'       => Color::lighten( $base_color, 40 ),
    'dark'        => Color::darken( $base_color, 30 ),
    'muted'       => Color::grayscale( $base_color )
];
```

### WordPress Customizer Integration
```php
// In customizer settings
function register_color_controls( $wp_customize ) {
    $wp_customize->add_setting( 'header_bg_color', [
        'sanitize_callback' => function ( $color ) {
            return Color::sanitize_hex( $color ) ?: '#ffffff';
        }
    ] );
}

// In theme output
function output_dynamic_styles() {
    $header_bg   = get_theme_mod( 'header_bg_color', '#ffffff' );
    $header_text = Color::get_contrast_color( $header_bg );
    
    // Ensure AAA compliance for better accessibility
    if ( ! Color::meets_wcag_aaa( $header_text, $header_bg ) ) {
        $header_text = Color::adjust_for_contrast( $header_text, $header_bg, 7.0 );
    }

    echo "<style>
    .site-header {
        background-color: {$header_bg};
        color: {$header_text};
    }
    </style>";
}
```

### Disabled State Generation
```php
// Generate disabled/muted versions of UI colors
$button_color = '#3498db';
$disabled_color = Color::grayscale( $button_color );
$disabled_color = Color::lighten( $disabled_color, 20 );

echo "
.button:disabled {
    background: {$disabled_color};
    cursor: not-allowed;
}
";
```

## Error Handling

All methods handle invalid input gracefully:

```php
// Invalid hex codes return null
$result = Color::hex_to_rgb( 'invalid' );     // null
$result = Color::lighten( 'notacolor', 10 );  // null
$result = Color::grayscale( 'bad' );          // null

// RGB values are automatically clamped
$hex = Color::rgb_to_hex( 300, -50, 1000 ); // "#ff00ff" (clamped to 0-255)

// Alpha values are clamped to 0-1
$rgba = Color::hex_to_rgba( '#ff0000', 2.5 ); // Uses 1.0 instead

// Validation is simple
if ( Color::is_valid_hex( $user_input ) ) {
    // Safe to use
}
```

## Requirements

- PHP 7.4+
- WordPress 5.0+

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the GPL-2.0-or-later License.

## Support

- [Documentation](https://github.com/arraypress/wp-color-utils)
- [Issue Tracker](https://github.com/arraypress/wp-color-utils/issues)