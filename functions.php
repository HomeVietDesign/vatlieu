<?php
define('THEME_DIR', get_stylesheet_directory());
define('THEME_URI', get_stylesheet_directory_uri());

require_once THEME_DIR.'/inc/trait-singleton.php';
require_once THEME_DIR.'/inc/unyson/class-unyson.php';
//require_once THEME_DIR.'/inc/duplicate-post/class-duplicate-post.php';
require_once THEME_DIR.'/inc/custom-taxonomy-order-ne/class-custom-taxonomy-order-ne.php';

require_once THEME_DIR.'/inc/class-authentication.php';
require_once THEME_DIR.'/inc/global-functions.php';
require_once THEME_DIR.'/inc/class-theme.php';

\HomeViet\Theme::get_instance();