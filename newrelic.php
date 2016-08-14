<?php
/*
 * This snippet automatically switches the app name for Wordpress sites.
 * To use it, simply drop it into your theme and include it from functions.php.
 *
 * The intent is to separate out three very different request types:
 * - Anonymous / public
 * - AJAX
 * - Admins
 * These request types tend to have very different performance profiles,
 * so it's important to be able to analyze them as separate groups.
 */

/**
 * Whether or not to interact with New Relic.
 * @return boolean
 */
function custom_new_relic_should_log() {
  return extension_loaded('newrelic') && !defined('IGNORE_NEW_RELIC');
}

/**
 * Standard NR params & app switching
 */
function custom_new_relic_add_custom_params() {
  if(!custom_new_relic_should_log()) return;

  // Set app name for admins & ajax
  $logged_in = is_user_logged_in() ? 'TRUE' : 'FALSE';
  if(($logged_in === 'TRUE') || defined('DOING_CRON')) {
    if(defined('WP_HOME')) newrelic_set_appname(WP_HOME.'-admins');
    $user = wp_get_current_user();
    newrelic_add_custom_parameter( 'current_user', $user->user_email );
  } elseif( is_admin() ) {
    newrelic_set_appname(WP_HOME.'-ajax');
  }
}
add_action('init', 'custom_new_relic_add_custom_params');
