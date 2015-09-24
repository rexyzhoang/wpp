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
define('AUTH_KEY',         ')HXj9tUlTz|pWynB%Q2nAyd~mt`zO|#g#--z%eVqnCU$?m[u~k2AHD>PIx/v>g1]');
define('SECURE_AUTH_KEY',  'jJ1z,YDP^-u{9{=}$:A-ZG01$t^m2wnq{X7G64f9Vh(Jc/k=:B?D|+Iizq{_DI)/');
define('LOGGED_IN_KEY',    'FSf61h/zz4{p]tY,CN,~8nr5YxjlvL=`Oz[1o5+|ZE8b[&7?K}T+rw+iW@B| !os');
define('NONCE_KEY',        '87w1`fR:>fI75gOG{pl?V.xi:_9.=Wj-Yi7f&l9,s[XnmyF;/36KN!89^l3A,x5F');
define('AUTH_SALT',        'Mt={t:u!n/oRqVJvCR`1LkZ>)4wJbi-L6#-+A6lfGbC.ya@2dvWbH/8r-Oi|@tuG');
define('SECURE_AUTH_SALT', '`QS%agKp.&f`BX685G#`Tv+If?U@;/Po7<l$,mnG8+}|vI-5M<1+Vq})vr]yGnb~');
define('LOGGED_IN_SALT',   'bt%NU}bB8YXN7FP.SP++4;a*i@pY[Ot~_fOK-T-?a3;mx 3^%Q6kfT s `|Aa[T:');
define('NONCE_SALT',       'QX{i];U-0/=2S+;GEc73)MaPjcxfk-oDD`Nkht9z-S.&6>zJZJ!Lw^LQ,h+p-|$x');

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