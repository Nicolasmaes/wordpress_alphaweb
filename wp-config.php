<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en « wp-config.php » et remplir les
 * valeurs.
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * ABSPATH
 *
 * @link https://fr.wordpress.org/support/article/editing-wp-config-php/.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'db_alphaweb' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', '' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Type de collation de la base de données.
 * N’y touchez que si vous savez ce que vous faites.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clés secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'mATNpGexku1ctHR]WJdH}yS41_8T}=kB.GQ+!JNaDqcUIb,4<P{]my+KVVll(5c)' );
define( 'SECURE_AUTH_KEY',  'e?F&fN>T64kp?~GkwE~TvS!fR!4mjNrU@<fXvDt-zB/L5+7*x>es{`(Tv2u{ ehQ' );
define( 'LOGGED_IN_KEY',    'VSc*E9l-D,U!-G{7/um86HoQUQ4{W?R|DRmxN=iRTvcTw*QCf[Y,ei7heq<BrGLY' );
define( 'NONCE_KEY',        'fcqTLS35hKMeXo~S.j4u2}.$m!]),S6Esv3M>3RmyS0R*hvawI1(X5lat+eeZX2_' );
define( 'AUTH_SALT',        ',@q`7K/E:YnU%n3,tK>}ZJ:M(sEk`ZaY!U]C n6QE*w%x5IA(*v7>/7/Ac6:K1Qy' );
define( 'SECURE_AUTH_SALT', 'L)WH{FCF?y;X?,mfmrWz-L}#em_( ]mT:(3!c<j pa1HVVmdYGMx?.bs>]G.8T`o' );
define( 'LOGGED_IN_SALT',   'iF>e;?Y:~|}SJ}MhTz{I%%8>33l!a<1$rnaHfxt.p Q]-S4,yy{#<J%y/PMb?l>`' );
define( 'NONCE_SALT',       'b`b5# 4{.qrvphRej@tjAC3j>byzHUJVhMv3OmP+l1{VKKPE7C-~Ah~dVt4-JK(Y' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'aw_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortement recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://fr.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );
