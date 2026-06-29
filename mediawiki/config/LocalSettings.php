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
 * MediaWiki branch: REL1_43
 * When updating major MediaWiki version, please update the branch text above
 * in this document, it will update the documentation links to the right version.
 *
 * @see https://www.mediawiki.org/wiki/Manual:LocalSettings.php Documentation
 * @link https://starcitizen.tools/ Offical site
 * @link https://discord.com/invite/XcKwqyD4sc Contact us
 */

use MediaWiki\Html\Html;
use MediaWiki\Title\Title;

// Protect against web entry
if (!defined("MEDIAWIKI")) {
    exit();
}

// Use dev.starcitizen.tools if it is in dev
const SCT_DOMAIN = "starcitizen.tools";

/**
 * Invalidate cache
 *
 * Changing localsettings.php no longer invalidate cache
 * since we set $wgInvalidateCacheOnLocalSettingsChange to false
 * These cache would need to be invalidated manually if needed.
 */
// Get the timestamp from https://www.mediawiki.org/wiki/Manual:$wgCacheEpoch and ADD SINGLE QUOTES
$wgCacheEpoch = "20260506221529"; // Page cache - Invalidate when there are HTML changes
// $wgThumbnailEpoch = '20241210023315'; // Thumbnail cache - Invalidate when there are thumbanil/image config changes
// $wgExtensionInfoMTime = filemtime( "$IP/LocalSettings.php" ); // Extension cache
$sespgLabelCacheVersion = "2025.04"; // Semantic Extra Special Properties cache

/**
 * Maintenance config
 */
// $wgReadOnly = 'Maintenance is underway. Website is on read-only mode';

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
 * @see https://github.com/wikimedia/mediawiki/blob/REL1_43/includes/MainConfigSchema.php Definitions
 * @see https://www.mediawiki.org/wiki/Manual:Configuration_settings Documentation
 */

/**
 * Keys
 */
$wgSecretKey = getenv("MEDIAWIKI_SECRETKEY");
$wgUpgradeKey = getenv("MEDIAWIKI_UPGRADEKEY");

/**
 * Server/site settings
 */
$wgSitename = "Star Citizen Wiki";
$wgServer = "https://" . SCT_DOMAIN;
// Short URL paths
$wgArticlePath = '/$1';
$wgScriptPath = "";
// Main page is served as the domain root
$wgMainPageIsDomainRoot = true;
$wgLocaltimezone = "UTC";
$wgMaxShellMemory = 0;

// Copyright
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "https://creativecommons.org/licenses/by-sa/4.0/";
$wgRightsText = "Creative Commons Attribution-ShareAlike";
$wgRightsIcon = "$wgResourceBasePath/resources/assets/licenses/cc-by-sa.png";

// Logos and icons
$wgLogos = [
    "svg" => "$wgResourceBasePath/resources/assets/sitelogo.svg",
];
$wgFavicon = "/favicon.svg";
$wgFooterIcons = [
    "poweredby" => [
        "mediawiki" => [
            "src" => "$wgResourceBasePath/resources/assets/badge-mediawiki.svg",
            "url" => "https://www.mediawiki.org",
            "alt" => "Powered by MediaWiki",
            "height" => "42",
            "width" => "127",
        ],
    ],
    "poweredbysmw" => [
        "semanticmediawiki" => [
            "src" => "$wgResourceBasePath/resources/assets/badge-semanticmediawiki.svg",
            "url" =>
                "https://www.semantic-mediawiki.org/wiki/Semantic_MediaWiki",
            "alt" => "Powered by Semantic MediaWiki",
            "height" => "42",
            "width" => "131",
        ],
    ],
    "copyright" => [
        "copyright" => [
            "src" => "$wgResourceBasePath/resources/assets/badge-ccbysa.svg",
            "url" => $wgRightsUrl,
            "alt" => $wgRightsText,
            "height" => "50",
            "width" => "110",
        ],
    ],
    "madeby" => [
        "thecommunity" => [
            "src" => "$wgResourceBasePath/resources/assets/badge-starcitizencommunity.svg",
            "url" => "https://robertsspaceindustries.com",
            "alt" => "Made by the community",
            "height" => "50",
            "width" => "50",
        ],
    ],
    "partof" => [
        "starcitizentools" => [
            "src" => "$wgResourceBasePath/resources/assets/badge-starcitizentools.svg",
            "url" => "https://starcitizen.tools",
            "alt" => "Part of Star Citizen Tools",
            "height" => "50",
            "width" => "50",
        ],
    ],
];

// Email
$wgSMTP = [
    "host" => "mail.methean.com",
    "IDHost" => "starcitizen.tools",
    "port" => 2525,
    "auth" => true,
    "username" => "no-reply@starcitizen.tools",
    "password" => getenv("SMTP_PASSWORD"),
];
$wgEmergencyContact = "webmaster@starcitizen.tools";
$wgPasswordSender = "no-reply@starcitizen.tools";
// Required for sending multipart emails (e.g. Extension:Echo)
$wgAllowHTMLEmail = true;

/**
 * Database settings
 */
$wgDBserver = "mariadb-service.default.svc.cluster.local";
$wgDBname = "scw_PROD";
$wgDBuser = "root";
$wgDBpassword = getenv("PRD_DB_PASSWORD");
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
$wgObjectCaches["valkey"] = [
    "class" => "RedisBagOStuff",
    "servers" => ["valkey-service.default.svc.cluster.local"],
    "persistent" => true,
    // 'connectTimeout' => 1,
    // 'password' => 'secret',
    // 'automaticFailOver' => true,
];

// https://phabricator.wikimedia.org/T352481
$wgMainStash = "valkey";
$wgMainCacheType = "valkey";
$wgParserCacheType = "valkey";
$wgSessionCacheType = "valkey";
$wgLanguageConverterCacheType = "valkey";
$wgEnableSidebarCache = true;
$wgUseLocalMessageCache = true;
$wgGitInfoCacheDirectory = "$IP/cache/gitinfo";

// Extend parser cache to 3 days
$wgParserCacheExpireTime = 259200;

// Extend search suggestions response cache to 3 days
// This allows the CDN to cache the search suggestions response
$wgSearchSuggestCacheExpiry = 259200;

// Cloudflare CDN settings
$wgUseCdn = true;
$wgCdnMaxAge = $wgParserCacheExpireTime;
$wgCdnMatchParameterOrder = false;
$wgCdnServersNoPurge = [
    // Linode Loadbalancer
    "143.42.223.238",
    // Internal IPs
    "10.0.0.0/8",
    // Cloudflare IPv4
    // https://www.cloudflare.com/ips
    "173.245.48.0/20",
    "103.21.244.0/22",
    "103.22.200.0/22",
    "103.31.4.0/22",
    "141.101.64.0/18",
    "108.162.192.0/18",
    "190.93.240.0/20",
    "188.114.96.0/20",
    "197.234.240.0/22",
    "198.41.128.0/17",
    "162.158.0.0/15",
    "104.16.0.0/13",
    "104.24.0.0/14",
    "172.64.0.0/13",
    "131.0.72.0/22",
    // Cloudflare IPv6
    "2400:cb00::/32",
    "2606:4700::/32",
    "2803:f800::/32",
    "2405:b500::/32",
    "2405:8100::/32",
    "2a06:98c0::/29",
    "2c0f:f248::/32",
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
    "script-src" => [
        '\'self\'',
        // Cloudflare RUM
        "https://static.cloudflareinsights.com",
    ],
    "default-src" => [
        '\'self\'',
        // Flickr API is required for UploadWizard
        "https://api.flickr.com",
    ],
    "style-src" => ['\'self\''],
    "object-src" => ['\'none\''],
];
// $wgReferrerPolicy = 'strict-origin-when-cross-origin'; // Enforced through HTTP header
// Cookies for me and not for thee
$wgCookieSameSite = "Strict";
// Only send cookies over HTTPS
$wgCookieSecure = true;
// Use argon2 to hash user password (MW default: 'pbkdf2')
$wgPasswordDefault = "argon2";
// Eww we don't want to know your real name,
// remove the real name field from sign up and preference page.
$wgHiddenPrefs[] = "realname";
// Open external link in new tab/window
$wgExternalLinkTarget = "_blank";

/**
 * Performance settings
 */
$wgMultiShardSiteStats = true;
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
    "Ancientpages" => "half-monthly",
    "Deadendpages" => "half-monthly",
    "Fewestrevisions" => "half-monthly",
    "Mostlinked" => "half-monthly",
    "Mostrevisions" => "half-monthly",
    "Wantedpages" => "half-monthly",
];
// Job queue
$wgJobTypeConf["default"] = [
    "class" => "JobQueueRedis",
    "redisServer" => "valkey-service.default.svc.cluster.local",
    "redisConfig" => [],
    "checkDelay" => true,
    "daemonized" => true,
];
// We have jobrunner set up so don't run any jobs on request
$wgJobRunRate = 0;
// Avoid excessive CPU due to cache misses from rapid invalidations
$wgJobBackoffThrottling["htmlCacheUpdate"] = 50;

// Defer upload tasks to jobrunner
// TODO: Check if our jobrunner can handle that
// $wgEnableAsyncUploads = true;

/**
 * PoolCounter
 *
 * Serializes concurrent renders of the same uncached page via Valkey so
 * that a heavy template/module/SMW edit (which invalidates parser cache
 * across hundreds or thousands of pages) doesn't cause every FPM worker
 * to re-parse the same page in parallel.
 *
 * With ArticleView ACQ4ANY semantics, one worker performs the parse and
 * waiters share the cached result instead of each running their own.
 * On timeout or queue overflow, fastStale serves the previous (now
 * invalidated) parser-cache copy rather than 503-ing.
 *
 * @see https://www.mediawiki.org/wiki/PoolCounter
 * @see https://www.mediawiki.org/wiki/Manual:$wgPoolCounterConf
 */
$wgPoolCounterConf = [
    "ArticleView" => [
        "class" => "PoolCounterRedis",
        "timeout" => 20,
        "workers" => 1,
        "maxqueue" => 8,
        "fastStale" => true,
        "servers" => [
            "valkey" => "valkey-service.default.svc.cluster.local:6379",
        ],
        "redisConfig" => [
            "persistent" => true,
        ],
    ],
    "CirrusSearch-Search" => [
        "class" => "PoolCounterRedis",
        "timeout" => 8,
        "workers" => 2,
        "maxqueue" => 4,
        "servers" => [
            "valkey" => "valkey-service.default.svc.cluster.local:6379",
        ],
        "redisConfig" => [
            "persistent" => true,
        ],
    ],
];

/**
 * Output settings
 */
// Use HTML5 encoding with minimal escaping
$wgFragmentMode = ["html5"];
// Enable new heading DOM (https://www.mediawiki.org/wiki/Heading_HTML_changes)(T13555)
$wgParserEnableLegacyHeadingDOM = false;
// Enable protection indicators (T12347)
$wgEnableProtectionIndicators = true;
// Enable sorted categories (T373480)
$wgSortedCategories = true;
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
// $wgGenerateThumbnailOnParse = false;
// $wgThumbnailScriptPath = "$wgScriptPath/thumb.php";

// SVG Support
$wgFileExtensions[] = "svg";
$wgAllowTitlesInSVG = true;
$wgSVGNativeRendering = true;

/**
 * Standardize thumbnail sizes
 * MediaWiki thumbnailing is all over the place (T360589)
 *
 * TODO: Wait on https://gerrit.wikimedia.org/r/c/mediawiki/core/+/1084920
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
    [160, 120],
    [240, 180],
    [320, 240],
    [640, 480],
    [1280, 1024],
    [2560, 2048],
];

// Reduce the number of thumb sizes served
$wgThumbLimits = [
    SCT_THUMB_SIZES[0][0], // thumb size 0
    SCT_THUMB_SIZES[1][0], // thumb size 1
    SCT_THUMB_SIZES[2][0], // thumb size 2
];
// Set to 300px thumb by default
$wgDefaultUserOptions["thumbsize"] = 2;
// Reduce the number of image sizes served in description page
$wgImageLimits = [
    [SCT_THUMB_SIZES[2][0], SCT_THUMB_SIZES[2][1]], // image size 0
    [SCT_THUMB_SIZES[3][0], SCT_THUMB_SIZES[3][1]], // image size 1
    [SCT_THUMB_SIZES[4][0], SCT_THUMB_SIZES[4][1]], // image size 2
    [SCT_THUMB_SIZES[5][0], SCT_THUMB_SIZES[5][1]], // image size 3
];
// Set to 1280px image by default
$wgDefaultUserOptions["imagesize"] = 2; // image size 2

// Use intermediary thumbnails to speed up thumbnail rendering
// This will result in several chained lossy transformations
// but we need it because the wiki uses a lot of high quality images
$wgThumbnailBuckets = [SCT_THUMB_SIZES[4][0]];
$wgThumbnailMinimumBucketDistance = 100;

// Gallery settings
// Sync with default image size 0
$wgGalleryOptions["imageWidth"] = $wgImageLimits[0][0];
$wgGalleryOptions["imageHeight"] = $wgImageLimits[0][1];
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
$wgEnableEditRecovery = true;

/**
 * Namespace settings
 *
 * @see https://www.mediawiki.org/wiki/Manual:Using_custom_namespaces#Creating_a_custom_namespace
 */
// Constant definition
// Defined by Extension:Scribunto later, but we need it for $wgNamespaceAliases
define("NS_MODULE", 828);
define("NS_MODULE_TALK", 829);
// Custom namespaces
define("NS_COMMLINK", 3000);
define("NS_COMMLINK_TALK", 3001);
define("NS_PROJMGMT", 3002);
define("NS_PROJMGMT_TALK", 3003);
define("NS_ISSUE", 3004);
define("NS_ISSUE_TALK", 3005);
define("NS_GUIDE", 3006);
define("NS_GUIDE_TALK", 3007);
define("NS_ORG", 3008);
define("NS_ORG_TALK", 3009);
define("NS_UPDATE", 3016);
define("NS_UPDATE_TALK", 3017);
define("NS_ERROR", 30000);
define("NS_ERROR_TALK", 30001);
define("NS_UUID", 69420);

// Default to $wgSitename but we need to escape it with underscores
$wgMetaNamespace = "Star_Citizen_Wiki";
// Add permission to edit templates
$wgNamespaceProtection[NS_TEMPLATE] = ["verified-edit"];
$wgNamespaceProtection[NS_MODULE] = ["verified-edit"];

$wgExtraNamespaces[NS_COMMLINK] = "Comm-Link";
$wgExtraNamespaces[NS_COMMLINK_TALK] = "Comm-Link_talk";
$wgNamespacesWithSubpages[NS_COMMLINK] = true;
$wgNamespacesToBeSearchedDefault[NS_COMMLINK] = true;

$wgExtraNamespaces[NS_PROJMGMT] = "ProjMGMT";
$wgExtraNamespaces[NS_PROJMGMT_TALK] = "ProjMGMT_talk";
$wgNamespacesWithSubpages[NS_PROJMGMT] = true;

$wgExtraNamespaces[NS_ISSUE] = "Issue";
$wgExtraNamespaces[NS_ISSUE_TALK] = "Issue_talk";
$wgNamespacesWithSubpages[NS_ISSUE] = true;

$wgExtraNamespaces[NS_GUIDE] = "Guide";
$wgExtraNamespaces[NS_GUIDE_TALK] = "Guide_talk";
$wgNamespacesWithSubpages[NS_GUIDE] = true;
$wgNamespacesToBeSearchedDefault[NS_GUIDE] = true;

$wgExtraNamespaces[NS_ORG] = "ORG";
$wgExtraNamespaces[NS_ORG_TALK] = "ORG_talk";
$wgNamespacesWithSubpages[NS_ORG] = true;
$wgNamespaceProtection[NS_ORG] = ["org-edit"];

$wgExtraNamespaces[NS_UPDATE] = "Update";
$wgExtraNamespaces[NS_UPDATE_TALK] = "Update_talk";
$wgNamespacesWithSubpages[NS_UPDATE] = true;

$wgExtraNamespaces[NS_ERROR] = "Error";
$wgExtraNamespaces[NS_ERROR_TALK] = "Error_talk";
$wgNamespacesWithSubpages[NS_ERROR] = true;
$wgNamespacesToBeSearchedDefault[NS_ERROR] = true;

$wgExtraNamespaces[NS_UUID] = "UUID";

// Namespace alias
// Use capital case to avoid conflicts with interwiki links
$wgNamespaceAliases = [
    "C" => NS_CATEGORY,
    "CL" => NS_COMMLINK,
    "E" => NS_ERROR,
    "F" => NS_FILE,
    "H" => NS_HELP,
    "LUA" => NS_MODULE,
    "SCW" => NS_PROJECT,
    "SC" => NS_PROJECT,
    // Legacy support
    // We used to use Star Citizen as the project namespace name
    // TODO: Replace all instance of old namespace name on wiki then remove this
    "Star_Citizen" => NS_PROJECT,
    "ST" => NS_PROJECT_TALK,
    "T" => NS_TEMPLATE,
    "U" => NS_UPDATE,
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
    NS_MAIN => true,
    NS_USER => true,
    NS_HELP => true,
    NS_PROJECT => true,
    NS_COMMLINK => true,
    NS_ERROR => true,
    NS_PROJMGMT => true,
    NS_ISSUE => true,
    NS_GUIDE => true,
    NS_ORG => true,
    NS_UPDATE => true,
];

// Sitemap
$wgSitemapNamespaces = array_merge($wgContentNamespaces, [
    NS_FILE,
    NS_CATEGORY,
]);

/**
 * Extensions and skins settings
 */
wfLoadExtensions([
    "AbuseFilter",
    "AdvancedSearch",
    "AGGrid",
    "Apiunto",
    "AWS",
    "Babel",
    "CategoryTree",
    "CheckUser",
    "CirrusSearch",
    "Cite",
    "CiteThisPage",
    "Cldr",
    "CodeEditor",
    "CodeMirror",
    "CommonsMetadata",
    "ConfirmEdit",
    "ConfirmEdit/QuestyCaptcha",
    "Details",
    "Disambiguator",
    "DiscussionTools",
    "DismissableSiteNotice",
    "DynamicPageList4",
    "Echo",
    "Elastica",
    "EmbedVideo",
    "FloatingUI",
    "Gadgets",
    "InputBox",
    "Interwiki",
    "JsonConfig",
    "Linter",
    "LoginNotify",
    "Loops",
    "MediaSearch",
    "MultimediaViewer",
    "MultiPurge",
    "Nuke",
    "OATHAuth",
    // "OAuth",
    "PageImages",
    "ParserFunctions",
    "ParserMigration",
    "Popups",
    "RelatedArticles",
    "ReplaceText",
    "RevisionSlider",
    "SandboxLink",
    "Scribunto",
    "SearchDigest",
    "SemanticExtraSpecialProperties",
    "SemanticMediaWiki",
    "SemanticScribunto",
    "ShortDescription",
    "SyntaxHighlight_GeSHi",
    "TabberNeue",
    "TemplateData",
    "TemplateSandbox",
    "TemplateStyles",
    "TemplateStylesExtender",
    "TextExtracts",
    "Thanks",
    "Thumbro",
    "TwoColConflict",
    "UploadWizard",
    "Variables",
    "VisualEditor",
    "WebAuthn",
    "WikiEditor",
    "WikiSEO",
]);

// Citizen needs to be loaded after extensions to display correct icons for extensions
wfLoadSkin("Citizen");
// Set Citizen to the default skin
$wgDefaultSkin = "citizen";

/**
 * Extension:AdvancedSearch
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-AdvancedSearch
 */
// We don't have the service to support deepcat:
// @see https://gerrit.wikimedia.org/g/mediawiki/extensions/AdvancedSearch/+/3019b4f8c10139b85737372b6c6f47981e2d0796/docs/settings.md#category-tree-support
$wgAdvancedSearchDeepcatEnabled = false;

/**
 * Extension:Apiunto
 *
 * @see https://github.com/StarCitizenWiki/Apiunto
 */
$wgApiuntoSources = [
    "StarCitizenWikiAPI" => [
        "baseUrl" => "https://api.star-citizen.wiki/api/",
        "token" => "",
        "timeout" => 30,
    ],
    "UEX" => [
        "baseUrl" => "https://api.uexcorp.uk/2.0/",
        "token" => getenv("UEX_APITOKEN"),
        "timeout" => 30,
        "cacheDuration" => 86400,
    ],
];

/**
 * Extension:AWS
 *
 * @see https://github.com/edwardspec/mediawiki-aws-s3
 */
$wgAWSCredentials = [
    "key" => getenv("IMAGES_ACCESS_KEY"),
    "secret" => getenv("IMAGES_SECRET_KEY"),
    "token" => false,
];
$wgAWSBucketName = "media.starcitizen.tools";
$wgAWSBucketDomain = "media.starcitizen.tools";
$wgAWSRepoHashLevels = "2";
$wgAWSRepoDeletedHashLevels = "3";
$wgAWSRegion = "eu-central-1";
// These MW core settings are grouped under Extension:AWS
// because Extension:AWS is the only consumer
// Set up S3 bucket as backend
$wgFileBackends["s3"]["endpoint"] = "https://eu-central-1.linodeobjects.com";
// Preconnect to media.starcitizen.tools
$wgImagePreconnect = true;
// Enable 404 handler
// $wgLocalFileRepo['transformVia404'] = true;

/**
 * Extension:CheckUser
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-CheckUser
 */
// Always give a comment on why checkuser is used
$wgCheckUserForceSummary = true;

/**
 * Extension:CirrusSearch
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-CirrusSearch
 */
$wgCirrusSearchIndexBaseName = "scw_prod";
$wgSearchType = "CirrusSearch";
$wgCirrusSearchUseCompletionSuggester = "yes";
$wgCirrusSearchClusters = [
    "default" => ["elasticsearch-es-elasticsearch.default.svc.cluster.local"],
];
$wgCirrusSearchCompletionSuggesterSubphrases = [
    "build" => true,
    "use" => true,
    "type" => "anywords",
    "limit" => 5,
];

/**
 * Extension:CodeMirror
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-CodeMirror
 */
$wgCodeMirrorV6 = true;
/*
NOTE: CodeMirrorV6 is completely broken in 2017 Wikitext Editor without line numbering
$wgCodeMirrorLineNumberingNamespaces = [
	NS_TEMPLATE,
	NS_MODULE
];
*/
// Enable syntax highlight in editor by default
$wgDefaultUserOptions["usecodemirror"] = 1;

/**
 * Extension:CommonsMetadata
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-CommonsMetadata
 */
// Enable tracking categories for CommonsMetadata, to track files without proper metadata
$wgCommonsMetadataSetTrackingCategories = true;

/**
 * Extension:ConfirmEdit
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-ConfirmEdit
 */
// hCaptcha is disabled as it did not stop the bots :(
// $wgHCaptchaSiteKey = getenv( 'HCAPTCHA_SITEKEY' );
// $wgHCaptchaSecretKey = getenv( 'HCAPTCHA_SECRETKEY' );
$wgCaptchaClass = "QuestyCaptcha";
$wgCaptchaTriggers["edit"] = true;
$wgCaptchaTriggers["create"] = true;
$wgCaptchaTriggers["sendemail"] = true;
// Questy Catpcha
$wgCaptchaQuestions = [
    "What the name of site?" => [
        "sct",
        "star citizen wiki",
        "star citizen tools",
        "starcitizen.tools",
    ],
    "What is the name of the company that is developing the game?" => [
        "cig",
        "rsi",
        "cloud imperium",
        "cloud imperium games",
        "robert space industries",
        "roberts space industries",
    ],
    "Who is the co-founder, CEO, director of the game's developer" => [
        "chris roberts",
        "chris robert",
    ],
    "What is the single player part of the game named?" => [
        "squadron 42",
        "sq42",
        "squadron42",
    ],
    "Who is the in-lore manufacturer of the <a href='https://starcitizen.tools/Talon'> Talon</a>?" => [
        "esperia",
        "espr",
        "esperia (espr)",
    ],
];

/**
 * Extension:Details
 *
 * @see https://github.com/chariz/mediawiki-extensions-Details
 */
// Disable custom handling since we only need to write <details> and <summary> in wikitext
$wgDetailsMWCollapsibleCompatibility = false;

/**
 * Extension:Discord
 *
 * @see https://github.com/jayktaylor/mw-discord
 */
$wgDiscordWebhookURL = [getenv("DISCORD_WEBHOOKURL")];
$wgDiscordUseEmojis = true;

/**
 * Extension:Disambiguator
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-Disambiguator
 */
$wgDisambiguatorNotifications = true;

/**
 * Extension:DiscussionTools
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-DiscussionTools
 */
$wgDiscussionTools_replytool = "available";
$wgDiscussionTools_newtopictool = "available";
$wgDiscussionTools_sourcemodetoolbar = "available";
$wgDiscussionTools_topicsubscription = "available";
$wgDiscussionTools_autotopicsub = "available";
$wgDiscussionTools_visualenhancements = "available";
$wgDiscussionTools_visualenhancements_namespaces = true;
$wgDiscussionTools_visualenhancements_pageframe = "available";
$wgDiscussionTools_visualenhancements_reply = "available";

/**
 * Extension:DismissableSiteNotice
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-DismissableSiteNotice
 */
// Allow anon to dismiss site notice
$wgDismissableSiteNoticeForAnons = true;

/**
 * Extension:DynamicPageList4
 *
 * @see https://github.com/Universal-Omega/DynamicPageList4
 */
$wgDPLRecursiveTagParse = true;
$wgDPLAllowUnlimitedResults = true;

/**
 * Extension:Echo
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-Echo
 */
$wgEchoUseJobQueue = true;

/**
 * Extension:EmbedVideo
 *
 * @see https://github.com/StarCitizenWiki/mediawiki-extensions-EmbedVideo
 */
// Disable the embed styles so that the EmbedVideo ResourceLoader modules
// won't load on every single page
$wgEmbedVideoUseEmbedStyleForLocalVideos = false;

/**
 * Extension:MultimediaViewer
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-MultimediaViewer
 */
$wgMediaViewerEnableByDefault = true;
$wgMediaViewerThumbnailBucketSizes = [
    SCT_THUMB_SIZES[2][0],
    SCT_THUMB_SIZES[3][0],
    SCT_THUMB_SIZES[4][0],
    SCT_THUMB_SIZES[5][0],
];

/**
 * Extension:MultiPurge
 *
 * @see https://github.com/octfx/mediawiki-extensions-MultiPurge
 */
$wgMultiPurgeEnabledServices = ["Cloudflare"];
$wgMultiPurgeServiceOrder = $wgMultiPurgeEnabledServices;
$wgMultiPurgeCloudFlareZoneId = getenv("CLOUDFLARE_ZONEID");
$wgMultiPurgeCloudFlareApiToken = getenv("CLOUDFLARE_APITOKEN");
$wgMultiPurgeStaticPurges = [
    "Startup script" =>
        "load.php?lang=en&modules=startup&only=scripts&raw=1&skin=citizen",
    "Site styles" =>
        "load.php?lang=en&modules=site.styles&only=styles&skin=citizen",
];
$wgMultiPurgeRunInQueue = true;

/**
 * Extension:OAuth
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-OAuth
 */
/*
$wgOAuth2PrivateKey = getenv("OAUTH_PRIVATE_KEY");
$wgOAuth2PublicKey = getenv("OAUTH_PUBLIC_KEY");
$wgGroupPermissions["sysop"]["mwoauthproposeconsumer"] = true;
$wgGroupPermissions["sysop"]["mwoauthupdateownconsumer"] = true;
$wgGroupPermissions["sysop"]["mwoauthmanageconsumer"] = true;
$wgGroupPermissions["sysop"]["mwoauthsuppress"] = true;
$wgGroupPermissions["sysop"]["mwoauthviewsuppressed"] = true;
$wgGroupPermissions["user"]["mwoauthmanagemygrants"] = true;
*/

/**
 * Extension:PageImages
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-PageImages
 */
$wgPageImagesAPIDefaultLicense = "any";
$wgPageImagesLeadSectionOnly = false;
$wgPageImagesNamespaces = $wgContentNamespaces;
$wgPageImagesOpenGraphFallbackImage = "$wgResourceBasePath/resources/assets/sitelogo.svg";

/**
 * Parsoid
 * Need to load Parsoid explicitly to make Extension:Linter work
 *
 * @see https://github.com/StarCitizenWiki/WikiDocker/commit/ea149d74daba5cc13594cee57db70dab099e214d
 */
wfLoadExtension("Parsoid", "$IP/vendor/wikimedia/parsoid/extension.json");
$wgParsoidSettings = [
    "useSelser" => true,
    "linting" => true,
];
/*
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
*/

/**
 * Extension:ParserMigration
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-ParserMigration
 */
$wgParserMigrationEnableQueryString = true;
// $wgParserMigrationEnableParsoidDiscussionTools = true;
// $wgParserMigrationEnableParsoidArticlePages = true;
// $wgParserMigrationUserNoticeDays = 365;
$wgParserMigrationCompactIndicator = true;

/**
 * Extension:RelatedArticles
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-RelatedArticles
 */
// Enable RelatedArticle for Citizen
$wgRelatedArticlesFooterWhitelistedSkins = ["citizen"];
// Needed because we changed script path for Short URL
$wgRelatedArticlesUseCirrusSearchApiUrl = "/api.php";
// wikidata is supplied by Extension:ShortDescription
$wgRelatedArticlesDescriptionSource = "wikidata";
$wgRelatedArticlesUseCirrusSearch = true;
$wgRelatedArticlesOnlyUseCirrusSearch = true;
// 5 is weird since we have a 3 col layout
$wgRelatedArticlesCardLimit = 6;

/**
 * Extension:Scribunto
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-Scribunto
 */
$wgScribuntoDefaultEngine = "luasandbox";
$wgScribuntoEngineConf["luasandbox"]["memoryLimit"] = 50 * 1024 * 1024; // 50 MB
$wgScribuntoEngineConf["luasandbox"]["cpuLimit"] = 10; // Seconds
$wgScribuntoGatherFunctionStats = true;

/**
 * Extension:SemanticMediaWiki
 *
 * @see https://github.com/SemanticMediaWiki/SemanticMediaWiki
 */
// Set default property type to Text
// Because we use SMW property for displaying data through templates mainly
$smwgPDefaultType = "_txt";
// Use Valkey to cache SMW query result
$smwgMainCacheType = "valkey";
$smwgQueryResultCacheType = "valkey";
// Enable tracking and storing of dependencies of embedded queries
// NOTE: Disabled due to performance issues.
// Upon enabling, it can potentially trigger a lot of parser cache invalidation,
// which throws the wiki into a deadlock.
// $smwgEnabledQueryDependencyLinksStore = true;
// Duplicate query conditions should be removed from computing query results
$smwgQFilterDuplicates = true;
$smwgConfigFileDir = "/usr/local/smw";
// Enable SMW in the following namespaces
$smwgNamespacesWithSemanticLinks[NS_USER] = true;
$smwgNamespacesWithSemanticLinks[NS_TEMPLATE] = true;
$smwgNamespacesWithSemanticLinks[NS_MODULE] = true;
foreach ($wgContentNamespaces as $contentNS) {
    $smwgNamespacesWithSemanticLinks[$contentNS] = true;
}
// Raise the default limit since we have a lot of templates and modules
// that needs to access the data (e.g. Navplates, DataTables, etc.)
$smwgQDefaultLimit = 2000;
$smwgQMaxInlineLimit = $smwgQDefaultLimit;
// Increase query max size so that we can use query with more OR conditions (e.g. UUID lookup)
$smwgQMaxSize = 100;
// Disable RDF link in <head> to mitigate bot scrapers
$smwgEnableExportRDFLink = false;
// Do not let SMW invalidate parser cache
$smwgSetParserCacheTimestamp = false;
// Drop cache keys to avoid cache fragmentation
$smwgSetParserCacheKeys = [];
// Disable entity issue panel for all users by default since it is useless to most users
// This generates an uncached call to api.php which is not needed
$wgDefaultUserOptions[
    "smw-prefs-general-options-show-entity-issue-panel"
] = false;

/**
 * Extension:SemanticExtraSpecialProperties
 *
 * @see https://github.com/SemanticMediaWiki/SemanticExtraSpecialProperties
 */

$sespgUseFixedTables = true;
$sespgEnabledPropertyList = [
    "_USERREG",
    "_USEREDITCNT",
    "_PAGEIMG",
    //'_LINKSTO', // Attempt to remove our old local definition for _LINKSTO
];
// Required by Module:DependencyList
$sespgLinksToEnabledNamespaces = [NS_TEMPLATE, NS_MODULE];

/**
 * Extension:SyntaxHighlight
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-SyntaxHighlight_GeSHi
 */
// Use more updated Pygmentize from system instead of bundled one
$wgPygmentizePath = "/usr/local/bin/pygmentize";

/**
 * Extension:TemplateSandbox
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-TemplateSandbox
 */
// Add module namespace support for TemplateSandbox
$wgTemplateSandboxEditNamespaces[] = NS_MODULE;

/**
 * Extension:TemplateStyles
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-TemplateStyles
 */
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
        '<^https://starcitizen\\.tools/[^?#]*\\.svg(?:[?#]|$)>',
        '<^https://media\\.starcitizen\\.tools/[^?#]*\\.svg(?:[?#]|$)>',
    ],
    "font" => ["<^https://starcitizen\\.tools/>"],
    "namespace" => ["<.>"],
    "css" => [],
];
$wgTemplateStylesNamespaces = [
    NS_TEMPLATE => true,
    NS_MODULE => true,
];

/**
 * Extension:TextExtracts
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-TextExtracts
 */
$wgExtractsRemoveClasses[] = ".metadata";

/**
 * Extension:Thumbro
 *
 * @see https://github.com/StarCitizenTools/mediawiki-extensions-Thumbro
 */
// We restricted the page to specific user groups
// Check the group permission settings below
// $wgThumbroExposeTestPage = true;

/**
 * Extension:TwoColConflict
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-TwoColConflict
 */
$wgTwoColConflictBetaFeature = false;

/**
 * Extension:UniversalLanguageSelector
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-UniversalLanguageSelector
 */
// Disable language detection as some message fallback are broken
// Copyright notice and footer does not appear
// $wgULSLanguageDetection = false;
// Disable IME as it is not needed nowadays
// $wgULSIMEEnabled = false;
// Disable web fonts as it is not needed nowadays
// $wgULSWebfontsEnabled = false;
// Disable due to caching
// $wgULSAnonCanChangeLanguage = false;

/**
 * Extension:UploadWizard
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-UploadWizard
 */
// MW core settings that are required by UploadWizard
$wgApiFrameOptions = "SAMEORIGIN";
$wgAllowCopyUploads = true;
$wgCopyUploadsDomains = ["*.flickr.com", "*.staticflickr.com"];
$wgUploadNavigationUrl = "/Special:UploadWizard";

// Extension settings
// @see https://github.com/wikimedia/mediawiki-extensions-UploadWizard/blob/REL1_43/UploadWizard.config.php
$wgUploadWizardConfig = [
    "campaignExpensiveStatsEnabled" => false,
    "flickrApiKey" => getenv("FLICKR_APIKEY"),
    "tutorial" => [
        "skip" => true,
    ],
    // Custom licenses
    "licenses" => [
        // Cloud Imperium license
        "rsilicense" => [
            // HACK: Add custom license message
            // Edit MediaWiki:mwe-upwiz-license-pd-usgov to the text you wanted
            "msg" => "mwe-upwiz-license-pd-usgov",
            // 'msg' => 'mwe-upwiz-license-rsi',
            "templates" => ["RSIlicense"],
        ],
        // CC-BY-NC-SA-2.0 required by Flickr
        // Note that this need to be added to mw.FlickrChecker.js every time it is updated
        "cc-by-nc-sa-2.0" => [
            "msg" => "mwe-upwiz-license-cc-by-nc-sa-2.0",
            "templates" => ["cc-by-nc-sa-2.0"],
            // 'icons' => [ 'cc-by','cc-nc','cc-sa' ], // NC icon is missing
            "url" => "//creativecommons.org/licenses/by-nc-sa/2.0/",
            "languageCodePrefix" => "deed.",
        ],
        // CC-BY-NC-2.0 required by Flickr
        // Note that this need to be added to mw.FlickrChecker.js every time it is updated
        "cc-by-nc-2.0" => [
            "msg" => "mwe-upwiz-license-cc-by-nc-2.0",
            "templates" => ["cc-by-nc-2.0"],
            // 'icons' => [ 'cc-by','cc-nc' ], // NC icon is missing
            "url" => "//creativecommons.org/licenses/by-nc/2.0/",
            "languageCodePrefix" => "deed.",
        ],
    ],
    // License selection page
    "licensing" => [
        "ownWork" => [
            "type" => "or",
            "template" => "self",
            "defaults" => ["cc-by-sa-4.0"],
            "licenses" => ["cc-by-sa-4.0", "cc-by-4.0", "cc-zero"],
        ],
        "thirdParty" => [
            "type" => "or",
            "defaults" => "rsilicense",
            "licenseGroups" => [
                [
                    // Cloud Imperium license
                    // HACK: Add custom license header
                    // Edit MediaWiki:mwe-upwiz-license-usgov-head to the text you wanted
                    // We have to use this because this message is loaded by UploadWizard and we don't use it
                    "head" => "mwe-upwiz-license-usgov-head",
                    // 'head' => 'mwe-upwiz-license-sc-head',
                    "defaults" => ["rsilicense"],
                    "licenses" => ["rsilicense"],
                ],
                [
                    // This should be a list of all CC licenses we can reasonably expect to find around the web
                    "head" => "mwe-upwiz-license-cc-head",
                    "subhead" => "mwe-upwiz-license-cc-subhead",
                    "defaults" => ["cc-by-sa-4.0"],
                    "licenses" => ["cc-by-sa-4.0", "cc-by-4.0", "cc-zero"],
                ],
                [
                    // Flickr still uses CC 2.0
                    "head" => "mwe-upwiz-license-flickr-head",
                    "subhead" => "mwe-upwiz-license-flickr-subhead",
                    "licenses" => [
                        "cc-by-nc-sa-2.0",
                        "cc-by-nc-2.0",
                        "cc-by-sa-2.0",
                        "cc-by-2.0",
                    ],
                ],
                [
                    "head" => "mwe-upwiz-license-custom-head",
                    "special" => "custom",
                    "defaults" => ["custom"],
                    "licenses" => ["custom"],
                ],
                [
                    "head" => "mwe-upwiz-license-none-head",
                    "defaults" => ["none"],
                    "licenses" => ["none"],
                ],
            ],
        ],
    ],
    "maxUploads" => 20,
    "feedbackLink" => "https://discord.gg/XcKwqyD4sc",
    "allCategoriesLink" => "",
    "altUploadForm" => "Special:Upload",
    "alternativeUploadToolsPage" => "",
];

/**
 * Extension:VisualEditor
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-VisualEditor
 */
// Enable Edit Check
// @see https://www.mediawiki.org/wiki/Edit_check
$wgVisualEditorEditCheck = true;
// Enable 2017 Wikitext Editor
// @see https://www.mediawiki.org/wiki/2017_wikitext_editor
$wgVisualEditorEnableWikitext = true;
$wgVisualEditorUseSingleEditTab = true;
$wgVisualEditorEnableVisualSectionEditing = true;
$wgDefaultUserOptions["visualeditor-enable"] = 1;
// Default to new source editor
$wgDefaultUserOptions["visualeditor-newwikitext"] = 1;
// It is enabled by default, no need to add it to preferences
$wgPrefs[] = "visualeditor-enable";

/**
 * Extension:WikiEditor
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-WikiEditor
 */
$wgWikiEditorRealtimePreview = true;

/**
 * Extension:WikiSEO
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-WikiSEO
 */
$wgTwitterSiteHandle = "ToolsWiki";
$wgWikiSeoDefaultLanguange = "en-us";
// Disable wgLogo as fallback image in embed
$wgWikiSeoDisableLogoFallbackImage = true;
// Use TextExtracts description
$wgWikiSeoEnableAutoDescription = true;
$wgWikiSeoTryCleanAutoDescription = true;

/**
 * Skin:Citizen
 *
 * @see https://github.com/StarCitizenTools/mediawiki-skins-Citizen
 */
// Default to dark theme
$wgCitizenThemeDefault = "dark";
$wgCitizenThemeColor = "#0d1012";
$wgCitizenManifestOptions = [
    "background_color" => "#0d1012",
    "description" =>
        "Unofficial wiki dedicated to Star Citizen and Squadron 42",
    "short_name" => "SC Wiki",
    "theme_color" => "#0d1012",
    "icons" => [
        [
            "src" => "$wgResourceBasePath/resources/assets/sitelogo.svg",
            "sizes" => "any",
            "type" => "image/svg+xml",
        ],
        [
            "src" => "$wgResourceBasePath/resources/assets/maskable_icon_x192.png",
            "sizes" => "192x192",
            "type" => "image/png",
            "purpose" => "maskable",
        ],
        [
            "src" => "$wgResourceBasePath/resources/assets/maskable_icon_x512.png",
            "sizes" => "512x512",
            "type" => "image/png",
            "purpose" => "maskable",
        ],
    ],
];
$wgCitizenUseNewToken = true;

/**
 * Enable WikiDiff2
 */
$wgDiffEngine = "wikidiff2";

/**
 * User group permission settings
 */
// Block creating accounts using the API
$wgAPIModules["createaccount"] = "ApiDisabled";
// 6 account creations per day (captcha failures are counted as a creation)
$wgAccountCreationThrottle = [
    [
        "count" => 6,
        "seconds" => 86400, // 1 day
    ],
];

// Anon
// Enable temp account
$wgAutoCreateTempUser['enabled'] = true;
// Disable all anon edits as anti-spam measure
// $wgGroupPermissions["*"]["edit"] = false;
// $wgGroupPermissions["*"]["createpage"] = false;
// $wgGroupPermissions["*"]["createtalk"] = false;
// Restrict Special:ThumbroTest to editors or above
$wgGroupPermissions["*"]["thumbro-test"] = false;

// Registered user
$wgGroupPermissions["user"]["edit"] = true;
$wgGroupPermissions["user"]["purge"] = false;
// Need to be verified to create page as an anti-spam measure
// Sometimes bots get through registration and will create spam page endlessly
$wgGroupPermissions["user"]["createpage"] = false;
$wgGroupPermissions["user"]["createtalk"] = false;
// Disable minor edits for easier patrol
$wgGroupPermissions["user"]["minoredit"] = false;
$wgGroupPermissions["user"]["move"] = false;
$wgGroupPermissions["user"]["movefile"] = false;
$wgGroupPermissions["user"]["move-categorypages"] = false;
$wgGroupPermissions["user"]["move-rootuserpages"] = false;
$wgGroupPermissions["user"]["move-subpages"] = false;
$wgGroupPermissions["user"]["reupload"] = false;
$wgGroupPermissions["user"]["reupload-own"] = false;
$wgGroupPermissions["user"]["oathauth-enable"] = true;
$wgGroupPermissions["user"]["guide-edit"] = true;

// ORG editor
// Special role for editing in the ORG namespace to avoid vandalism
$wgGroupPermissions["ORG-Editor"]["org-edit"] = true;

// Autoconfirmed users
$wgAutoConfirmAge = 86400 * 3; // three days
$wgAutoConfirmCount = 20;
// Add email requirement for autoconfirmed user
$wgAutopromote["autoconfirmed"][] = APCOND_EMAILCONFIRMED;
// Enable page upload, create page permission for confirmed users
$wgGroupPermissions["autoconfirmed"]["upload_by_url"] = true;
$wgGroupPermissions["autoconfirmed"]["createpage"] = true;
$wgGroupPermissions["autoconfirmed"]["createtalk"] = true;

// Verified users
// Users that are verified manually (e.g. Discord)
$wgRestrictionLevels[] = "verified-edit";
$wgGroupPermissions["Verified"] = $wgGroupPermissions["autoconfirmed"];
$wgGroupPermissions["Verified"]["skipcaptcha"] = true;
$wgGroupPermissions["Verified"]["purge"] = true;
$wgGroupPermissions["Verified"]["reupload"] = true;
$wgGroupPermissions["Verified"]["reupload-own"] = true;
$wgGroupPermissions["Verified"]["minoredit"] = true;
$wgGroupPermissions["Verified"]["verified-edit"] = true;

// Trusted users
$wgAutopromoteOnce["onEdit"]["Trusted"] = [
    "&",
    [APCOND_EDITCOUNT, 300],
    [APCOND_INGROUPS, "Verified"],
    ["!", [APCOND_INGROUPS, "Editor"]],
    ["!", [APCOND_INGROUPS, "sysop"]],
    ["!", [APCOND_INGROUPS, "bot"]],
];
$wgRestrictionLevels[] = 'trusted-edit';
$wgGroupPermissions["Trusted"] = $wgGroupPermissions["Verified"];
$wgGroupPermissions["Trusted"]["patrol"] = true;
$wgGroupPermissions["Trusted"]["move"] = true;
$wgGroupPermissions["Trusted"]["movefile"] = true;
$wgGroupPermissions["Trusted"]["move-categorypages"] = true;
$wgGroupPermissions["Trusted"]["sendemail"] = true;
$wgGroupPermissions["Trusted"]["move-subpages"] = true;

// Editors
// A hybrid of bureaucrat and sysops, some kind of moderators without site permissions
$wgGroupPermissions["Editor"] = $wgGroupPermissions["Trusted"];
$wgAddGroups["Editor"] = ["Verified", "Translator", "ORG-Editor"];
$wgGroupPermissions["Editor"]["rollback"] = true;
$wgGroupPermissions["Editor"]["protect"] = true;
$wgGroupPermissions["Editor"]["editprotected"] = true;
$wgGroupPermissions["Editor"]["suppressredirect"] = true;
$wgGroupPermissions["Editor"]["autopatrol"] = true;
$wgGroupPermissions["Editor"]["pagetranslation"] = true;
$wgGroupPermissions["Editor"]["delete"] = true;
$wgGroupPermissions["Editor"]["bigdelete"] = true;
$wgGroupPermissions["Editor"]["deletedhistory"] = true;
$wgGroupPermissions["Editor"]["deletedtext"] = true;
$wgGroupPermissions["Editor"]["block"] = true;
$wgGroupPermissions["Editor"]["undelete"] = true;
$wgGroupPermissions["Editor"]["mergehistory"] = true;
$wgGroupPermissions["Editor"]["browsearchive"] = true;
$wgGroupPermissions["Editor"]["noratelimit"] = true;
$wgGroupPermissions["Editor"]["move-rootuserpages"] = true;
$wgGroupPermissions["Editor"]["org-edit"] = true;
$wgGroupPermissions["Editor"]["thumbro-test"] = true;

// Sysop
// It's Over 9000!
$wgGroupPermissions["sysop"] = $wgGroupPermissions["Editor"];
$wgGroupPermissions["sysop"]["userrights"] = true;
$wgGroupPermissions["sysop"]["siteadmin"] = true;
$wgGroupPermissions["sysop"]["checkuser"] = true;
$wgGroupPermissions["sysop"]["checkuser-log"] = true;
$wgGroupPermissions["sysop"]["renameuser"] = true;
// To grant sysops permissions to edit interwiki data
$wgGroupPermissions["sysop"]["interwiki"] = true;

// Make these permissions grantable for Bot Passwords and OAuth
$wgGrantPermissions["editpage"]["verified-edit"] = true;
$wgGrantPermissions["editpage"]["org-edit"] = true;

/**
 * MediaWiki hooks
 * Site-specific stuff that does not make sense to go into an extension
 *
 * @see https://www.mediawiki.org/wiki/Manual:Hooks
 */

/**
 * Add links to the footer
 *
 * @see https://www.mediawiki.org/wiki/Manual:Hooks/SkinAddFooterLinks
 */
$wgHooks["SkinAddFooterLinks"][] = function ($sk, $key, &$footerlinks) {
    // Early edit
    if ($key !== "places") {
        return;
    }
    $rel = "nofollow noreferrer noopener";
    $footerlinks["cookiestatement"] = Html::rawElement(
        "a",
        [
            "href" => Title::newFromText(
                $sk->msg("cookiestatementpage")->inContentLanguage()->text(),
            )->getFullURL(),
        ],
        $sk->msg("cookiestatement")->escaped(),
    );
    //$footerlinks['analytics'] = Html::rawElement(
    //	'a',
    //	[
    //		'href' => 'https://analytics.starcitizen.tools/starcitizen.tools',
    //		'rel' => $rel
    //	],
    //	$sk->msg( 'footer-analytics' )->escaped()
    //);
    $footerlinks["statuspage"] = Html::rawElement(
        "a",
        [
            "href" => "https://status.starcitizen.tools",
            "rel" => $rel,
        ],
        $sk->msg("footer-statuspage")->escaped(),
    );
    $footerlinks["github"] = Html::rawElement(
        "a",
        [
            "href" => "https://github.com/StarCitizenTools",
            "rel" => $rel,
        ],
        $sk->msg("footer-github")->escaped(),
    );
    $footerlinks["patreon"] = Html::rawElement(
        "a",
        [
            "href" => "https://www.patreon.com/starcitizentools",
            "rel" => $rel,
        ],
        $sk->msg("footer-patreon")->escaped(),
    );
    $footerlinks["kofi"] = Html::rawElement(
        "a",
        [
            "href" => "https://ko-fi.com/starcitizentools",
            "rel" => $rel,
        ],
        $sk->msg("footer-kofi")->escaped(),
    );
};

/** @see https://www.mediawiki.org/wiki/Manual:Hooks/ThumbnailBeforeProduceHTML */
$sctHasSetImageEager = false;
$wgHooks["ThumbnailBeforeProduceHTML"][] = function (
    $thumbnail,
    &$attribs,
    &$linkAttribs,
) {
    /**
     * Eager load the first image on the page
     * Currently we don't have a reliable way to set which image,
     * so we will just grab the first image with 400px as width,
     * since it is used by infoboxes usually.
     */
    global $sctHasSetImageEager;
    if ($sctHasSetImageEager === false) {
        /**
         * Check if the image is a LCP image
         * 1. Make sure that the image has the mw-file-description class
         * 2. Make sure that the image has 400px image width (i.e. infobox image)
         */
        $isLCPImage =
            strpos($linkAttribs["class"] ?? "", "mw-file-description") !==
                false && $attribs["width"] === 400;
        if ($isLCPImage) {
            unset($attribs["loading"]);
            $attribs["fetchpriority"] = "high";
            $sctHasSetImageEager = true;
        }
    }
    return true;
};

/** @see https://www.mediawiki.org/wiki/Manual:$wgNoFollowLinks */
$wgHooks["HtmlPageLinkRendererEnd"][] = function (
    $linkRenderer,
    $target,
    $isKnown,
    &$text,
    &$attribs,
    &$ret,
) {
    // Append rel="nofollow" to red links to avoid unnecessary crawler traffic
    if (!$isKnown && preg_match("/\bnew\b/S", $attribs["class"] ?? "")) {
        $attribs["rel"] = "nofollow";
    }
    return true;
};

/**
 * Strengthen Content Security Policy implementation in MW
 *
 * @see https://doc.wikimedia.org/mediawiki-core/REL1_43/php/classMediaWiki_1_1HookContainer_1_1HookRunner.html#a1bef6100adfea8724efc95072a9c9ef2
 */
$wgHooks["ContentSecurityPolicyDirectives"][] = function (
    &$directives,
    &$policyConfig,
    $mode,
) {
    $directives[] = "base-uri 'self'"; // Special:Upload requires base-uri 'self'
    $directives[] = "form-action 'self'";
    $directives[] = "frame-ancestors 'none'";
};
