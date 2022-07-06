<?php
/*
 * Plugin Name: WP-LoginAttempts
 * Plugin URI:  https://github.com/sandrolenz/WP-LoginAttempts
 * Description: Set a limit and timeout to wrong logins to prevent brute forcing
 * Version:     1.1
 * Author:      Sandro Lenz <sl@sandrolenz.ch>
 * Author URI:  mailto:sl@sandrolenz.ch
 */

add_filter('authenticate', 'TR_authenticate', 30, 3);
add_action('wp_login_failed', 'TR_login_failed', 10, 1);

// Check amount of previous logins and set timeout if more than 3
function TR_authenticate($user, $username, $password)
{
    if ($data = get_transient('failed_login')) {

        if ($data['tries'] >= 3) {
            $timeout = get_option('_transient_timeout_' . 'failed_login');
            $timeleft = $timeout - time();

            // Display Error message
            return new WP_Error(
                'too_many_tries',
                sprintf(('<strong>ERROR</strong>: Too many login attempts. Please try again in %d seconds.'), $timeleft)
            );
        }
    }

    return $user;
}

// Count wrong login attempts
function TR_login_failed($username)
{
    if ($data = get_transient('failed_login')) {
        $data['tries']++;
    } else {
        $data = ['tries' => 1];
    }

    set_transient('failed_login', $data, 300);
}

// EOF - WP-LoginAttempts\login_attempts.php
