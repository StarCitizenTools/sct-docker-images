<?php
/**
 *  _____ _               _____ _ _   _                 _____           _     
 * /  ___| |             /  __ (_) | (_)               |_   _|         | |    
 * \ `--.| |_ __ _ _ __  | /  \/_| |_ _ _______ _ __     | | ___   ___ | |___ 
 *  `--. \ __/ _` | '__| | |   | | __| |_  / _ \ '_ \    | |/ _ \ / _ \| / __|
 * /\__/ / || (_| | |    | \__/\ | |_| |/ /  __/ | | |   | | (_) | (_) | \__ \
 * \____/ \__\__,_|_|     \____/_|\__|_/___\___|_| |_|   \_/\___/ \___/|_|___/
 *
 * MediaWiki configuration setting for Star Citizen Wiki
 *
 * MediaWiki branch: REL1_39
 * When updating major MediaWiki version, please update the branch text above
 * in this document, it will update the documentation links to the right version.
 *
 * @see https://www.mediawiki.org/wiki/Manual:LocalSettings.php Documentation
 * @link https://starcitizen.tools/ Offical site
 * @link https://discord.com/invite/XcKwqyD4sc Contact us
 */

// Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

/**
 * Maintenance config
 */
// $wgReadOnly = 'Maintenance is underway. Website is on read-only mode';
// Invalidate cache
// Get the timestamp from https://www.mediawiki.org/wiki/Manual:$wgCacheEpoch and ADD SINGLE QUOTES
// $wgCacheEpoch = '20241110231803'; // Page cache
// $wgThumbnailEpoch = '20241110231803'; // Thumbnail cache
// $wgExtensionInfoMTime = filemtime( "$IP/LocalSettings.php" ); // Extension cache

/**
 * Debug/Development config
 * Do not enable these on production unless necessary
 * 
 * @see https://www.mediawiki.org/wiki/Manual:How_to_debug
 */
// error_reporting( -1 );
// ini_set( 'display_errors', 1 );
// $wgShowExceptionDetails = true;
// $wgDebugToolbar = true;
// $wgDevelopmentWarnings = true;
// $wgDebugDumpSql = true;
// $wgDebugComments = true;

/**
 * MediaWiki core main config
 *
 * @see https://github.com/wikimedia/mediawiki/blob/REL1_39/includes/MainConfigSchema.php Definitions
 * @see https://www.mediawiki.org/wiki/Manual:Configuration_settings Documentation
 */

/**
 * Keys
 */
$wgSecretKey = "{$_ENV['MEDIAWIKI_SECRETKEY']}";
$wgUpgradeKey = "{$_ENV['MEDIAWIKI_UPGRADEKEY']}";

/**
 * Server/site settings
 */
$wgSitename = 'Star Citizen Wiki';
$wgServer = 'https://starcitizen.tools';
// Short URL paths
$wgArticlePath = "/$1";
$wgScriptPath = "";
// Main page is served as the domain root
$wgMainPageIsDomainRoot = true;
$wgLocaltimezone = "UTC";
$wgMaxShellMemory = 0;

// Logos
$wgLogos = [
	'svg' => "$wgResourceBasePath/resources/assets/sitelogo.svg",
];
$wgFavicon = '/favicon.svg';

// Email
$wgSMTP = [
	'host' => 'mail.methean.com',
	'IDHost' => 'starcitizen.tools',
	'port' => 2525,
	'auth' => true,
	'username' => 'no-reply@starcitizen.tools',
	'password' => $_ENV['SMTP_PASSWORD'],
];
$wgEmergencyContact = "webmaster@starcitizen.tools";
$wgPasswordSender = "no-reply@starcitizen.tools";
// Required for sending multipart emails (e.g. Extension:Echo)
$wgAllowHTMLEmail = true;

// Copyright
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "https://creativecommons.org/licenses/by-sa/4.0/";
$wgRightsText = "Creative Commons Attribution-ShareAlike";
$wgRightsIcon = "$wgResourceBasePath/resources/assets/licenses/cc-by-sa.png";

/**
 * Database settings
 */
$wgDBserver = "mariadb-service.default.svc.cluster.local";
$wgDBname = "scw_PROD";
$wgDBuser = "root";
$wgDBpassword = "{$_ENV['PRD_DB_PASSWORD']}";
$wgDBprefix = "wiki";
// MySQL table options to use during installation or update
$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=utf8";

/**
 * Cache settings
 */
// Set $wgCacheDirectory to a writable directory on the web server
// to make your wiki go slightly faster. The directory should not
// be publically accessible from the web.
$wgCacheDirectory = "$IP/cache";

// Define redis
$wgObjectCaches['redis'] = [
	'class' => 'RedisBagOStuff',
	'servers' => [ 'redis-service.default.svc.cluster.local' ],
	'persistent' => true,
	// 'connectTimeout' => 1,
	// 'password' => 'secret',
	// 'automaticFailOver' => true,
];

// https://phabricator.wikimedia.org/T352481
$wgMainStash = 'redis';
$wgMainCacheType = 'redis';
// Set explicitly to CACHE_DB (https://www.mediawiki.org/wiki/Manual:$wgParserCacheType)
$wgParserCacheType = CACHE_DB;
$wgSessionCacheType = 'redis';
$wgMemCachedServers = [];
$wgEnableSidebarCache = true;
$wgUseLocalMessageCache = true;
// Extend parser cache to 3 days
$wgParserCacheExpireTime = 259200;

// Cloudflare CDN settings
$wgUseCdn = true;
$wgCdnMaxAge = $wgParserCacheExpireTime;
// https://www.cloudflare.com/ips
$wgCdnServersNoPurge = [
	'194.233.168.70', // Linode Loadbalancer
	'10.0.0.0/8',
	'173.245.48.0/20',
	'103.21.244.0/22',
	'103.22.200.0/22',
	'103.31.4.0/22',
	'141.101.64.0/18',
	'108.162.192.0/18',
	'190.93.240.0/20',
	'188.114.96.0/20',
	'197.234.240.0/22',
	'198.41.128.0/17',
	'162.158.0.0/15',
	'104.16.0.0/13',
	'104.24.0.0/14',
	'172.64.0.0/13',
	'131.0.72.0/22',
	'2400:cb00::/32',
	'2606:4700::/32',
	'2803:f800::/32',
	'2405:b500::/32',
	'2405:8100::/32',
	'2a06:98c0::/29',
	'2c0f:f248::/32',
	'2405:b500::/32',
];
$wgUsePrivateIPs = true;

/**
 * Security and privacy settings
 */
// We have HSTS preload, so we should enforce HTTPS at all times
$wgForceHTTPS = true;
// Set X-Frame-Options to DENY
$wgBreakFrames = true;
$wgCSPHeader = [
	// nonces have limited support and removed in MW 1.41
	'useNonces' => false,
	'script-src' => [
		'\'self\''
	],
	'default-src' => [
		'\'self\'',
		// Flickr API is required for UploadWizard
		'https://api.flickr.com'
	],
	'style-src' => ['\'self\'',],
	'object-src' => ['\'none\''],
];
$wgReferrerPolicy = 'strict-origin-when-cross-origin';
// Cookies for me and not for thee
$wgCookieSameSite = 'Strict';
// Only send cookies over HTTPS
$wgCookieSecure = true;
// Use argon2 to hash user password (MW default: 'pbkdf2')
$wgPasswordDefault = 'argon2';
// Eww we don't want to know your real name,
// remove the real name field from sign up and preference page.
$wgHiddenPrefs[] = 'realname';
// Open external link in new tab/window
$wgExternalLinkTarget = '_blank';

/**
 * Performance settings
 */
$wgMultiShardSiteStats = true;
// @see https://phabricator.wikimedia.org/T343492
$wgResourceLoaderUseObjectCacheForDeps = true;
// Don't invalidate cache for changes in localsettings.php,
// instead use $wgCacheEpoch above to do it.
$wgInvalidateCacheOnLocalSettingsChange = false;
/**
 * Disable database-intensive features and let cron to handle them
 *
 * @see https://github.com/StarCitizenTools/sct-k8-config/blob/smw/mediawiki/mw-cronjob.yaml
 */
$wgMiserMode = true;
// Disable all the query pages that take more than about 15 minutes to update
// We will run these pages separately with a lower interval
$wgDisableQueryPageUpdate = [
	'Ancientpages' => 'half-monthly',
	'Deadendpages' => 'half-monthly',
	'Fewestrevisions' => 'half-monthly',
	'Mostlinked' => 'half-monthly',
	'Mostrevisions' => 'half-monthly',
	'Wantedpages' => 'half-monthly',
];
// Job queue
$wgJobTypeConf['default'] = [
	'class' => 'JobQueueRedis',
	'order' => 'fifo',
	'redisServer' => 'redis-service.default.svc.cluster.local',
	'checkDelay' => true,
	'daemonized' => true,
];
// We have jobrunner set up so don't run any jobs on request
$wgJobRunRate = 0;
// Defer upload tasks to jobrunner
// TODO: Check if our jobrunner can handle that
// $wgEnableAsyncUploads = true;

/**
 * Output settings
 */
// Use HTML5 encoding with minimal escaping
$wgFragmentMode = [ 'html5' ];
// Use Parsoid media HTML structure
$wgParserEnableLegacyMediaDOM = false;
// Allow MediaWiki:Citizen.css to load on all pages
$wgAllowSiteCSSOnRestrictedPages = true;
// Output a canonical meta tag on every page
$wgEnableCanonicalServerLink = true;
// Enable native lazyloading
$wgNativeImageLazyLoading = true;

/**
 * File settings
 */
// Enable image uploads
// Make sure the 'images' directory is writable
$wgEnableUploads = true;
$wgIgnoreImageErrors = true;
$wgMaxImageArea = 6.4e7;
$wgUseImageMagick = true;
// Stream and serve thumbnails with thumb.php
// Disabled due to incompatibility with Extension:WebP
// $wgGenerateThumbnailOnParse = false;
// $wgThumbnailScriptPath = "$wgScriptPath/thumb.php";

// SVG Support
$wgFileExtensions[] = 'svg';
$wgAllowTitlesInSVG = true;
$wgSVGConverter = 'ImageMagick';

/**
 * Standardize thumbnail sizes
 * MediaWiki thumbnailing is all over the place (T360589)
 * Since thumbnailing is quite performance-heavy especially
 * when we use Extension:WebP, we need to defragment the image sizes
 *
 * TODO: Wait on https://gerrit.wikimedia.org/r/c/mediawiki/core/+/1084920
 * TODO: Set wgMediaViewerThumbnailBucketSizes once we move to MW 1.43
 *
 * List of image widths on the wiki
 * 120px - File history on file page (ImageHistoryList.php)
 * 160px - Thumb size 0
 * 320px - Thumb size 1 / Image size 0
 * 480px - Responsive image
 * 640px - Image size 1
 * 1280px - Image size 2
 * 1920px - Responsive image
 * 2560px - Image size 3
 */

/** @var array Standardized thumb sizes (Multiples of 80) */
const SCT_THUMB_SIZES = [
	[ 160, 120 ],
	[ 240, 180 ],
	[ 320, 240 ],
	[ 640, 480 ],
	[ 1280, 1024 ],
	[ 2560, 2048 ],
];

// Reduce the number of thumb sizes served
$wgThumbLimits = [
	SCT_THUMB_SIZES[0][0], // thumb size 0
	SCT_THUMB_SIZES[1][0], // thumb size 1
	SCT_THUMB_SIZES[2][0], // thumb size 2
];
// Set to 300px thumb by default
$wgDefaultUserOptions['thumbsize'] = 2;
// Reduce the number of image sizes served in description page
$wgImageLimits = [
	[ SCT_THUMB_SIZES[3][0], SCT_THUMB_SIZES[3][1] ], // image size 0
	[ SCT_THUMB_SIZES[4][0], SCT_THUMB_SIZES[4][1] ], // image size 1
	[ SCT_THUMB_SIZES[5][0], SCT_THUMB_SIZES[5][1] ], // image size 2
	[ SCT_THUMB_SIZES[6][0], SCT_THUMB_SIZES[6][1] ], // image size 3
];
// Set to 1280px image by default
$wgDefaultUserOptions['imagesize'] = 2; // image size 2

// Use intermediary thumbnails to speed up thumbnail rendering
// This will result in several chained lossy transformations
// but we need it because the wiki uses a lot of high quality images
$wgThumbnailBuckets = [ SCT_THUMB_SIZES[5][0] ];
$wgThumbnailMinimumBucketDistance = 100;

// Gallery settings
// Sync with default image size 0
$wgGalleryOptions['imageWidth'] = $wgImageLimits[0][0];
$wgGalleryOptions['imageHeight'] = $wgImageLimits[0][1];
// packed-overlay seems to ignore the thumbnail restrictions above
// $wgGalleryOptions['mode'] = 'packed-overlay';

/**
 * Content settings
 */
// Fix double redirects after a page move
$wgFixDoubleRedirects = true;
// Allow pages to override their title
$wgRestrictDisplayTitle = false;

/**
 * User settings
 */
// Allow user styles
$wgAllowUserCss = true;
// Allow logged-in users to set a preference whether or not matches 
// in search results should force redirection to that page.
$wgSearchMatchRedirectPreference = true;

#=============================================== Namespaces ===============================================
/**
 * Namespace global constants
 *
 * @see https://www.mediawiki.org/wiki/Manual:Using_custom_namespaces#Creating_a_custom_namespace
 */
// Would be defined by Scribunto later, but we need it for $wgNamespaceAliases
define( 'NS_MODULE' , 828 );
define( 'NS_MODULE_TALK' , 829 );
// Custom namespaces
define( 'NS_COMMLINK', 3000 );
define( 'NS_COMMLINK_TALK', 3001 );
define( 'NS_PROJMGMT', 3002 );
define( 'NS_PROJMGMT_TALK', 3003 );
define( 'NS_ISSUE', 3004 );
define( 'NS_ISSUE_TALK', 3005 );
define( 'NS_GUIDE', 3006 );
define( 'NS_GUIDE_TALK', 3007 );
define( 'NS_ORG', 3008 );
define( 'NS_ORG_TALK', 3009 );
define( 'NS_UPDATE', 3016 );
define( 'NS_UPDATE_TALK', 3017 );
define( 'NS_ERROR', 30000 );
define( 'NS_ERROR_TALK', 30001 );

// Default to $wgSitename but we need to escape it with underscores
$wgMetaNamespace = 'Star_Citizen_Wiki';
// Add permission to edit templates
$wgNamespaceProtection[NS_TEMPLATE] = [ 'template-edit' ];

$wgExtraNamespaces[NS_COMMLINK] = 'Comm-Link';
$wgExtraNamespaces[NS_COMMLINK_TALK] = 'Comm-Link_talk';
$wgNamespacesWithSubpages[NS_COMMLINK] = true;
$wgNamespacesToBeSearchedDefault[NS_COMMLINK] = true;
$wgNamespaceProtection[NS_COMMLINK] = [ 'commlink-edit' ];

$wgExtraNamespaces[NS_PROJMGMT] = 'ProjMGMT';
$wgExtraNamespaces[NS_PROJMGMT_TALK] = 'ProjMGMT_talk';
$wgNamespacesWithSubpages[NS_PROJMGMT] = true;
$wgNamespaceProtection[NS_PROJMGMT] = [ 'projmgmt-edit' ];

$wgExtraNamespaces[NS_ISSUE] = 'Issue';
$wgExtraNamespaces[NS_ISSUE_TALK] = 'Issue_talk';
$wgNamespacesWithSubpages[NS_ISSUE] = true;
$wgNamespaceProtection[NS_ISSUE] = [ 'issue-edit' ];

$wgExtraNamespaces[NS_GUIDE] = 'Guide';
$wgExtraNamespaces[NS_GUIDE_TALK] = 'Guide_talk';
$wgNamespacesWithSubpages[NS_GUIDE] = true;
$wgNamespacesToBeSearchedDefault[NS_GUIDE] = true;
$wgNamespaceProtection[NS_GUIDE] = [ 'guide-edit' ];

$wgExtraNamespaces[NS_ORG] = 'ORG';
$wgExtraNamespaces[NS_ORG_TALK] = 'ORG_talk';
$wgNamespacesWithSubpages[NS_ORG] = true;
$wgNamespaceProtection[NS_ORG] = [ 'org-edit' ];

$wgExtraNamespaces[NS_UPDATE] = 'Update';
$wgExtraNamespaces[NS_UPDATE_TALK] = 'Update_talk';
$wgNamespacesWithSubpages[NS_UPDATE] = true;

$wgExtraNamespaces[NS_ERROR] = 'Error';
$wgExtraNamespaces[NS_ERROR_TALK] = 'Error_talk';
$wgNamespacesWithSubpages[NS_ERROR] = true;
$wgNamespacesToBeSearchedDefault[NS_ERROR] = true;

// Namespace alias
// Use capital case to avoid conflicts with interwiki links
$wgNamespaceAliases = [
	'C' => NS_CATEGORY,
	'CL' => NS_COMMLINK,
	'E' => NS_ERROR,
	'F' => NS_FILE,
	'H' => NS_HELP,
	'LUA' => NS_MODULE,
	'SCW' => NS_PROJECT,
	'SC' => NS_PROJECT,
	// Legacy support
	// We used to use Star Citizen as the project namespace name
	// TODO: Replace all instance of old namespace name on wiki then remove this
	'Star_Citizen' => NS_PROJECT,
	'ST' => NS_PROJECT_TALK,
	'T' => NS_TEMPLATE,
	'U' => NS_UPDATE,
];

$wgContentNamespaces = [
	NS_MAIN,
	NS_GUIDE,
	NS_COMMLINK,
	NS_UPDATE,
	NS_ERROR,
	NS_ORG,
];

$wgVisualEditorAvailableNamespaces = [
	NS_MAIN       => true,
	NS_USER       => true,
	NS_HELP       => true,
	NS_PROJECT     => true,
	NS_COMMLINK   => true,
	NS_ERROR      => true,
	NS_PROJMGMT   => true,
	NS_ISSUE      => true,
	NS_GUIDE      => true,
	NS_ORG        => true,
	NS_UPDATE     => true,
];

// Sitemap
$wgSitemapNamespaces = array_push(
	$wgContentNamespaces,
	NS_HELP,
	NS_FILE,
	NS_CATEGORY,
);

#=============================================== Extension Load ===============================================
wfLoadExtensions( [
	'AdvancedSearch',
	'Apiunto',
	'AWS',
	'Babel',
	'CategoryTree',
	'CheckUser',
	'CirrusSearch',
	'Cite',
	'CiteThisPage',
	'Cldr',
	'CodeEditor',
	'CodeMirror',
	'CommonsMetadata',
	'ConfirmEdit',
	'ConfirmEdit/QuestyCaptcha',
	// 'CookieWarning', // Disabled due to performance issue and we only have first party functional cookies
	'Details',
	'Disambiguator',
	'Discord',
	'DiscussionTools',
	'DismissableSiteNotice',
	'DynamicPageList3',
	'Echo',
	'Elastica',
	'EmbedVideo',
	'FloatingUI',
	'Gadgets',
	// 'Graph', // Disabled due to security issue
	'InputBox',
	'Interwiki',
	'JsonConfig',
	'Linter',
	'LoginNotify',
	'Loops',
	'MediaSearch',
	'MultimediaViewer',
	'MultiPurge',
	'NativeSvgHandler',
	'Nuke',
	'OATHAuth',
	'PageImages',
	// 'PageViewInfo', // Disabled with Extension:Plausible
	'ParserFunctions',
	'PictureHtmlSupport',
	// 'Plausible', // Disabled to allocate more resources to MW
	'Popups',
	'RelatedArticles',
	'Renameuser',
	'ReplaceText',
	'RevisionSlider',
	'RSS',
	'SandboxLink',
	'SemanticDrilldown',
	'SemanticExtraSpecialProperties',
	'SemanticMediaWiki',
	'SemanticResultFormats',
	'SemanticScribunto',
	'Scribunto',
	'ShortDescription',
	'SyntaxHighlight_GeSHi',
	'TabberNeue',
	'TemplateData',
	'TemplateStyles',
	'TemplateStylesExtender',
	'TextExtracts',
	'Thanks',
	'TwoColConflict',
	// 'UniversalLanguageSelector', // Disabled due to performance issue
	'UploadWizard',
	'Variables',
	'VipsScaler',
	'VisualEditor',
	'WebP',
	'WebAuthn',
	'WikiEditor',
	'WikiSEO',
] );

// Citizen needs to be loaded after extensions to display correct icons for extensions
wfLoadSkin( 'Citizen' );
// Set Citizen to the default skin
$wgDefaultSkin = 'citizen';

#=============================================== Extension Config ===============================================
# Apiunto 
$wgApiuntoKey = '';
$wgApiuntoUrl = 'https://api.star-citizen.wiki';
$wgApiuntoTimeout = '30'; // 5 seconds
$wgApiuntoDefaultLocale = 'en_EN';

# AWS
$wgAWSCredentials = [
	'key' => $_ENV['IMAGES_ACCESS_KEY'],
	'secret' => $_ENV['IMAGES_SECRET_KEY'],
	'token' => false
];
$wgAWSBucketName = 'media.starcitizen.tools';
$wgAWSBucketDomain = 'media.starcitizen.tools';
$wgAWSRepoHashLevels = '2';
$wgAWSRepoDeletedHashLevels = '3';
$wgAWSRegion = 'eu-central-1';
// These MW core settings are grouped under Extension:AWS
// because Extension:AWS is the only consumer
// Set up S3 bucket as backend
$wgFileBackends['s3']['endpoint'] = 'https://eu-central-1.linodeobjects.com';
// Preconnect to media.starcitizen.tools
$wgImagePreconnect = true;

# CirrusSearch
$wgCirrusSearchIndexBaseName = 'scw_prod';
$wgSearchType = 'CirrusSearch';
$wgCirrusSearchUseCompletionSuggester = 'yes';
$wgCirrusSearchClusters = [
	'default' => [ 'elasticsearch-es-elasticsearch.default.svc.cluster.local' ],
];
$wgCirrusSearchCompletionSuggesterSubphrases = [
	'build'  => true,
	'use' => true,
	'type' => 'anywords',
	'limit' => 5,
];

# CleanChanges
#$wgCCTrailerFilter = true;
#$wgCCUserFilter = false;
#$wgDefaultUserOptions['usenewrc'] = 1;

# Code Editor
$wgDefaultUserOptions['usebetatoolbar'] = 1; // user option provided by WikiEditor extension

# Code Mirror
# Enable syntax highlight in editor by default
$wgDefaultUserOptions['usecodemirror'] = 1;

# CookieWarning
#$wgCookieWarningEnabled = true;

# ConfirmEdit
#$wgHCaptchaSiteKey = "{$_ENV['HCAPTCHA_SITEKEY']}";
#$wgHCaptchaSecretKey = "{$_ENV['HCAPTCHA_SECRETKEY']}";
$wgCaptchaTriggers['edit'] = true;
$wgCaptchaTriggers['create'] = true;

# Details
# Disable custom handling since we only need to write <details> and <summary> in wikitext
$wgDetailsMWCollapsibleCompatibility = false;

# Discord
$wgDiscordWebhookURL = [ "{$_ENV['DISCORD_WEBHOOKURL']}" ];

# DismissableSiteNotice
$wgDismissableSiteNoticeForAnons = true;

# DynamicPageList3
$wgDplSettings['recursiveTagParse'] = true;
$wgDplSettings['allowUnlimitedResults'] = true;

# EmbedVideo
# Disable the embed styles so that the EmbedVideo ResourceLoader modules
# won't load on every single page
$wgEmbedVideoUseEmbedStyleForLocalVideos = false;

# LocalicationUpdate
// $wgLocalisationUpdateDirectory = "$IP/cache";

# MultimediaViewer
$wgMediaViewerEnableByDefault = true;
$wgMediaViewerEnableByDefaultForAnonymous = true;

# MultiPurge
$wgMultiPurgeEnabledServices = [ 'Cloudflare' ];
$wgMultiPurgeServiceOrder = [ 'Cloudflare' ];
$wgMultiPurgeCloudFlareZoneId = "{$_ENV['CLOUDFLARE_ZONEID']}";
$wgMultiPurgeCloudFlareApiToken = "{$_ENV['CLOUDFLARE_APITOKEN']}";
$wgMultiPurgeStaticPurges = [
	'Load Script' => 'load.php?lang=de&modules=startup&only=scripts&raw=1&skin=citizen'
];
$wgMultiPurgeRunInQueue = true;

# PageImages
$wgPageImagesAPIDefaultLicense = 'any';
$wgPageImagesLeadSectionOnly = false;
$wgPageImagesNamespaces = $wgContentNamespaces;
$wgPageImagesOpenGraphFallbackImage = "$wgResourceBasePath/resources/assets/sitelogo.svg";

# Parsoid
# Need to load Parsoid explicitly to make Linter work
# @see https://github.com/StarCitizenWiki/WikiDocker/commit/ea149d74daba5cc13594cee57db70dab099e214d
wfLoadExtension( 'Parsoid', "$IP/vendor/wikimedia/parsoid/extension.json" );
$wgParsoidSettings = [
	'useSelser' => true,
	'linting' => true,
];
// This belongs to VE but this is more relevant here
$wgVisualEditorParsoidAutoConfig = false;
$wgVirtualRestConfig['modules']['parsoid'] = [
	// URL to the Parsoid instance - use port 8142 if you use the Debian package - the parameter 'URL' was first used but is now deprecated (string)
	'url' => 'https://starcitizen.tools/rest.php',
	// Parsoid "domain" (string, optional) - MediaWiki >= 1.26
	'domain' => 'starcitizen.tools',
	'restbaseCompat' => false,
	'timeout' => 30,
];

# Plausible
// $wgPlausibleDomain = 'https://analytics.starcitizen.tools';
// $wgPlausibleDomainKey = 'starcitizen.tools';
// $wgPlausibleHonorDNT = true;
// $wgPlausibleTrackLoggedIn = true;
// $wgPlausibleTrackOutboundLinks = true;
// $wgPlausibleIgnoredTitles = [ '/Special:*' ];
// $wgPlausibleEnableCustomEvents = true;
// $wgPlausibleTrack404 = true;
// $wgPlausibleTrackSearchInput = true;
// $wgPlausibleTrackEditButtonClicks = true;
// $wgPlausibleTrackCitizenSearchLinks = true;
// $wgPlausibleTrackCitizenMenuLinks = true;
// $wgPlausibleApiKey = "{$_ENV['PLAUSIBLE_APIKEY']}";

# Popups
// Reference Previews are enabled for all users by default
$wgPopupsReferencePreviewsBetaFeature = false;

# Questy Catpcha
$wgCaptchaQuestions = [
	"What the name of site?" => [ 'sct', 'star citizen wiki', 'star citizen tools', 'starcitizen.tools' ],
	"What is the name of the company that is developing the game?" => [ 'cig', 'rsi', 'cloud imperium', 'cloud imperium games', 'robert space industries', 'roberts space industries' ],
	"Who is the co-founder, CEO, director of the game's developer" => [ 'chris roberts', 'chris robert' ],
	"What is the single player part of the game named?" => [ 'squadron 42', 'sq42', 'squadron42' ],
	"Who is the in-lore manufacturer of the <a href='https://starcitizen.tools/Talon'> Talon</a>? " => [ 'esperia', 'espr', 'esperia (espr)' ],
];

# RelatedArticles 
// Enable RelatedArticle for Citizen
$wgRelatedArticlesFooterWhitelistedSkins = [ 'citizen' ];
// Needed because we changed script path for Short URL
$wgRelatedArticlesUseCirrusSearchApiUrl = '/api.php';
// wikidata is supplied by Extension:ShortDescription
$wgRelatedArticlesDescriptionSource = 'wikidata';
$wgRelatedArticlesUseCirrusSearch = true;
$wgRelatedArticlesOnlyUseCirrusSearch = true;

# Semantic Mediawiki
// Required to enable SMW
enableSemantics( 'starcitizen.tools' );
// Set default property type to Text
// Because we use SMW property for displaying data through templates mainly
$smwgPDefaultType = '_txt';
// Use Redis to cache SMW query result
$smwgQueryResultCacheType = 'redis';
// Enable tracking and storing of dependencies of embedded queries
$smwgEnabledQueryDependencyLinksStore = true;
// Duplicate query conditions should be removed from computing query results
$smwgQFilterDuplicates = true;
$smwgConfigFileDir = "/usr/local/smw";
// Enable SMW in the following namespaces
$smwgNamespacesWithSemanticLinks[NS_TEMPLATE] = true;
$smwgNamespacesWithSemanticLinks[NS_MODULE] = true;
foreach ($wgContentNamespaces as $contentNS) {
	$smwgNamespacesWithSemanticLinks[$contentNS] = true;
}
// Disable entity issue panel for all users by default since it is useless to most users
// This generates an uncached call to api.php which is not needed
$wgDefaultUserOptions['smw-prefs-general-options-show-entity-issue-panel'] = false;

# Semantic Extra Special Properties
$sespgUseFixedTables = true;
$sespgEnabledPropertyList = [
	'_USERREG',
	'_USEREDITCNT',
	'_PAGEIMG',
	'_LINKSTO',
];
// Required by Module:DependencyList
$sespgLinksToEnabledNamespaces = [
	NS_TEMPLATE,
	NS_MODULE,
];

# Scribunto
$wgScribuntoDefaultEngine = 'luasandbox';
$wgScribuntoEngineConf['luasandbox']['memoryLimit'] = 50 * 1024 * 1024; // 50 MB
$wgScribuntoEngineConf['luasandbox']['cpuLimit'] = 10; // Seconds

# SyntaxHighlight
$wgPygmentizePath = '/usr/local/bin/pygmentize';

# TemplateStyles
$wgTemplateStylesAllowedUrls = [
	"audio" => [
		"<^https://starcitizen\\.tools/>",
		"<^https://media\\.starcitizen\\.tools/>",
	],
	"image" => [
		"<^https://starcitizen\\.tools/>",
		"<^https://media\\.starcitizen\\.tools/>",
	],
	"svg" => [
		"<^https://starcitizen\\.tools/[^?#]*\\.svg(?:[?#]|$)>",
		"<^https://media\\.starcitizen\\.tools/[^?#]*\\.svg(?:[?#]|$)>",
	],
	"font" => [
		"<^https://starcitizen\\.tools/>",
	],
	"namespace" => [
		"<.>",
	],
	"css" => [],
];

# TextExtracts
$wgExtractsRemoveClasses = [ 'dd', 'dablink', 'figcaption', 'li' ];

# TwoColConflict
$wgTwoColConflictBetaFeature = false;

# Universal Language Selector
// Disable language detection as some message fallback are broken
// Copyright notice and footer does not appear
// $wgULSLanguageDetection = false;
// Disable IME as it is not needed nowadays
// $wgULSIMEEnabled = false;
// Disable web fonts as it is not needed nowadays
// $wgULSWebfontsEnabled = false;
// Disable due to caching
// $wgULSAnonCanChangeLanguage = false;

# UploadWizard
$wgApiFrameOptions = 'SAMEORIGIN';
$wgAllowCopyUploads = true;
$wgCopyUploadsDomains = [ '*.flickr.com', '*.staticflickr.com' ];
$wgUploadNavigationUrl = '/Special:UploadWizard';
$wgUploadWizardConfig = [
	'flickrApiKey' => "{$_ENV['FLICKR_APIKEY']}",
];
$wgUploadWizardConfig = [
	'debug' => false,
	'altUploadForm' => 'Special:Upload',
	'fallbackToAltUploadForm' => false,
	'alternativeUploadToolsPage' => false,
	'enableFormData' => true,
	'enableMultipleFiles' => true,
	'enableMultiFileSelect' => false,
	'tutorial' => [
		'skip' => true,
	],
	'maxUploads' => 15,
	'fileExtensions' => $wgFileExtensions,
	'flickrApiUrl' => 'https://api.flickr.com/services/rest/?',
	'licenses' => [
		// Cloud Imperium license
		'rsilicense' => [
			// HACK: Add custom license message
			// Edit MediaWiki:mwe-upwiz-license-pd-usgov to the text you wanted
			'msg' => 'mwe-upwiz-license-pd-usgov',
			// 'msg' => 'mwe-upwiz-license-rsi',
			'templates' => [ 'RSIlicense' ],
		],
		// CC-BY-NC-SA-2.0 required by Flickr
		// Note that this need to be added to mw.FlickrChecker.js every time it is updated
		'cc-by-nc-sa-2.0' => [
			'msg' => 'mwe-upwiz-license-cc-by-nc-sa-2.0',
			'templates' => [ 'cc-by-nc-sa-2.0' ],
			// 'icons' => [ 'cc-by','cc-nc','cc-sa' ], // NC icon is missing
			'url' => '//creativecommons.org/licenses/by-nc-sa/2.0/',
			'languageCodePrefix' => 'deed.',
		],
		// CC-BY-NC-2.0 required by Flickr
		// Note that this need to be added to mw.FlickrChecker.js every time it is updated
		'cc-by-nc-2.0' => [
			'msg' => 'mwe-upwiz-license-cc-by-nc-2.0',
			'templates' => [ 'cc-by-nc-2.0' ],
			// 'icons' => [ 'cc-by','cc-nc' ], // NC icon is missing
			'url' => '//creativecommons.org/licenses/by-nc/2.0/',
			'languageCodePrefix' => 'deed.',
		],
	],
	// License selection page
	'licensing' => [
		'thirdParty' => [
			'type' => 'or',
			'defaults' => 'rsilicense',
			'licenseGroups' => [
				[
					// Cloud Imperium license
					// HACK: Add custom license header
					// Edit MediaWiki:mwe-upwiz-license-usgov-head to the text you wanted
					// We have to use this because this message is loaded by UploadWizard and we don't use it
					'head' => 'mwe-upwiz-license-usgov-head',
					// 'head' => 'mwe-upwiz-license-sc-head',
					'licenses' => [
						'rsilicense',
					],
				],
				[
					// This should be a list of all CC licenses we can reasonably expect to find around the web
					'head' => 'mwe-upwiz-license-cc-head',
					'subhead' => 'mwe-upwiz-license-cc-subhead',
					'licenses' => [
						'cc-by-sa-4.0',
						'cc-by-sa-3.0',
						'cc-by-sa-2.5',
						'cc-by-4.0',
						'cc-by-3.0',
						'cc-by-2.5',
						'cc-zero',
					],
				],
				[
					// Flickr still uses CC 2.0
					'head' => 'mwe-upwiz-license-flickr-head',
					'subhead' => 'mwe-upwiz-license-flickr-subhead',
					'licenses' => [
						'cc-by-nc-sa-2.0',
						'cc-by-nc-2.0',
						'cc-by-sa-2.0',
						'cc-by-2.0',
					],
				],
				[
					'head' => 'mwe-upwiz-license-custom-head',
					'special' => 'custom',
					'licenses' => [ 'custom' ],
				],
				[
					'head' => 'mwe-upwiz-license-none-head',
					'licenses' => [ 'none' ]
				],
			],
		],
	],
];

# VipsScaler
// We restricted the page to specific user groups
// Check the group permission settings below
$wgVipsExposeTestPage = true;
$wgVipsOptions = [
	// Enable for all PNGs
	[
		'conditions' => [
			'mimeType' => 'image/png',
		],
	],
];

# Visual Editor
$wgDefaultUserOptions['visualeditor-enable'] = 1;
$wgDefaultUserOptions['visualeditor-editor'] = "visualeditor";
$wgDefaultUserOptions['visualeditor-newwikitext'] = 1;
$wgPrefs[] = 'visualeditor-enable';
$wgVisualEditorEnableWikitext = true;
$wgVisualEditorEnableDiffPage = true;
$wgVisualEditorUseSingleEditTab = true;
$wgVisualEditorEnableVisualSectionEditing = true;

# WebP
// $wgEnabledTransformers = [
//   "MediaWiki\\Extension\\WebP\\Transformer\\WebPTransformer",
//   "MediaWiki\\Extension\\WebP\\Transformer\\AvifTransformer",
// ];
$wgWebPCompressionQuality = 95;
$wgWebPEnableResponsiveVersionJobs = true;

# WikiEditor
$wgWikiEditorRealtimePreview = true;

# WikiSEO
$wgTwitterSiteHandle = 'ToolsWiki';
$wgWikiSeoDefaultLanguange = 'en-us';
$wgWikiSeoEnableSocialImages = true;
// Disable wgLogo as fallback image in embed
$wgWikiSeoDisableLogoFallbackImage = true;
// Use TextExtracts description
$wgWikiSeoEnableAutoDescription = true;
$wgWikiSeoTryCleanAutoDescription = true;

#=============================================== Skin ===============================================

// Use REST API search endpoint
$wgCitizenSearchGateway = 'mwRestApi';
// Use Extension:ShortDescription for search suggestion description
$wgCitizenSearchDescriptionSource = 'wikidata';
// Increase the number of search results in suggestion
$wgCitizenMaxSearchResults = 10;
// Default to dark theme
$wgCitizenThemeDefault = 'dark';

#=============================================== Permissions ===============================================
$wgAutopromote = [
	'autoconfirmed' => [
		'&',
		[ APCOND_EDITCOUNT, &$wgAutoConfirmCount ],
		[ APCOND_AGE, &$wgAutoConfirmAge ],
		APCOND_EMAILCONFIRMED,
	],
	'Trusted' => [
		'&',
		[ APCOND_EDITCOUNT, 300 ],
		[ APCOND_INGROUPS, 'Verified' ],
	],
];

#all
$wgGroupPermissions['*']['createaccount'] = true;
$wgGroupPermissions['*']['edit'] = false;
$wgGroupPermissions['*']['createpage'] = false;
$wgGroupPermissions['*']['writeapi'] = true;
$wgGroupPermissions['*']['createtalk'] = false;
$wgGroupPermissions['*']['vipsscaler-test'] = false;

#user
$wgGroupPermissions['user']['edit'] = true;
$wgGroupPermissions['user']['purge'] = false;
$wgGroupPermissions['user']['createpage'] = false;
$wgGroupPermissions['user']['createtalk'] = false;
$wgGroupPermissions['user']['minoredit'] = false;
$wgGroupPermissions['user']['move'] = false;
$wgGroupPermissions['user']['movefile'] = false;
$wgGroupPermissions['user']['move-categorypages'] = false;
$wgGroupPermissions['user']['move-rootuserpages'] = false;
$wgGroupPermissions['user']['move-subpages'] = false;
$wgGroupPermissions['user']['reupload'] = false;
$wgGroupPermissions['user']['reupload-own'] = false;
$wgGroupPermissions['user']['guide-edit'] = true;
$wgGroupPermissions['user']['oathauth-enable'] = true;

#ORG Editor
$wgGroupPermissions['ORG-Editor']['org-edit'] = true;

#autoconfirmed
$wgAutoConfirmAge = 86400 * 3; // three days
$wgAutoConfirmCount = 20;
$wgGroupPermissions['autoconfirmed']['upload_by_url'] = true;
$wgGroupPermissions['autoconfirmed']['createpage'] = true;
$wgGroupPermissions['autoconfirmed']['createtalk'] = true;

#verified
$wgGroupPermissions['Verified'] = $wgGroupPermissions['autoconfirmed'];
$wgGroupPermissions['Verified']['skipcaptcha'] = true;
$wgGroupPermissions['Verified']['purge'] = true;
$wgGroupPermissions['Verified']['reupload'] = true;
$wgGroupPermissions['Verified']['reupload-own'] = true;
$wgGroupPermissions['Verified']['minoredit'] = true;

#trusted
$wgGroupPermissions['Trusted'] = $wgGroupPermissions['Verified'];
$wgGroupPermissions['Trusted']['patrol'] = true;
$wgGroupPermissions['Trusted']['move'] = true;
$wgGroupPermissions['Trusted']['movefile'] = true;
$wgGroupPermissions['Trusted']['move-categorypages'] = true;
$wgGroupPermissions['Trusted']['writeapi'] = true;
$wgGroupPermissions['Trusted']['sendemail'] = true;
$wgGroupPermissions['Trusted']['commlink-edit'] = true;
$wgGroupPermissions['Trusted']['issue-edit'] = true;
$wgGroupPermissions['Trusted']['projmgmt-edit'] = true;
$wgGroupPermissions['Trusted']['move-subpages'] = true;
$wgGroupPermissions['Trusted']['template-edit'] = true;

#editor
$wgGroupPermissions['Editor'] = $wgGroupPermissions['Trusted'];
$wgAddGroups['Editor'] = ['Verified', 'Translator', 'ORG-Editor'];
$wgGroupPermissions['Editor']['rollback'] = true;
$wgGroupPermissions['Editor']['protect'] = true;
$wgGroupPermissions['Editor']['editprotected'] = true;
$wgGroupPermissions['Editor']['suppressredirect'] = true;
$wgGroupPermissions['Editor']['autopatrol'] = true;
$wgGroupPermissions['Editor']['pagetranslation'] = true;
$wgGroupPermissions['Editor']['delete'] = true;
$wgGroupPermissions['Editor']['bigdelete'] = true;
$wgGroupPermissions['Editor']['deletedhistory'] = true;
$wgGroupPermissions['Editor']['deletedtext'] = true;
$wgGroupPermissions['Editor']['block'] = true;
$wgGroupPermissions['Editor']['undelete'] = true;
$wgGroupPermissions['Editor']['mergehistory'] = true;
$wgGroupPermissions['Editor']['browsearchive'] = true;
$wgGroupPermissions['Editor']['noratelimit'] = true;
$wgGroupPermissions['Editor']['move-rootuserpages'] = true;
$wgGroupPermissions['Editor']['org-edit'] = true;
$wgGroupPermissions['Editor']['vipsscaler-test'] = true;

#sysop
$wgGroupPermissions['sysop'] = $wgGroupPermissions['Editor'];
$wgGroupPermissions['sysop']['userrights'] = true;
$wgGroupPermissions['sysop']['siteadmin'] = true;
$wgGroupPermissions['sysop']['checkuser'] = true;
$wgGroupPermissions['sysop']['checkuser-log'] = true;
$wgGroupPermissions['sysop']['nuke'] = true;
$wgGroupPermissions['sysop']['editinterface'] = true;
$wgGroupPermissions['sysop']['delete'] = true;
$wgGroupPermissions['sysop']['renameuser'] = true;
$wgGroupPermissions['sysop']['import'] = true;
$wgGroupPermissions['sysop']['importupload'] = true;
$wgGroupPermissions['sysop']['smw-admin'] = true;
$wgGroupPermissions['sysop']['smw-pageedit'] = true;
$wgGroupPermissions['sysop']['smw-patternedit'] = true;
$wgGroupPermissions['sysop']['smw-schemaedit'] = true;
// To grant sysops permissions to edit interwiki data
$wgGroupPermissions['sysop']['interwiki'] = true;

#=============================================== Footer ===============================================

$wgFooterIcons = [
	'poweredby' => [
		'mediawiki' => [
			'src' => "$wgResourceBasePath/resources/assets/badge-mediawiki.svg",
			'url' => 'https://www.mediawiki.org',
			'alt' => 'Powered by MediaWiki',
			'height' => '42',
			'width' => '127',
		],
		'semanticmediawiki' => [
			'src' => "$wgResourceBasePath/resources/assets/badge-semanticmediawiki.svg",
			'url' => 'https://www.semantic-mediawiki.org/wiki/Semantic_MediaWiki',
			'alt' => 'Powered by Semantic MediaWiki',
			'height' => '42',
			'width' => '131',
		],
	],
	'copyright' => [
		'copyright' => [
			'src' => "$wgResourceBasePath/resources/assets/badge-ccbysa.svg",
			'url' => $wgRightsUrl,
			'alt' => $wgRightsText,
			'height' => "50",
			'width' => "110",
		],
	],
	'madeby' => [
		'thecommunity' => [
			'src' => "$wgResourceBasePath/resources/assets/badge-starcitizencommunity.svg",
			'url' => 'https://robertsspaceindustries.com',
			'alt' => 'Made by the community',
			'height' => '50',
			'width' => '50',
		],
	],
	'partof' => [
		'starcitizentools' => [
			'src' => "$wgResourceBasePath/resources/assets/badge-starcitizentools.svg",
			'url' => 'https://starcitizen.tools',
			'alt' => 'Part of Star Citizen Tools',
			'height' => '50',
			'width' => '50',
		],
	],
];

// Add links to footer
$wgHooks['SkinAddFooterLinks'][] = function ($sk, $key, &$footerlinks) {
	$rel = 'nofollow noreferrer noopener';

	if ($key === 'places') {
		$footerlinks['cookiestatement'] = Html::rawElement(
			'a',
			[
				'href' => Title::newFromText(
					$sk->msg('cookiestatementpage')->inContentLanguage()->text()
				)->getFullURL()
			],
			$sk->msg('cookiestatement')->escaped()
		);
		#$footerlinks['analytics'] = Html::rawElement(
		#	'a',
		#	[
		#		'href' => 'https://analytics.starcitizen.tools/starcitizen.tools',
		#		'rel' => $rel
		#	],
		#	$sk->msg( 'footer-analytics' )->escaped()
		#);
		$footerlinks['statuspage'] = Html::rawElement(
			'a',
			[
				'href' => 'https://status.starcitizen.tools',
				'rel' => $rel
			],
			$sk->msg('footer-statuspage')->escaped()
		);
		$footerlinks['github'] = Html::rawElement(
			'a',
			[
				'href' => 'https://github.com/StarCitizenTools',
				'rel' => $rel
			],
			$sk->msg('footer-github')->escaped()
		);
		$footerlinks['patreon'] = Html::rawElement(
			'a',
			[
				'href' => 'https://www.patreon.com/starcitizentools',
				'rel' => $rel
			],
			$sk->msg('footer-patreon')->escaped()
		);
		$footerlinks['kofi'] = Html::rawElement(
			'a',
			[
				'href' => 'https://ko-fi.com/starcitizentools',
				'rel' => $rel
			],
			$sk->msg('footer-kofi')->escaped()
		);
	}
};

# Eager load the first image on the page
# Currently we don't have a reliable way to set which image,
# so we will just grab the first image with 400px as width,
# since it is used by infoboxes usually.
$hasSetImageEager = false;
$wgHooks['ThumbnailBeforeProduceHTML'][] = function ($thumbnail, &$attribs, &$linkAttribs) {
	global $hasSetImageEager;
	if ($hasSetImageEager === false) {
		/**
		 * Check if the image is a LCP image
		 * 1. Make sure that the image has the mw-file-description class
		 * 2. Make sure that the image has 400px image width (i.e. infobox image)
		 */
		$isLCPImage = strpos($linkAttribs['class'] ?? '', 'mw-file-description') !== false &&
			$attribs['width'] === 400;
		if ($isLCPImage) {
			unset($attribs['loading']);
			$attribs['fetchpriority'] = 'high';
			$hasSetImageEager = true;
		}
	}
	return true;
};
