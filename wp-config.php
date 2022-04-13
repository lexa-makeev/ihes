<?php
define( 'WP_CACHE', true ); // Added by WP Rocket

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u1263204_ihes' );

/** MySQL database username */
define( 'DB_USER', 'u1263204_ihes' );

/** MySQL database password */
define( 'DB_PASSWORD', '!IhesiheS@2198@' );

/** MySQL hostname */
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
define( 'AUTH_KEY',         'Sjg>2,O)V~WdxU%:MB4<bZc :-3=;BJ=iZoo[8Tynz/KS!IenVE)WhP1Q:3@9 D2' );
define( 'SECURE_AUTH_KEY',  '(i=xQhCKsRd{)$$eq):7dM=,<mU4Hd9UrMh%gvj@6X!p|bf7!Q4N:Iy:u]P0u:AE' );
define( 'LOGGED_IN_KEY',    'X9SDZ6^BI v+ajjt}x+vg nj^o&CEO%bl8A?T&5J#o:wH2S(AeCkcaiz0KX5|A0z' );
define( 'NONCE_KEY',        'UAr[g)an(W rS&Q[-S>&7&6>Ew) &?Q7LDW>hsdiRkAxAj2 E437d%Q^;y|lry$}' );
define( 'AUTH_SALT',        '6a;SJ}!YTc=>Hl@HnTq2$I)FCM?s)?ULEqiD45`)Me%8&N!jx [o{D7JErFf;ReV' );
define( 'SECURE_AUTH_SALT', 'O37_/Oagn-J2 WO22dPRQ-8?y=u}?jdF[dQporgBCeG-7sU2ds`D9]6NhW^[{At%' );
define( 'LOGGED_IN_SALT',   '{/v5Edox!/h(M`NWj(z%#Ik@oLR%GiVk5Wm<J`7xV0[.^U.]zjgIlik1+>,}*lY;' );
define( 'NONCE_SALT',       'mZDs2o%g}5T6bUJRX7p<4BawWR[x-S*H/]@.K},}8gT(/<Y=i!%dX,Q#P{~DRrb%' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'bbr_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
