<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '##TP(8|W+_YuGhOwWJ~/5-(~Bea9{,$W`S2{fa@h]jaO/LS)4a!G-jLC-+/6Wz+{');
define('SECURE_AUTH_KEY',  'IkG0>JwH<wfGHNRxYRidOJck2IvzY-Z!:e1EWgVO+LiFX%)B)1T+**+IH~Eya8ut');
define('LOGGED_IN_KEY',    '1ZmAd!WMLISmzmHr>|/qMYE!?:4Aqa_i+lXVG/+9vH_~{%O2u4Q+IbT6<gt+Z~IE');
define('NONCE_KEY',        '*:.|{H8nRK]Fn#xk[7#oKc@^|pd,[-0F86IBoY8z.k||Ym,q~(VtxO+-L>Ay63g ');
define('AUTH_SALT',        'qh5O.3T(R5}%U1!T66%,|mA|07A&,NoKIMFL4J7iW6nGn:`&4~wnSlLTdo2Ao.rj');
define('SECURE_AUTH_SALT', 'c8Wi0M|-89TB(-#}@IC--U>i+DVmy@WQl][K=</<4|^7sLT-B^*)r3iYH%ZCnd$x');
define('LOGGED_IN_SALT',   ']+Mo2UD=|m8v@#k~n#}H.ef?:&|[7e-N0fDD&Y#KKyt~N-rxZ9EYt9Z#+T{E56X ');
define('NONCE_SALT',       'j;%LoM[S/iHg@v:&r3Q?aqnV|GHkA(I4qI(:|TD5*-+<#jWt|A|Czi+OsV+&,,^x');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
