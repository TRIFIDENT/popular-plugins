<?php
namespace AMPforWP\AMPVendor;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class AMP_String_Utils {
	public static function endswith( $haystack, $needle ) {
		return '' !== $haystack
			&& '' !== $needle
			&& substr( $haystack, -strlen( $needle ) ) === $needle;
	}
}
