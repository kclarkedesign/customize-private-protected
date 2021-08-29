<?php
/*
Plugin Name: Customize Private & Protected
Plugin URI: http://kirkclarke.com
Description: Use WP Customizer to modify elements of password protected and private posts and pages.
Version: 1.0.1
Author: Kirk Clarke
Author URI: http://kirkclarke.com
*/

/**
 * Add customizer options
 */
function cpp_register_customizer( $wp_customize ) {
	
	$wp_customize->add_section( 
		'cpp_plugin_settings', 
		array(
			'title' => 'Custom Password Protected',
            'priority'    => 20

		) 
	);

	//  =============================
    //  = Title Prefix    =
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
			'settings'	=> 'cpp_prefix_protected'
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
			'settings'	=> 'cpp_prefix_private'
		) 
	);

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
			'settings'	=> 'cpp_hide_prefix'
		) 
	);

	//  =============================
    //  = Intro Text                =
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
			'settings'	=> 'cpp_text_intro'
		) 
	);

	//  =============================
    //  = Protected Label Text                =
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
			'settings'	=> 'cpp_label_text'
		) 
	);

	//  =============================
    //  = Protected Button Text                =
    //  =============================

	$wp_customize->add_setting( 
		'cpp_button_text', 
		array(
			'type' 				=> 'option',
			'capability'		=> 'manage_options',
			'default' 			=> 'Submit',
			'sanitize_callback' => 'wp_kses_post',
		) 
	);

	$wp_customize->add_control( 
		'cpp_button_text', 
		array(
			'label' 	=> 'Protected Button Text',
			'section' 	=> 'cpp_plugin_settings',
			'settings'	=> 'cpp_button_text'
		) 
	);
}

add_action( 'customize_register', 'cpp_register_customizer' );

/**
 * Customize Private/Protected prefix
 */

function set_protected_prefix() {
	$cpp_prefix = get_option( 'cpp_prefix_protected', 'Protected: ' );
	$cpp_hide_prefix = get_option( 'cpp_hide_prefix', false );
	$cpp_prefix = ( true == $cpp_hide_prefix ) ? '' : $cpp_prefix . ' '; 
	$cpp_prefix = ( post_password_required() ) ? $cpp_prefix : '';		

	return __($cpp_prefix . '%s');
}

add_filter( 'protected_title_format', 'set_protected_prefix' );


function set_private_prefix() {
	$cpp_prefix = get_option( 'cpp_prefix_private', 'Private: ' );
	$cpp_hide_prefix = get_option( 'cpp_hide_prefix', false );
	$cpp_prefix = ( true == $cpp_hide_prefix ) ? '' : $cpp_prefix . ' ';
	$cpp_prefix = ( get_post_status ( get_the_ID() ) == 'private' ) ? $cpp_prefix : '';

	return __($cpp_prefix . '%s');
}
add_filter( 'private_title_format', 'set_private_prefix' );

/**
 * Add Widget areas
 */
function cpp_register_widgets() {

	register_sidebar( array(
		'name' => __( 'Widgetized Before Password Form', 'cpp' ),
		'id' => 'widgetized-before-password-form',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	));

	register_sidebar( array(
		'name' => __( 'Widgetized After Password Form', 'cpp' ),
		'id' => 'widgetized-after-password-form',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	));
		
}

add_action( 'widgets_init', 'cpp_register_widgets' );

/**
 * Build Widgets and Customized Protected form area
 */

function cpp_form( $output ) {
    global $post;

	/* Create Before form area */
    ob_start();
    dynamic_sidebar( 'widgetized-before-password-form' );
    $before_area = ob_get_contents();
    ob_end_clean();

	$cpp_intro = get_option( 'cpp_text_intro', '' );
	$cpp_label = get_option( 'cpp_label_text', '' );
	$cpp_button_text = get_option( 'cpp_button_text', '' );
    $label_selector = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );

	/* Create After form area */
    ob_start();
    dynamic_sidebar( 'widgetized-after-password-form' );
    $after_area = ob_get_contents();
    ob_end_clean();

    $output = $before_area . '<p>' . $cpp_intro . '</p>' . '<form class="cpp-form" action="' . esc_attr( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
    ' . '<label class="cpp-label" for="' .  esc_attr__($label_selector) . '">' . $cpp_label . ' </label><input class="cpp-password" name="post_password" id="' . $label_selector . '" type="password" size="20" maxlength="20" /><input class="cpp-submit" type="submit" name="Submit" value="' . esc_attr__( $cpp_button_text ) . '" />
    </form>' . $after_area ;
    return $output;
}

add_filter( 'the_password_form', 'cpp_form', 11);


function cpp_styles() {
    wp_register_style( 'cpp-styles', 'cpp/css/style.css' );
	wp_enqueue_style( 'cpp-styles' );
}

add_action('wp_enqueue_scripts', 'cpp_styles');

// Sanitize text
function sanitize_text( $text ) {
    return sanitize_text_field( $text );
}

?>