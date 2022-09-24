<?php
/*
Plugin Name: Customize Private & Protected
Plugin URI: https://github.com/kclarkedesign/cpp
Description: Use WP Customize to modify elements of password protected and private posts and pages.
Version: 1.0.0
Author: Kirk Clarke
Author URI: http://kirkclarke.com
*/

function get_filters_for( $hook = '' ) {
    global $wp_filter;
    if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
        return;

    return $wp_filter[$hook];
}

/**
 * Add customizer options
 */
function customize_pp_plugin_register_customizer($wp_customize)
{

	$wp_customize->add_section(
		'cpp_plugin_settings',
		array(
			'title' => 'Custom Private & Protected',
			'priority'    => 20

		)
	);

	//  =============================
	//  = Hide Prefix
	//  =============================

	$wp_customize->add_setting(
		'cpp_hide_prefix',
		array(
			'type' 			=> 'option',
			'capability'	=> 'manage_options',
			'default' 		=> false,
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		'cpp_hide_prefix',
		array(
			'type'		=> 'checkbox',
			'label' 	=> 'Hide Prefix',
			'section' 	=> 'cpp_plugin_settings',
			'settings'	=> 'cpp_hide_prefix',
		)
	);

	//  =============================
	//  = Use Default Theme Form
	//  =============================

	$wp_customize->add_setting(
		'cpp_use_default_form',
		array(
			'type' 			=> 'option',
			'capability'	=> 'manage_options',
			'default' 		=> false,
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		'cpp_use_default_form',
		array(
			'type'		=> 'checkbox',
			'label' 	=> 'Use Default form',
			'section' 	=> 'cpp_plugin_settings',
			'settings'	=> 'cpp_use_default_form',
		)
	);

	//  =============================
	//  = Title Prefix
	//  =============================

	$wp_customize->add_setting(
		'cpp_prefix_protected',
		array(
			'type' 			=> 'option',
			'capability'	=> 'manage_options',
			'default' 		=> 'Protected: ',
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		'cpp_prefix_protected',
		array(
			'label' 	=> 'Protected Title Prefix',
			'section' 	=> 'cpp_plugin_settings',
			'settings'	=> 'cpp_prefix_protected',
			'active_callback' => 'customize_pp_plugin_hide_prefix_condition'
		)
	);

	$wp_customize->add_setting(
		'cpp_prefix_private',
		array(
			'type' 			=> 'option',
			'capability'	=> 'manage_options',
			'default' 		=> 'Private: ',
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		'cpp_prefix_private',
		array(
			'label' 	=> 'Private Title Prefix',
			'section' 	=> 'cpp_plugin_settings',
			'settings'	=> 'cpp_prefix_private',
			'active_callback' => 'customize_pp_plugin_hide_prefix_condition'
		)
	);

	//  =============================
	//  = Intro Text
	//  =============================

	$wp_customize->add_setting(
		'cpp_text_intro',
		array(
			'type' 				=> 'option',
			'capability'		=> 'manage_options',
			'default' 			=> 'To view this protected content, enter the password below:',
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		'cpp_text_intro',
		array(
			'type'		=> 'textarea',
			'label' 	=> 'Protected Intro Text',
			'section' 	=> 'cpp_plugin_settings',
			'settings'	=> 'cpp_text_intro',
			'active_callback' => 'customize_pp_plugin_hide_form_options_condition'
		)
	);

	//  =============================
	//  = Protected Label Text
	//  =============================

	$wp_customize->add_setting(
		'cpp_label_text',
		array(
			'type' 				=> 'option',
			'capability'		=> 'manage_options',
			'default' 			=> 'Password: ',
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		'cpp_label_text',
		array(
			'type'		=> 'textarea',
			'label' 	=> 'Protected Label Text',
			'section' 	=> 'cpp_plugin_settings',
			'settings'	=> 'cpp_label_text',
			'active_callback' => 'customize_pp_plugin_hide_form_options_condition'
		)
	);

	//  =============================
	//  = Protected Button Text
	//  =============================

	$wp_customize->add_setting(
		'cpp_button_text',
		array(
			'type' 				=> 'option',
			'capability'		=> 'manage_options',
			'default' 			=> 'Enter',
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		'cpp_button_text',
		array(
			'label' 	=> 'Protected Button Text',
			'section' 	=> 'cpp_plugin_settings',
			'settings'	=> 'cpp_button_text',
			'active_callback' => 'customize_pp_plugin_hide_form_options_condition'
		)
	);
}

add_action('customize_register', 'customize_pp_plugin_register_customizer');

/** TODO: Add ability to customize button style and/or additonal button styles */

/**
 * Show Prefix controls only if a hide is false.
 *
 * @param WP_Customize_Manager object
 * @return bool
 */
function customize_pp_plugin_hide_prefix_condition($control) {
	$setting = $control->manager->get_setting('cpp_hide_prefix');
	if(true == $setting->value()) {
		return false;
	} else {
		return true;
	}
}

/**
 * Show form element controls only if use default form is false.
 *
 * @param WP_Customize_Manager object
 * @return bool
 */

function customize_pp_plugin_hide_form_options_condition($control) {
	$setting = $control->manager->get_setting('cpp_use_default_form');
	if(true == $setting->value()) {
		return false;
	} else {
		return true;
	}
}


/**
 * Customize Private/Protected prefix
 */

function customize_pp_plugin_set_protected_prefix()
{
	$cpp_hide_prefix = get_option('cpp_hide_prefix', false);
	$cpp_prefix = get_option('cpp_prefix_protected', 'Protected: ');
	$cpp_prefix = (true == $cpp_hide_prefix) ? '' : $cpp_prefix . ' ';
	$cpp_prefix = (post_password_required()) ? $cpp_prefix : '';

	return __($cpp_prefix . '%s');
}

add_filter('protected_title_format', 'customize_pp_plugin_set_protected_prefix');


function customize_pp_plugin_set_private_prefix()
{
	$cpp_hide_prefix = get_option('cpp_hide_prefix', false);
	$cpp_prefix = get_option('cpp_prefix_private', 'Private: ');
	$cpp_prefix = (true == $cpp_hide_prefix) ? '' : $cpp_prefix . ' ';
	$cpp_prefix = (get_post_status(get_the_ID()) == 'private') ? $cpp_prefix : '';

	return __($cpp_prefix . '%s');
}
add_filter('private_title_format', 'customize_pp_plugin_set_private_prefix');

/**
 * Add Widget areas
 */
function customize_pp_plugin_register_widgets()
{

	register_sidebar(array(
		'name' => __('Widgetized Before Password Form', 'cpp'),
		'id' => 'widgetized-before-password-form',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	));

	register_sidebar(array(
		'name' => __('Widgetized After Password Form', 'cpp'),
		'id' => 'widgetized-after-password-form',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	));
}

add_action('widgets_init', 'customize_pp_plugin_register_widgets');

/**
 * Build Widgets and Customized Protected form area
 */

function customize_pp_plugin_form($output)
{
	global $post;

	$cpp_use_default_form = get_option('cpp_use_default_form', false);

	/* Create Before form area */
	ob_start();
	dynamic_sidebar('widgetized-before-password-form');
	$before_area = ob_get_contents();
	ob_end_clean();

	if (false == $cpp_use_default_form) {
		$cpp_intro = get_option('cpp_text_intro', '');
		$cpp_label = get_option('cpp_label_text', 'Password: ');
		$cpp_button_text = get_option('cpp_button_text', 'Enter');
		$label_selector = 'pwbox-' . (empty($post->ID) ? rand() : $post->ID);
	}
	

	/* Create After form area */
	ob_start();
	dynamic_sidebar('widgetized-after-password-form');
	$after_area = ob_get_contents();
	ob_end_clean();

	if (false == $cpp_use_default_form) {
		$output = $before_area . '<p>' . $cpp_intro . '</p>' . '<form class="cpp-form" action="' . esc_attr(site_url('wp-login.php?action=postpass', 'login_post')) . '" class="post-password-form" method="post">
		' . '<label class="cpp-label" for="' .  esc_attr__($label_selector) . '">' . $cpp_label . ' </label><input class="cpp-password" name="post_password" id="' . $label_selector . '" type="password" size="20" maxlength="20" /><input class="cpp-submit" type="submit" name="Submit" value="' . esc_attr__($cpp_button_text) . '" />
		</form>' . $after_area;
	} else if (function_exists('et_password_form')) { /* if divi theme */
		$output = $before_area . et_password_form() . $after_area;
	} else {
		$output = $before_area . $output . $after_area;
	}

	//TODO: detect other themes that use 'the_password_form' hook  get_filters_for('the_password_form')
	return $output;
}

add_filter('the_password_form', 'customize_pp_plugin_form', 11);


/**
 * Handle Admin notice for requesting a review
 */

function customize_pp_plugin_admin_review_notice(){
    global $pagenow;
		if ( $pagenow == 'index.php' || $pagenow == 'edit.php' ) {
		$user = wp_get_current_user();
		if ( in_array( 'Administrator', (array) $user->roles ) || in_array( 'Super Administrator', (array) $user->roles ) ) {
		echo '<div class="notice notice-info is-dismissible">
			  <p>Find Customize Private & Protected helpful? give it a 5-star rating on WordPress</p>
			  <p><a href="https://wordpress.org/support/plugin/customize-private-protected/reviews/#new-post" class="" target="_blank" rel="noopener noreferrer">Sure, you deserve it!</a>
			 </div>';
		}
	}
}
add_action('admin_notices', 'customize_pp_plugin_admin_review_notice');

 
/**
 * Register and enqueue plugin styles
 */

function customize_pp_plugin_styles()
{
	wp_register_style('cpp-styles', plugins_url('css/style.css', __FILE__));
	wp_enqueue_style('cpp-styles');
}

add_action('wp_enqueue_scripts', 'customize_pp_plugin_styles');