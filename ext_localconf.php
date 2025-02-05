<?php
defined('TYPO3_MODE') or die();

/* ===========================================================================
  Custom cache, done with the caching framework
=========================================================================== */
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_ttaddress_category'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_ttaddress_category'] = [];
}
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ttaddress_geocoding'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ttaddress_geocoding'] = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
        'backend'  => \TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend::class,
    ];
}

/* ===========================================================================
  Hooks
=========================================================================== */
// Add wizard with map for setting geo location
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1546531781] = [
   'nodeName' => 'locationMapWizard',
   'priority' => 30,
   'class' => \FriendsOfTYPO3\TtAddress\FormEngine\FieldControl\LocationMapWizard::class
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:tt_address/Configuration/TSconfig/NewContentElementWizard.typoscript">');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'TtAddress',
    'ListView',
    [
       \FriendsOfTYPO3\TtAddress\Controller\AddressController::class => 'list,show'
    ]
);

// Register evaluations for TCA
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\FriendsOfTYPO3\TtAddress\Evaluation\TelephoneEvaluation::class] = '';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\FriendsOfTYPO3\TtAddress\Evaluation\LatitudeEvaluation::class] = '';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\FriendsOfTYPO3\TtAddress\Evaluation\LongitudeEvaluation::class] = '';

// Register icons
$icons = [
    'apps-pagetree-folder-contains-tt-address' => 'page-tree-module.svg',
    'tt-address-plugin' => 'ContentElementWizard.svg',
    'location-map-wizard' => 'actions-geo.svg',
];
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
foreach ($icons as $identifier => $path) {
    $iconRegistry->registerIcon(
        $identifier,
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:tt_address/Resources/Public/Icons/' . $path]
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(trim('
    config.pageTitleProviders {
        tt_address {
            provider = FriendsOfTYPO3\TtAddress\Seo\AddressTitleProvider
            before = altPageTitle,record,seo
        }
    }
'));

// Plugin Preview
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['tt_address']
    = \FriendsOfTYPO3\TtAddress\Hooks\PageLayoutViewHook::class;
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('mod.web_layout.tt_content.preview.list.ttaddress_listview = EXT:tt_address/Resources/Private/Templates/Backend/PluginPreview.html');
