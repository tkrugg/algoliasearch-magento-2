<?php

namespace Algolia\AlgoliaSearch\Model;

use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Algolia\AlgoliaSearch\Helper\Data;
use Algolia\AlgoliaSearch\Helper\ProxyHelper;

class ConfigurationTracker
{
    /** @var Data */
    private $proxyHelper;

    /** @var ConfigHelper */
    private $configHelper;

    /**
     * @param ProxyHelper $proxyHelper
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        ProxyHelper $proxyHelper,
        ConfigHelper $configHelper
    ) {
        $this->proxyHelper = $proxyHelper;
        $this->configHelper = $configHelper;
    }

    /**
     * @param int $storeId
     */
    public function trackConfiguration($storeId)
    {
        $this->proxyHelper->trackEvent($this->configHelper->getApplicationID($storeId), 'Configuration saved', [
            'source' => 'magento2.saveconfig',
            'indexingEnabled' => $this->configHelper->isEnabledBackend($storeId),
            'searchEnabled' => $this->configHelper->isEnabledFrontEnd($storeId),
            'autocompleteEnabled' => $this->configHelper->isAutoCompleteEnabled($storeId),
            'instantsearchEnabled' => $this->configHelper->isInstantEnabled($storeId),
            'sortingChanged' => $this->isSortingChanged($storeId),
            'rankingChanged' => $this->isCustomRankingChanged($storeId),
            'replaceImageByVariantUsed' => $this->configHelper->useAdaptiveImage($storeId),
            'indexingQueueEnabled' => $this->configHelper->isQueueActive($storeId),
            'synonymsManagementEnabled' => $this->configHelper->isEnabledSynonyms($storeId),
            'clickAnalyticsEnabled' => $this->configHelper->isClickConversionAnalyticsEnabled($storeId),
            'googleAnalyticsEnabled' => $this->configHelper->isAnalyticsEnabled($storeId),
            'customerGroupsEnabled' => $this->configHelper->isCustomerGroupsEnabled($storeId),
            // 'merchangisingQRsCreated' => true, TODO
            // 'landingPageCreated' => true, TODO
            // 'noOfMerchandisingQRs' => 10, TODO
        ]);
    }

    /**
     * @param int $storeId
     *
     * @return bool
     */
    private function isSortingChanged($storeId)
    {
        // TODO: Take the default array directly from UpgradeSchema.php or find a way how not to duplicate the array
        return $this->configHelper->getSorting($storeId) !== [
                [
                    'attribute' => 'price',
                    'sort' => 'asc',
                    'sortLabel' => 'Lowest price',
                ],
                [
                    'attribute' => 'price',
                    'sort' => 'desc',
                    'sortLabel' => 'Highest price',
                ],
                [
                    'attribute' => 'created_at',
                    'sort' => 'desc',
                    'sortLabel' => 'Newest first',
                ],
            ];
    }

    /**
     * @param int $storeId
     *
     * @return bool
     */
    private function isCustomRankingChanged($storeId)
    {
        // TODO: Take the default array directly from UpgradeSchema.php or find a way how not to duplicate the array
        return $this->configHelper->getProductCustomRanking($storeId) !== [
                [
                    'attribute' => 'in_stock',
                    'order' => 'desc',
                ],
                [
                    'attribute' => 'ordered_qty',
                    'order' => 'desc',
                ],
                [
                    'attribute' => 'created_at',
                    'order' => 'desc',
                ],
            ];
    }
}
