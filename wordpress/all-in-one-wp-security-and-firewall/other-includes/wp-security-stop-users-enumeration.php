<?php
/**
 * Merged by Davide Giunchi, from plugin "Stop User Enumeration"
 */

if (!is_admin() && isset($_SERVER['REQUEST_URI'])) {
	if (preg_match('/(wp-comments-post)/', $_SERVER['REQUEST_URI']) === 0 && !empty($_REQUEST['author'])) {
		wp_die(__('Accessing author info via link is forbidden', 'all-in-one-wp-security-and-firewall'), 403);
	}
}
