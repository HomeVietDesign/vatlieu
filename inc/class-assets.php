<?php
namespace HomeViet;

class Assets {

	public static function enqueue_styles() {
		wp_dequeue_style( 'font-awesome' );
		wp_register_style( 'jquery-ui-theme', 'https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css', [], '1.14.1' );
		wp_register_style( 'bootstrap', THEME_URI.'/libs/bootstrap/css/bootstrap.min.css', [], '5.1.3' );
		wp_register_style( 'owlcarousel', THEME_URI.'/libs/owlcarousel/assets/owl.carousel.min.css', [], '2.3.4' );
		wp_register_style( 'select2', THEME_URI.'/libs/select2/dist/css/select2.min.css', [], '4.0.13' );
		wp_register_style( 'photoswipe', THEME_URI.'/libs/PhotoSwipe/photoswipe.css', [], '5.4.3' );
		
		$deps = [
			'bootstrap',
			'jquery-ui-theme',
			'dashicons',
			'photoswipe',
			'select2',
			//'owlcarousel'
		];
		
		wp_enqueue_style( 'texture', THEME_URI.'/assets/css/main.css', $deps, date('YmdHis', filemtime(THEME_DIR . '/assets/css/main.css')) );
		wp_enqueue_style( 'texture-upload', THEME_URI.'/assets/css/texture-upload.css', ['texture'], date('YmdHis', filemtime(THEME_DIR . '/assets/css/texture-upload.css')) );
	}

	public static function enqueue_scripts() {
		global $theme_setting;
		// wp_scripts()->add_data( 'jquery', 'group', 1 );
		// wp_scripts()->add_data( 'jquery-core', 'group', 1 );
		// wp_scripts()->add_data( 'jquery-migrate', 'group', 1 );

		wp_dequeue_style( 'wp-block-library' );
	    wp_dequeue_style( 'wp-block-library-theme' );
	    wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS

	    //wp_enqueue_script('lodash');

		wp_register_script( 'bootstrap', THEME_URI.'/libs/bootstrap/js/bootstrap.bundle.min.js', ['jquery'], '5.1.3', true);

		wp_register_script( 'owlcarousel', THEME_URI.'/libs/owlcarousel/owl.carousel.min.js', ['jquery'], '2.3.4', true);
		wp_register_script( 'select2', THEME_URI.'/libs/select2/dist/js/select2.full.min.js', ['jquery'], '4.0.13', true);
		wp_register_script( 'photoswipe', THEME_URI.'/libs/PhotoSwipe/photoswipe.umd.min.js', ['jquery'], '5.4.3', true);
		wp_register_script( 'photoswipe-lightbox', THEME_URI.'/libs/PhotoSwipe/photoswipe-lightbox.umd.min.js', ['photoswipe'], '5.4.3', true);

		$deps = [
			'jquery',
			'jquery-ui-sortable',
			'bootstrap',
			'photoswipe-lightbox',
			'select2',
			//'masonry',
			//'owlcarousel',
			//'lodash',
		];

		wp_enqueue_script( 'texture', THEME_URI.'/assets/js/main.js', $deps, date('YmdHis', filemtime(THEME_DIR . '/assets/js/main.js')), true);
		wp_enqueue_script( 'texture-upload', THEME_URI.'/assets/js/texture-upload.js', ['texture'], date('YmdHis', filemtime(THEME_DIR . '/assets/js/texture-upload.js')), true);

		$data = [
			'home_url'=>esc_url(home_url()), 
			'ajax_url'=>esc_url(admin_url('admin-ajax.php')),
			'cf_turnstile_key'=>$theme_setting->get('cf_turnstile_key'),
			'is_user_logged_in' => (is_user_logged_in())?1:0,
			'nonce' => wp_create_nonce( 'global' )
		];

		wp_localize_script( 'jquery', 'theme', $data );
		wp_add_inline_script( 'jquery-core', self::get_inline_scripts(), 'before' );

	}

	public static function get_inline_scripts() {
		ob_start();
		?>
		<script type="text/javascript">
			
			function isValidEmailAddress( emailAddress ) {
				if ( '' === emailAddress ) {
					return false;
				}

				return /[0-9a-zA-Z][a-zA-Z\+0-9\.\_\-]*@[0-9a-zA-Z\-]+(\.[a-zA-Z]{2,24}){1,3}/.test( emailAddress );
			}

			function isValidUrl(urlString) {
				let httpRegex = /^https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&\/=]*)$/;
				return httpRegex.test(urlString);
			}

			function is_mobile() {
				if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
					return true;
				}
				return false;
			}

			function setCookie(cname, cvalue, exdays) {
				const d = new Date();
				d.setTime(d.getTime() + (exdays*24*60*60*1000));
				let expires = "expires="+ d.toUTCString();
				document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
			}

			function getCookie(cname) {
				let name = cname + "=";
				let decodedCookie = decodeURIComponent(document.cookie);
				let ca = decodedCookie.split(';');
				for(let i = 0; i <ca.length; i++) {
					let c = ca[i];
					while (c.charAt(0) == ' ') {
						c = c.substring(1);
					}
					if (c.indexOf(name) == 0) {
						return c.substring(name.length, c.length);
					}
				}
				return "";
			}

			function deleteCookie(name) {
				document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
			}

			function add_query_url(key,value,url) {
				const ourl = new URL(url);
				ourl.searchParams.delete(key);
				ourl.searchParams.set(key, value);
				return ourl.toString();
			}

			function debounce(func, wait = 250) { // Default wait time of 250ms
				let timeout;
				return function executedFunction(...args) {
					clearTimeout(timeout); // Clear any previous timeout
					timeout = setTimeout(() => func.apply(this, args), wait);
				};
			}

			function throttle(func, wait = 250) {
				let isWaiting = false;
				return function executedFunction(...args) {
					if (!isWaiting) {
						func.apply(this, args);
						isWaiting = true;
						setTimeout(() => {
							isWaiting = false;
						}, wait);
					}
				};
			}

			async function CopyToClipboard(text) {
				try {
					await navigator.clipboard.writeText(text);
					//console.log('Content copied to clipboard');
					return true
				} catch (err) {
					//console.error('Failed to copy: ', err);
					return false;
				}
			}

			function unserialize(str) {
				const params = new URLSearchParams(str);
				let obj = {};

				for (const [key, value] of params) {
					// Nếu là name dạng array, ví dụ color[]
					const isArray = key.endsWith("[]");
					const cleanKey = isArray ? key.slice(0, -2) : key;

					if (isArray) {
						if (!obj[cleanKey]) obj[cleanKey] = [];
						obj[cleanKey].push(value);
					} else {
						if (obj[cleanKey] !== undefined) {
							// Nếu trùng key, convert thành mảng
							if (!Array.isArray(obj[cleanKey])) {
								obj[cleanKey] = [obj[cleanKey]];
							}
							obj[cleanKey].push(value);
						} else {
							obj[cleanKey] = value;
						}
					}
				}

				return obj;
			}
			/*
			let ref = getCookie('_ref');
			if(ref=='') {
				ref = window.btoa((document.referrer=='')?window.location.href:document.referrer);
				setCookie('_ref', ref, 1);
			} else {
				let aref = window.atob(ref).split(',');
				if(document.referrer!=aref[aref.length-1]) {
					ref = window.btoa(window.atob(ref)+','+document.referrer);
				}
				setCookie('_ref', ref, 1);
			}
			*/
		</script>
		<?php
		return trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', ob_get_clean() ) );
	}

	public static function enqueue_page_builder_scripts_for_tax() {
		wp_enqueue_style('fw-ext-builder-frontend-grid');
		$shortcodes_extension = fw_ext( 'shortcodes' );
		foreach($shortcodes_extension->get_shortcodes() as $shortcode) {
			$shortcode->_enqueue_static();
		}

	}
}