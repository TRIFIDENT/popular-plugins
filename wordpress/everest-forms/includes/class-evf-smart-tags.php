<?php
/**
 * Smart tag functionality.
 *
 * @package EverestForms\Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Smart Tag Class.
 */
class EVF_Smart_Tags {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'everest_forms_process_smart_tags', array( $this, 'process' ), 10, 4 );
	}

	/**
	 * Other smart tags.
	 *
	 * @return string|array
	 */
	public function other_smart_tags() {
		$smart_tags = apply_filters(
			'everest_forms_smart_tags',
			array(
				'current_date'           => esc_html__( 'Current Date', 'everest-forms' ),
				'current_time'           => esc_html__( 'Current Time', 'everest-forms' ),
				'admin_email'            => esc_html__( 'Site Admin Email', 'everest-forms' ),
				'site_name'              => esc_html__( 'Site Name', 'everest-forms' ),
				'site_url'               => esc_html__( 'Site URL', 'everest-forms' ),
				'page_title'             => esc_html__( 'Page Title', 'everest-forms' ),
				'page_url'               => esc_html__( 'Page URL', 'everest-forms' ),
				'page_id'                => esc_html__( 'Page ID', 'everest-forms' ),
				'post_title'             => esc_html__( 'Post Title', 'everest-forms' ),
				'post_meta key=whatever' => esc_html__( 'Post Meta', 'everest-forms' ),
				'author_email'           => esc_html__( 'Author Email', 'everest-forms' ),
				'author_name'            => esc_html__( 'Author Name', 'everest-forms' ),
				'form_name'              => esc_html__( 'Form Name', 'everest-forms' ),
				'user_ip_address'        => esc_html__( 'User IP Address', 'everest-forms' ),
				'user_id'                => esc_html__( 'User ID', 'everest-forms' ),
				'user_meta key=whatever' => esc_html__( 'User Meta', 'everest-forms' ),
				'user_name'              => esc_html__( 'User Name', 'everest-forms' ),
				'display_name'           => esc_html__( 'User Display Name', 'everest-forms' ),
				'first_name'             => esc_html__( 'First Name', 'everest-forms' ),
				'last_name'              => esc_html__( 'Last Name', 'everest-forms' ),
				'user_email'             => esc_html__( 'User Email', 'everest-forms' ),
				'user_role'              => esc_html__( 'User Role', 'everest-forms' ),
				'referrer_url'           => esc_html__( 'Referrer URL', 'everest-forms' ),
				'form_id'                => esc_html__( 'Form ID', 'everest-forms' ),
			)
		);

		return $smart_tags;
	}

	/**
	 * Process and parse smart tags.
	 *
	 * @param string       $content The string to preprocess.
	 * @param array        $form_data Array of the form data.
	 * @param string|array $fields Form fields.
	 * @param int|string   $entry_id Entry ID.
	 *
	 * @return string
	 */
	public function process( $content, $form_data, $fields = '', $entry_id = '' ) {
		// Field smart tags (settings, etc).
		preg_match_all( '/\{field_id="(.+?)"\}/', $content, $ids );

		// We can only process field smart tags if we have $fields.
		if ( ! empty( $ids[1] ) && ! empty( $fields ) ) {

			foreach ( $ids[1] as $key => $field_id ) {
				$mixed_field_id = explode( '_', $field_id );
				$uploads        = wp_upload_dir();

				if ( 'fullname' !== $field_id && 'email' !== $field_id && 'subject' !== $field_id && 'message' !== $field_id ) {
					$value = isset( $fields[ $mixed_field_id[1] ]['value'] ) && ! empty( $fields[ $mixed_field_id[1] ]['value'] ) ? evf_sanitize_textarea_field( $fields[ $mixed_field_id[1] ]['value'] ) : '';
				} else {
					$value = isset( $fields[ $field_id ]['value'] ) && ! empty( $fields[ $field_id ]['value'] ) ? evf_sanitize_textarea_field( $fields[ $field_id ]['value'] ) : '';
				}

				$value = apply_filters( 'everest_forms_smart_tags_value', $value, $field_id, $fields, $form_data );

				if ( count( $mixed_field_id ) > 1 && ! empty( $fields[ $mixed_field_id[1] ] ) ) {
					// Properly display signature field in smart tag.
					if ( 'signature' === $fields[ $mixed_field_id[1] ]['type'] ) {
						if ( ! is_array( $value ) && false !== strpos( $value, $uploads['basedir'] ) ) {
							$value = trailingslashit( content_url() ) . str_replace( str_replace( 'uploads', '', $uploads['basedir'] ), '', $value );
						}

						if ( ! empty( $value ) ) {
							$value = sprintf(
								'<img src="%s" style="width:150px;height:80px;max-height:200px;max-width:100px;"/>',
								$value
							);
						}
					}

					// Properly display Radio field in smart tag.
					if ( isset( $value['image'] ) && 'radio' === $fields[ $mixed_field_id[1] ]['type'] ) {
						if ( ! is_array( $value ) && false !== strpos( $value['image'], $uploads['basedir'] ) ) {
							$value = trailingslashit( content_url() ) . str_replace( str_replace( 'uploads', '', $uploads['basedir'] ), '', $value['image'] );
						}

						if ( ! empty( $value ) ) {
							$value = sprintf(
								"\n" . '<img src="%s" style="width:150px;height:80px;max-height:200px;max-width:100px;"/>' . "\n" . '%s',
								$value['image'],
								$value['label']
							);
						}
					}

					// Properly display Checkboxes field in smart tag.
					if ( isset( $value['images'] ) && ( 'checkbox' === $fields[ $mixed_field_id[1] ]['type'] ) ) {
						$checkbox_images = '';
						foreach ( $value['images'] as $image_key => $image_value ) {
							if ( ! is_array( $image_value ) && false !== strpos( $image_value, $uploads['basedir'] ) ) {
								$value = trailingslashit( content_url() ) . str_replace( str_replace( 'uploads', '', $uploads['basedir'] ), '', $image_value );
							}

							if ( ! empty( $value ) ) {
								$checkbox_images .= sprintf(
									"\n" . '<img src="%s" style="width:150px;height:80px;max-height:200px;max-width:100px;"/>' . "\n" . '%s',
									$image_value,
									$value['label'][ $image_key ]
								);
							}
						}
						$value = $checkbox_images;
					}

					// Properly display Files and Image Upload field in smart tag.
					if ( 'image-upload' === $fields[ $mixed_field_id[1] ]['type'] || 'file-upload' === $fields[ $mixed_field_id[1] ]['type'] ) {
						$files = '';

						if ( ! empty( $fields[ $mixed_field_id[1] ]['value_raw'] ) ) {
							foreach ( $fields[ $mixed_field_id[1] ]['value_raw'] as $files_key => $files_value ) {
								if ( ! is_array( $files_value['value'] ) && false !== strpos( $files_value['value'], $uploads['basedir'] ) ) {
									$value = trailingslashit( content_url() ) . str_replace( str_replace( 'uploads', '', $uploads['basedir'] ), '', $files_value['value'] );
								}

								if ( ! empty( $value ) ) {
									$files .= sprintf(
										'<a href="%s">%s</a> ' . "\n",
										$files_value['value'],
										$files_value['name']
									);
								}
							}
							$value = $files;
						}
					}
				}

				if ( ! is_array( $value ) ) {
					$content = str_replace( '{field_id="' . $field_id . '"}', $value, $content );
				} else {
					if ( isset( $value['type'], $value['label'] ) ) {
						if ( in_array( $value['type'], array( 'radio', 'payment-multiple' ), true ) ) {
							$value = $value['label'];
						} elseif ( in_array( $value['type'], array( 'checkbox', 'payment-checkbox' ), true ) ) {
							$value = implode( ',', $value['label'] );

						}
					} elseif ( isset( $value['number_of_rating'], $value['value'] ) ) {
						$value = (string) $value['value'] . '/' . (string) $value['number_of_rating'];
					} else {
						$value = $value[0];
					}

					$content = str_replace( '{field_id="' . $field_id . '"}', $value, $content );
				}
			}
		}

		// Other Smart tags.
		preg_match_all( '/\{(.+?)\}/', $content, $other_tags );

		if ( ! empty( $other_tags[1] ) ) {

			foreach ( $other_tags[1] as $key => $tag ) {
				$other_tag = explode( ' ', $tag )[0];
				switch ( $other_tag ) {
					case 'admin_email':
						$admin_email = sanitize_email( get_option( 'admin_email' ) );
						$content     = str_replace( '{' . $other_tag . '}', $admin_email, $content );
						break;

					case 'site_name':
						$site_name = get_option( 'blogname' );
						$content   = str_replace( '{' . $other_tag . '}', $site_name, $content );
						break;

					case 'site_url':
						$site_url = get_option( 'siteurl' );
						$content  = str_replace( '{' . $other_tag . '}', $site_url, $content );
						break;

					case 'page_title':
						$page_title = get_the_title( get_the_ID() );
						$content    = str_replace( '{' . $other_tag . '}', $page_title, $content );
						break;

					case 'page_url':
						$page_url = get_permalink( get_the_ID() );
						$content  = str_replace( '{' . $other_tag . '}', $page_url, $content );
						break;

					case 'page_id':
						$page_id = get_the_ID();
						$content = str_replace( '{' . $other_tag . '}', $page_id, $content );
						break;

					case 'form_name':
						if ( isset( $form_data['settings']['form_title'] ) && ! empty( $form_data['settings']['form_title'] ) ) {
							$form_name = $form_data['settings']['form_title'];
						} else {
							$form_name = '';
						}
						$content = str_replace( '{' . $other_tag . '}', $form_name, $content );
						break;

					case 'user_ip_address':
						$user_ip_add = evf_get_ip_address();
						$content     = str_replace( '{' . $other_tag . '}', $user_ip_add, $content );
						break;

					case 'user_id':
						$user_id = is_user_logged_in() ? get_current_user_id() : '';
						$content = str_replace( '{' . $other_tag . '}', $user_id, $content );
						break;

					case 'user_email':
						if ( is_user_logged_in() ) {
							$user  = wp_get_current_user();
							$email = sanitize_email( $user->user_email );
						} else {
							$email = '';
						}
						$content = str_replace( '{' . $other_tag . '}', $email, $content );
						break;

					case 'user_name':
						if ( is_user_logged_in() ) {
							$user = wp_get_current_user();
							$name = sanitize_text_field( $user->user_login );
						} else {
							$name = '';
						}
						$content = str_replace( '{' . $other_tag . '}', $name, $content );
						break;

					case 'display_name':
						if ( is_user_logged_in() ) {
							$user = wp_get_current_user();
							$name = sanitize_text_field( $user->display_name );
						} else {
							$name = '';
						}
						$content = str_replace( '{' . $other_tag . '}', $name, $content );
						break;

					case 'first_name':
						if ( is_user_logged_in() ) {
							$user = wp_get_current_user();
							$name = sanitize_text_field( $user->user_firstname );
						} else {
							$name = '';
						}
						$content = str_replace( '{' . $other_tag . '}', $name, $content );
						break;

					case 'last_name':
						if ( is_user_logged_in() ) {
							$user = wp_get_current_user();
							$name = sanitize_text_field( $user->user_lastname  );
						} else {
							$name = '';
						}
						$content = str_replace( '{' . $other_tag . '}', $name, $content );
						break;

					case 'referrer_url':
						$referer = ! empty( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : ''; // @codingStandardsIgnoreLine
						$content = str_replace( '{' . $other_tag . '}', sanitize_text_field( $referer ), $content );
						break;
					case 'current_date':
						$current_date = date_i18n( get_option( 'date_format' ) );
						$content      = str_replace( '{' . $other_tag . '}', sanitize_text_field( $current_date ), $content );
						break;
					case 'current_time':
						$current_time = date_i18n( get_option( 'time_format' ) );
						$content      = str_replace( '{' . $other_tag . '}', sanitize_text_field( $current_time ), $content );
						break;
					case 'post_title':
						$post_title = get_the_title();
						$content    = str_replace( '{' . $other_tag . '}', sanitize_text_field( $post_title ), $content );
						break;
					case 'post_meta':
						preg_match_all( '/key\=(.*?)$/', $tag, $meta );
						if ( is_array( $meta ) && ! empty( $meta[1][0] ) ) {
							$key     = $meta[1][0];
							$value   = get_post_meta( get_the_ID(), $key, true );
							$content = str_replace( '{' . $tag . '}', wp_kses_post( $value ), $content );
						} else {
							$content = str_replace( '{' . $tag . '}', '', $content );
						}
						break;
					case 'author_email':
						$author  = get_the_author_meta( 'user_email' );
						$content = str_replace( '{' . $other_tag . '}', sanitize_text_field( $author ), $content );
						break;
					case 'author_name':
						$author  = get_the_author_meta( 'display_name' );
						$content = str_replace( '{' . $other_tag . '}', sanitize_text_field( $author ), $content );
						break;
					case 'user_meta':
						preg_match_all( '/key\=(.*?)$/', $tag, $meta );
						if ( is_array( $meta ) && ! empty( $meta[1][0] ) ) {
							$key     = $meta[1][0];
							$value   = get_user_meta( get_current_user_id(), $key, true );
							$content = str_replace( '{' . $tag . '}', wp_kses_post( $value ), $content );
						} else {
							$content = str_replace( '{' . $tag . '}', '', $content );
						}
						break;
					case 'form_id':
						if ( isset( $form_data['id'] ) && ! empty( $form_data['id'] ) ) {
							$form_id = $form_data['id'];
						} else {
							$form_id = '';
						}
						$content = str_replace( '{' . $other_tag . '}', $form_id, $content );
						break;
				}
			}
		}

		return $content;
	}
}
