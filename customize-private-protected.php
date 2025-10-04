<?php
/*
Plugin Name: Customize Private & Protected
Plugin URI: https://github.com/kclarkedesign/cpp
Description: Use WP Customize to modify elements of password protected and private posts and pages.
Version: 1.3.4
Author: Kirk Clarke
Author URI: http://kirkclarke.com
*/

function get_filters_for($hook = '')
{
	global $wp_filter;
	if (empty($hook) || !isset($wp_filter[$hook]))
		return;

	return $wp_filter[$hook];
}

/**
 * Add customizer options
 */
function customize_pp_plugin_register_customizer($wp_customize)
{

	$transport = ($wp_customize->selective_refresh ? 'postMessage' : 'refresh');

	class WP_Customize_Input_PX_Append_Control extends WP_Customize_Control
	{
		public $type = 'text_input_px_append';

		public function enqueue()
		{
			wp_enqueue_style('cpp_custom_controls_css', plugins_url('css/custom-controls.css', __FILE__));
		}
		/**
		 * Render the control's content.
		 */
		public function render_content()
		{
			$id = $this->id;

			$input_id = '_customize-input-cpp_' . $id;
			$description_id = $input_id . '_description';
			$describedby_attr = $description_id;
			?>
			<?php if (!empty($this->label)): ?>
				<label for="<?php echo esc_attr($input_id); ?>" class="customize-control-title">
					<?php echo esc_html($this->label); ?>
				</label>
			<?php endif; ?>
			<?php if (!empty($this->description)): ?>
				<span id="<?php echo esc_attr($description_id); ?>" class="description customize-control-description">
					<?php echo $this->description; ?>
				</span>
			<?php endif; ?>
			<div class="cpp-customize-control input-group">
				<input id="<?php echo esc_attr($input_id); ?>" class="form-control" type="<?php echo esc_attr($this->type); ?>"
					<?php echo $describedby_attr; ?> 			<?php $this->input_attrs(); ?> 			<?php if (!isset($this->input_attrs['value'])): ?> value="<?php echo esc_attr($this->value()); ?>" <?php endif; ?> 			<?php $this->link(); ?> />
				<div class="input-group-append">
					<span class="input-group-text">px</span>
				</div>
			</div>
		<?php }
	}


	$wp_customize->add_section(
		'cpp_plugin_settings',
		array(
			'title' => __('Customize Private & Protected', 'customize-private-protected'),
			'priority' => 20

		)
	);

	//  =============================
	//  = Hide Prefix
	//  =============================

	$wp_customize->add_setting(
		'cpp_hide_prefix',
		array(
			'type' => 'option',
			'capability' => 'manage_options',
			'default' => false,
			'sanitize_callback' => 'wp_kses_post',
			// 'transport' => $transport
		)
	);

	$wp_customize->add_control(
		'cpp_hide_prefix',
		array(
			'type' => 'checkbox',
			'label' => __('Hide Prefix', 'customize-private-protected'),
			'section' => 'cpp_plugin_settings',
			'settings' => 'cpp_hide_prefix',
		)
	);

	// if (isset($wp_customize->selective_refresh)) {
	// 	$wp_customize->selective_refresh->add_partial(
	// 		'cpp_hide_prefix',
	// 		array(
	// 			'selector' => 'body > h1',
	// 			'container_inclusive' => false,
	// 			'settings' => 'cpp_hide_prefix',
	// 			'render_callback' => function () {
	// 				$cpp_hide_prefix = get_option('cpp_hide_prefix', false);
	// 				$page_prefix_capable = (post_password_required() || get_post_status(get_the_ID()) == 'private');

	// 				if ($page_prefix_capable) {
	// 					$title_format = '';
	// 					$cpp_prefix = '';
	// 					if (post_password_required()) { // page is protected
	// 						$title_format = 'protected_title_format';
	// 						$cpp_prefix = get_option('cpp_prefix_protected', 'Protected: ');
	// 						$cpp_prefix = (true == $cpp_hide_prefix) ? '' : $cpp_prefix . ' ';

	// 					} elseif (get_post_status(get_the_ID()) == 'private') { // page is private
	// 						$title_format = 'private_title_format';
	// 						$cpp_prefix = get_option('cpp_prefix_private', 'Private: ');
	// 						$cpp_prefix = (true == $cpp_hide_prefix) ? '' : $cpp_prefix . ' ';
	// 					}

	// 					add_filter($title_format, function () use ($cpp_prefix) {
	// 						return __($cpp_prefix . '%s');
	// 					});


	// 				}

	// 				return get_the_title();
	// 			},
	// 		)
	// 	);
	// }

	//  =============================
	//  = Use Default Theme Form
	//  =============================

	$wp_customize->add_setting(
		'cpp_use_default_form',
		array(
			'type' => 'option',
			'capability' => 'manage_options',
			'default' => false,
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		'cpp_use_default_form',
		array(
			'type' => 'checkbox',
			'label' => __('Use Default form', 'customize-private-protected'),
			'section' => 'cpp_plugin_settings',
			'settings' => 'cpp_use_default_form',
		)
	);

	//  =============================
	//  = Title Prefix
	//  =============================

	$wp_customize->add_setting(
		'cpp_prefix_private',
		array(
			'type' => 'option',
			'capability' => 'manage_options',
			'default' => 'Private: ',
			'sanitize_callback' => 'wp_kses_post',
			'transport' => $transport,
		)
	);

	$wp_customize->add_control(
		'cpp_prefix_private',
		array(
			'label' => __('Private Title Prefix', 'customize-private-protected'),
			'section' => 'cpp_plugin_settings',
			'settings' => 'cpp_prefix_private',
			'active_callback' => 'customize_pp_plugin_hide_prefix_condition'
		)
	);

	if (isset($wp_customize->selective_refresh)) {
		$wp_customize->selective_refresh->add_partial(
			'cpp_prefix_private',
			array(
				'selector' => 'body > h1',
				'container_inclusive' => false,
				'settings' => 'cpp_prefix_private',
				'render_callback' => function () {
					get_the_title();
				}
			)
		);
	}

	$wp_customize->add_setting(
		'cpp_prefix_protected',
		array(
			'type' => 'option',
			'capability' => 'manage_options',
			'default' => 'Protected: ',
			'sanitize_callback' => 'wp_kses_post',
			'transport' => $transport,
		)
	);

	$wp_customize->add_control(
		'cpp_prefix_protected',
		array(
			'label' => __('Protected Title Prefix', 'customize-private-protected'),
			'section' => 'cpp_plugin_settings',
			'settings' => 'cpp_prefix_protected',
			'active_callback' => 'customize_pp_plugin_hide_prefix_condition',
			'type' => 'text',
		)
	);

	if (isset($wp_customize->selective_refresh)) {
		$wp_customize->selective_refresh->add_partial(
			'cpp_prefix_protected',
			array(
				'selector' => 'body > h1',
				'container_inclusive' => false,
				'settings' => 'cpp_prefix_protected',
				'render_callback' => 'customize_pp_plugin_set_protected_prefix'
			)
		);
	}

	//  =============================
	//  = Intro Text
	//  =============================

	$wp_customize->add_setting(
		'cpp_text_intro',
		array(
			'type' => 'option',
			'capability' => 'manage_options',
			'default' => 'To view this protected content, enter the password below:',
			'sanitize_callback' => 'wp_kses_post',
			'transport' => $transport,
		)
	);

	$wp_customize->add_control(
		'cpp_text_intro',
		array(
			'type' => 'textarea',
			'label' => __('Protected Intro Text', 'customize-private-protected'),
			'section' => 'cpp_plugin_settings',
			'settings' => 'cpp_text_intro',
			'active_callback' => 'customize_pp_plugin_hide_form_options_condition'
		)
	);

	if (isset($wp_customize->selective_refresh)) {
		$wp_customize->selective_refresh->add_partial(
			'cpp_text_intro',
			array(
				'selector' => '.protected-intro-text',
				'container_inclusive' => false,
				'render_callback' => function () {
					return get_option('cpp_text_intro');
				}
			)
		);
	}

	//  =============================
	//  = Protected Label Text
	//  =============================

	$wp_customize->add_setting(
		'cpp_label_text',
		array(
			'type' => 'option',
			'capability' => 'manage_options',
			'default' => 'Password: ',
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		'cpp_label_text',
		array(
			'type' => 'textarea',
			'label' => __('Protected Label Text', 'customize-private-protected'),
			'section' => 'cpp_plugin_settings',
			'settings' => 'cpp_label_text',
			'active_callback' => 'customize_pp_plugin_hide_form_options_condition'
		)
	);

	//  =============================
	//  = Protected Button Text
	//  =============================

	$wp_customize->add_setting(
		'cpp_button_text',
		array(
			'type' => 'option',
			'capability' => 'manage_options',
			'default' => 'Enter',
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		'cpp_button_text',
		array(
			'label' => __('Protected Button Text', 'customize-private-protected'),
			'section' => 'cpp_plugin_settings',
			'settings' => 'cpp_button_text',
			'active_callback' => 'customize_pp_plugin_hide_form_options_condition'
		)
	);

	//  =============================
	//  = Protected Button Appearance Panel
	//  =============================

	/** TODO: Add ability to customize button style and/or additonal button styles */

	$wp_customize->add_setting(
		'cpp_button_x_padding',
		array(
			'type' => 'option',
			'capability' => 'manage_options',
			'default' => 20,
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Input_PX_Append_Control(
			$wp_customize,
			'cpp_button__x_padding',
			array(
				'label' => __('Protected Button Horizontal Padding', 'customize-private-protected'),
				'description' => __('Controls padding inside the button to the left and right of the button text in pixels (px). Leave blank for default', 'customize-private-protected'),
				'type' => 'number',
				'settings' => 'cpp_button_x_padding',
				'section' => 'cpp_plugin_settings',
				'input_attrs' => array(
					'min' => 0,
				),
				'active_callback' => 'customize_pp_plugin_hide_form_options_condition'
			)
		)
	);

	$wp_customize->add_setting(
		'cpp_button_y_padding',
		array(
			'type' => 'option',
			'capability' => 'manage_options',
			'default' => 10,
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Input_PX_Append_Control(
			$wp_customize,
			'cpp_button_y_padding',
			array(
				'label' => __('Protected Button Vertical Padding', 'customize-private-protected'),
				'description' => __('Controls padding inside the button above and below the button text in pixels (px). Leave blank for default', 'customize-private-protected'),
				'type' => 'number',
				'settings' => 'cpp_button_y_padding',
				'section' => 'cpp_plugin_settings',
				'input_attrs' => array(
					'min' => 0,
				),
				'active_callback' => 'customize_pp_plugin_hide_form_options_condition'
			)
		)
	);
}

add_action('customize_register', 'customize_pp_plugin_register_customizer');



/**
 * Show Prefix controls only if a hide is false.
 *
 * @param WP_Customize_Manager object
 * @return bool
 */
function customize_pp_plugin_hide_prefix_condition($control)
{
	$setting = $control->manager->get_setting('cpp_hide_prefix');
	if (true == $setting->value()) {
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

function customize_pp_plugin_hide_form_options_condition($control)
{
	$setting = $control->manager->get_setting('cpp_use_default_form');
	if (true == $setting->value()) {
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

	return __($cpp_prefix . '%s', 'customize-private-protected');
}

add_filter('protected_title_format', 'customize_pp_plugin_set_protected_prefix');

function customize_pp_plugin_set_private_prefix()
{
	$cpp_hide_prefix = get_option('cpp_hide_prefix', false);
	$cpp_prefix = get_option('cpp_prefix_private', 'Private: ');
	$cpp_prefix = (true == $cpp_hide_prefix) ? '' : $cpp_prefix . ' ';
	$cpp_prefix = (get_post_status(get_the_ID()) == 'private') ? $cpp_prefix : '';

	return __($cpp_prefix . '%s', 'customize-private-protected');
}
add_filter('private_title_format', 'customize_pp_plugin_set_private_prefix');

/**
 * Add Widget areas
 */
function customize_pp_plugin_register_widgets()
{

	register_sidebar(
		array(
			'name' => __('Widgetized Before Password Form', 'customize-private-protected'),
			'id' => 'widgetized-before-password-form',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);

	register_sidebar(
		array(
			'name' => __('Widgetized After Password Form', 'customize-private-protected'),
			'id' => 'widgetized-after-password-form',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
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

		$y_padding = ('' == get_option('cpp_button_y_padding')) ? '' : get_option('cpp_button_y_padding') . 'px ';
		$x_padding = ('' == get_option('cpp_button_x_padding')) ? '' : get_option('cpp_button_x_padding') . 'px;';
		$cpp_button_padding = ('' == $y_padding && '' == $x_padding) ? '' : 'padding: ' . $y_padding . $x_padding . '';

		$label_selector = 'pwbox-' . (empty($post->ID) ? rand() : $post->ID);
	}


	/* Create After form area */
	ob_start();
	dynamic_sidebar('widgetized-after-password-form');
	$after_area = ob_get_contents();
	ob_end_clean();

	if (false == $cpp_use_default_form) {
		$output = $before_area . '<p class="protected-intro-text">' . $cpp_intro . '</p>' . '<form class="cpp-form" action="' . esc_attr(site_url('wp-login.php?action=postpass', 'login_post')) . '" class="post-password-form" method="post">
		' . '<label class="cpp-label" for="' . esc_attr__($label_selector) . '">' . $cpp_label . ' </label><input class="cpp-password" name="post_password" id="' . $label_selector . '" type="password" size="20" maxlength="20" /><input class="cpp-submit" style="' . esc_attr__($cpp_button_padding) . '" type="submit" name="Submit" value="' . esc_attr__($cpp_button_text) . '" /><div style="clear:both;"></div></form>' . $after_area;
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

function customize_pp_plugin_admin_review_notice()
{
	global $pagenow;
	if ($pagenow == 'index.php' || $pagenow == 'edit.php') {
		$user = wp_get_current_user();
		if (in_array('Administrator', (array) $user->roles) || in_array('Super Administrator', (array) $user->roles)) {
			echo '<div class="notice notice-info is-dismissible">
			  <p>Find Customize Private & Protected helpful? Give it a 5-star rating on WordPress</p>
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

/**
 * Register and enqueue plugin styles
 */
function customize_pp_plugin_customizer_live_preview()
{
	wp_enqueue_script(
		'cpp-customizer-preview',
		plugins_url('/js/cpp-customizer-preview.js', __FILE__),
		array('jquery'),
		'1.0',
		true
	);

	// Get data that you want to pass to your JavaScript
	$prefixProtected = get_option('cpp_prefix_protected', '');
	$prefixPrivate = get_option('cpp_prefix_private', '');

	// Create an array with the data
	$localized_data = array(
		'prefixProtected' => $prefixProtected,
		'prefixPrivate' => $prefixPrivate,
	);

	// Use wp_localize_script() to make the data available to your JavaScript
	wp_localize_script('cpp-customizer-preview', 'pageData', $localized_data);

}
add_action('customize_preview_init', 'customize_pp_plugin_customizer_live_preview');

function customize_pp_plugin_body_class($classes)
{
	if (post_password_required()) {
		$classes[] = 'is-protected';
	}

	if (get_post_status() == 'private') {
		$classes[] = 'is-private';
	}

	return $classes;
}
add_filter('body_class', 'customize_pp_plugin_body_class');