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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'socialme_tuffstuff_db' );

/** MySQL database username */
define( 'DB_USER', 'socialme_tstuff' );

/** MySQL database password */
define( 'DB_PASSWORD', '_kJ.N~Fii7LO' );

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'O:_/e-P,X[j&].`{|(wb7IS]xMFsHEkSip L^!k|}Y^YF.lRNXEU-]*D4=-qE7:w');
define('SECURE_AUTH_KEY',  '@2oM_|FSppJNPF|ZQUqu[(2`RZ]mF.f3_d>>m_@TDU&a.ZFa(ln+F+?*Fu^.,My4');
define('LOGGED_IN_KEY',    'F0xsswu19L92CLQp|+dcu}Q4jBI+&|AT:8B*{HobE3cv]r6}S_?9XNS*>XQ>nh9m');
define('NONCE_KEY',        'NqxxLmn$s(W`i}=4+,0[4q0}3#%jD-AIx7x]= 8y[XF79O&U^Xv~e<W/8@rZqHfp');
define('AUTH_SALT',        'dCh{`<j0C.WY}LK6+0b@oqfd%twkbTbeN.LN+r;+j5&D#epIae<MP;^j,8);xr54');
define('SECURE_AUTH_SALT', 'IhYr>d|<2:)f4W7t]VhsAxguaYkf?)Vh;9y24tMu?0c.~ajBF~)Fgkv~r|2C^x>y');
define('LOGGED_IN_SALT',   'K[i0u<Tb2R{UY)S-LT_,#W-s^4vqA!UX+0Vt86eP83qUJqDt8sQD(@,E-AXJWZ-L');
define('NONCE_SALT',       'xGr+9TGH#<28}-uLpwJ/C[cf:/yDW.P0FEq&gD,Ltj_zt{6Oy|jo=V;gnuj^&gjX');


/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'eovKMEuV_';


/* user defines goes here
 * e.g.: define('WP_DEBUG', true);
 */

/* don't remove the line below. Repeat, *DON'T* remove the line below! */
require_once( __DIR__ . "/wp-config-pressidium.php" );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
