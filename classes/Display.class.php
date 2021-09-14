<?php

/**
 * The class that is responsible for all the displays.
 */
class PostExpirator_Display {

	/**
	 * The singleton instance.
	 */
	private static $_instance = null;

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->hooks();
	}

	/**
	 * Returns instance of the singleton.
	 */
	public static function getInstance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Initialize the hooks.
	 */
	private function hooks() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
	}

	/**
	 * Add plugin page menu.
	 */
	function add_menu() {
		add_submenu_page( 'options-general.php', __( 'Post Expirator Options', 'post-expirator' ), __( 'Post Expirator', 'post-expirator' ), 'manage_options', POSTEXPIRATOR_BASENAME, array( self::$_instance, 'settings_tabs' ) );
	}

	/**
	 * Loads the specified tab.
	 *
	 * Make sure the name of the file is menu_{$tab}.php.
	 */
	public function load_tab( $tab ) {
		$function = 'menu_' . $tab;
		$this->$function();
	}

	/**
	 * Creates the settings page.
	 */
	public function settings_tabs() {
		PostExpirator_Facade::load_assets( 'settings' );

		$allowed_tabs = array( 'general', 'defaults', 'display', 'editor', 'diagnostics', 'viewdebug' );
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';
		if ( empty( $tab ) || ! in_array( $tab, $allowed_tabs, true ) ) {
			$tab = 'general';
		}

		ob_start();
		$this->load_tab( $tab );
		$html = ob_get_clean();

		$debug = postexpirator_debug(); // check for/load debug

		if ( ! POSTEXPIRATOR_DEBUG ) {
			unset( $allowed_tabs['viewdebug'] );
		}

		$this->render_template( 'tabs', array( 'tabs' => $allowed_tabs, 'html' => $html, 'tab' => $tab ) );

	}

	/**
	 * Editor menu.
	 */
	private function menu_editor() {
		if ( isset( $_POST['expirationdateSaveEditor'] ) && $_POST['expirationdateSaveEditor'] ) {
			if ( ! isset( $_POST['_postExpiratorMenuEditor_nonce'] ) || ! wp_verify_nonce( $_POST['_postExpiratorMenuEditor_nonce'], 'postexpirator_menu_editor' ) ) {
				print 'Form Validation Failure: Sorry, your nonce did not verify.';
				exit;
			} else {
				// Filter Content
				$_POST = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
				update_option( 'expirationdateGutenbergSupport', $_POST['gutenberg-support'] );
			}
		}

		$this->render_template( 'menu-editor' );

	}

	/**
	 * Display menu.
	 */
	private function menu_display() {
		if ( isset( $_POST['expirationdateSaveDisplay'] ) && $_POST['expirationdateSaveDisplay'] ) {
			if ( ! isset( $_POST['_postExpiratorMenuDisplay_nonce'] ) || ! wp_verify_nonce( $_POST['_postExpiratorMenuDisplay_nonce'], 'postexpirator_menu_display' ) ) {
				print 'Form Validation Failure: Sorry, your nonce did not verify.';
				exit;
			} else {
				// Filter Content
				$_POST = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

				update_option( 'expirationdateDisplayFooter', $_POST['expired-display-footer'] );
				update_option( 'expirationdateFooterContents', $_POST['expired-footer-contents'] );
				update_option( 'expirationdateFooterStyle', $_POST['expired-footer-style'] );
			}
		}

		$this->render_template( 'menu-display' );
	}

	/**
	 * Diagnostics menu.
	 */
	private function menu_diagnostics() {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			if ( ! isset( $_POST['_postExpiratorMenuDiagnostics_nonce'] ) || ! wp_verify_nonce( $_POST['_postExpiratorMenuDiagnostics_nonce'], 'postexpirator_menu_diagnostics' ) ) {
				print 'Form Validation Failure: Sorry, your nonce did not verify.';
				exit;
			}
			if ( isset( $_POST['debugging-disable'] ) ) {
				update_option( 'expirationdateDebug', 0 );
						echo "<div id='message' class='updated fade'><p>";
				_e( 'Debugging Disabled', 'post-expirator' );
				echo '</p></div>';
			} elseif ( isset( $_POST['debugging-enable'] ) ) {
				update_option( 'expirationdateDebug', 1 );
						echo "<div id='message' class='updated fade'><p>";
				_e( 'Debugging Enabled', 'post-expirator' );
				echo '</p></div>';
			} elseif ( isset( $_POST['purge-debug'] ) ) {
				require_once( plugin_dir_path( __FILE__ ) . 'post-expirator-debug.php' );
				$debug = new PostExpiratorDebug();
				$debug->purge();
						echo "<div id='message' class='updated fade'><p>";
				_e( 'Debugging Table Emptied', 'post-expirator' );
				echo '</p></div>';
			}
		}

		$debug = postexpirator_debug();

		$this->render_template( 'menu-diagnostics' );
	}

	/**
	 * Debug menu.
	 */
	private function menu_viewdebug() {
		require_once POSTEXPIRATOR_BASEDIR . '/post-expirator-debug.php';
		print '<p>' . __( 'Below is a dump of the debugging table, this should be useful for troubleshooting.', 'post-expirator' ) . '</p>';
		$debug = new PostExpiratorDebug();
		$debug->getTable();
	}

	/**
	 * The default menu.
	 */
	private function menu_defaults() {
		$debug = postexpirator_debug();
		$types = postexpirator_get_post_types();
		$defaults = array();

		if ( isset( $_POST['expirationdateSaveDefaults'] ) ) {
			if ( ! isset( $_POST['_postExpiratorMenuDefaults_nonce'] ) || ! wp_verify_nonce( $_POST['_postExpiratorMenuDefaults_nonce'], 'postexpirator_menu_defaults' ) ) {
				print 'Form Validation Failure: Sorry, your nonce did not verify.';
				exit;
			} else {
				// Filter Content
				$_POST = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

				foreach ( $types as $type ) {
					if ( isset( $_POST[ 'expirationdate_expiretype-' . $type ] ) ) {
						$defaults[ $type ]['expireType'] = $_POST[ 'expirationdate_expiretype-' . $type ];
					}
					if ( isset( $_POST[ 'expirationdate_autoenable-' . $type ] ) ) {
						$defaults[ $type ]['autoEnable'] = intval( $_POST[ 'expirationdate_autoenable-' . $type ] );
					}
					if ( isset( $_POST[ 'expirationdate_taxonomy-' . $type ] ) ) {
						$defaults[ $type ]['taxonomy'] = $_POST[ 'expirationdate_taxonomy-' . $type ];
					}
					if ( isset( $_POST[ 'expirationdate_activemeta-' . $type ] ) ) {
						$defaults[ $type ]['activeMetaBox'] = $_POST[ 'expirationdate_activemeta-' . $type ];
					}
					$defaults[ $type ]['emailnotification'] = trim( $_POST[ 'expirationdate_emailnotification-' . $type ] );

					if ( isset( $_POST[ 'expired-default-date-' . $type ] ) ) {
						$defaults[ $type ]['default-expire-type'] = $_POST[ 'expired-default-date-' . $type ];
					}
					if ( isset( $_POST[ 'expired-custom-date-' . $type ] ) ) {
						$defaults[ $type ]['default-custom-date'] = $_POST[ 'expired-custom-date-' . $type ];
					}

					// Save Settings
					update_option( 'expirationdateDefaults' . ucfirst( $type ), $defaults[ $type ] );
				}
				echo "<div id='message' class='updated fade'><p>";
				_e( 'Saved Options!', 'post-expirator' );
				echo '</p></div>';
			}
		}

		$this->render_template( 'menu-defaults', array( 'types' => $types, 'defaults' => $defaults ) );
	}

	/**
	 * Show the Expiration Date options page
	 */
	private function menu_general() {
		if ( isset( $_POST['expirationdateSave'] ) && $_POST['expirationdateSave'] ) {
			if ( ! isset( $_POST['_postExpiratorMenuGeneral_nonce'] ) || ! wp_verify_nonce( $_POST['_postExpiratorMenuGeneral_nonce'], 'postexpirator_menu_general' ) ) {
				print 'Form Validation Failure: Sorry, your nonce did not verify.';
				exit;
			} else {
				// Filter Content
				$_POST = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

				update_option( 'expirationdateDefaultDateFormat', $_POST['expired-default-date-format'] );
				update_option( 'expirationdateDefaultTimeFormat', $_POST['expired-default-time-format'] );
				update_option( 'expirationdateEmailNotification', $_POST['expired-email-notification'] );
				update_option( 'expirationdateEmailNotificationAdmins', $_POST['expired-email-notification-admins'] );
				update_option( 'expirationdateEmailNotificationList', trim( $_POST['expired-email-notification-list'] ) );
				update_option( 'expirationdateCategoryDefaults', isset( $_POST['expirationdate_category'] ) ? $_POST['expirationdate_category'] : array() );
				update_option( 'expirationdateDefaultDate', $_POST['expired-default-expiration-date'] );
				if ( $_POST['expired-custom-expiration-date'] ) {
					update_option( 'expirationdateDefaultDateCustom', $_POST['expired-custom-expiration-date'] );
				}
						echo "<div id='message' class='updated fade'><p>";
						_e( 'Saved Options!', 'post-expirator' );
						echo '</p></div>';
			}
		}

		$this->render_template( 'menu-general' );
	}

	/**
	 * Renders a named template, if it is found.
	 */
	public function render_template( $name, $params = null ) {
		$template = POSTEXPIRATOR_BASEDIR . "/views/{$name}.php";
		if ( file_exists( $template ) ) {
			// expand all parameters so that they can be directly accessed with their name.
			if ( $params ) {
				foreach ( $params as $param => $value ) {
					$$param = $value;
				}
			}
			include $template;
		}
	}

}
