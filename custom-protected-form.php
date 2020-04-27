<?php
/*
Plugin Name: Custom Password Protected
Plugin URI: http://kirkclarke.com
Description: Customize the default password protected text and add widgetization. 
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
    //  = Protected Title Prefix    =
    //  =============================

	$wp_customize->add_setting( 
		'cpp_prefix', 
		array(
			'type' 			=> 'option',
			'capability'	=> 'manage_options',
			'default' 		=> 'Protected: ',
			'sanitize_callback' => 'wp_kses_post',
		) 
	);

	$wp_customize->add_control( 
		'cpp_prefix', 
		array(
			'label' 	=> 'Protected Title Prefix',
			'section' 	=> 'cpp_plugin_settings',
			'settings'	=> 'cpp_prefix'
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
}

add_action( 'customize_register', 'cpp_register_customizer' );

/**
 * Customize Private/Protected prefix
 */

function set_protected_prefix() {
	$cpp_prefix = get_option( 'cpp_prefix', 'Protected: ' );
	$cpp_hide_prefix = get_option( 'cpp_hide_prefix', false );
	$cpp_prefix = ( true == $cpp_hide_prefix ) ? '' : $cpp_prefix . ' '; 
	$cpp_prefix = ( post_password_required() ) ? $cpp_prefix : '';		

	return __($cpp_prefix . '%s');
}

add_filter( 'protected_title_format', 'set_protected_prefix' );
// TODO: add in private page prefix behavior add_filter( 'private_title_format', 'set_protected_prefix' );

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

function cpp_form() {
    global $post;
    /* Create Before form area */
    ob_start();
    dynamic_sidebar( 'widgetized-before-password-form' );
    $before_area = ob_get_contents();
    ob_end_clean();
	
	
	$cpp_intro = get_option( 'cpp_text_intro', '' );

	/* Create After form area */
    ob_start();
    dynamic_sidebar( 'widgetized-after-password-form' );
    $after_area = ob_get_contents();
    ob_end_clean();

    $label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
    $o = $before_area . '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
    ' . $cpp_intro . '
    <label for="' . $label . '">' . __( "Password:" ) . ' </label><input name="post_password" id="' . $label . '" type="password" size="20" maxlength="20" /><input type="submit" name="Submit" value="' . esc_attr__( "Submit" ) . '" />
    </form>' . $after_area ;
    return $o;
}
add_filter( 'the_password_form', 'cpp_form' );

// Sanitize text
function sanitize_text( $text ) {
    return sanitize_text_field( $text );
}

?>