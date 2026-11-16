<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'nh_<ln=p/Ei,V%g8x$VBGONB9E_6d.;:I6t}Q@{Lo?@jVeVim&ORd{fM?3+lL?|:' );
define( 'SECURE_AUTH_KEY',  '$v:sWNlOu]B>vlB:6}QY TZKi7$wE{hJ(aqGEvvmr,~;)&604od*qW:j=oXngX4r' );
define( 'LOGGED_IN_KEY',    'MWo<`w<[1 El^!nUnrt9C+F&#Xk rb5.(,8eG?Z21f{^C^XmcDS-%VD3q,=J79*/' );
define( 'NONCE_KEY',        'yQZCoQE]BC^P%r[v]rID*GV+3jlYFF[%@+5Tj4Ks_@DAVTjcRWz]x,)LI1A#}*oY' );
define( 'AUTH_SALT',        'u?[b8B9Uc0:[AwO(S3!x_t8f7P4Er(;]Go+]|!Dp/J`HZfvEWUGPr|<g2%a2:Ils' );
define( 'SECURE_AUTH_SALT', 'GJ@@uJVQ<bi0KHP9|;w?@h:u*T6s7yLB=mUIE].Yv]x+yY!QehYsyJs4?ErN~6#^' );
define( 'LOGGED_IN_SALT',   '0v>|TbvTLx**.VW8Ag?S4&JXn%T:,dM <!0`=Nug%:RT^>0ytsi5w>K#XIG#TpvN' );
define( 'NONCE_SALT',       'j(Sd!9k[pQCUr|vQllci%(gauonHz.SsH=zjC]0&A6 q66)GFKJ7=Lh}bq:BB=*x' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */


define( 'FS_METHOD', 'direct' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
