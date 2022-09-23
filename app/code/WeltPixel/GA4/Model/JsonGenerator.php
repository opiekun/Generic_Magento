<?php

namespace WeltPixel\GA4\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use WeltPixel\GA4\Model\Api as ApiCore;
use WeltPixel\GA4\Model\Api\ConversionTracking as ApiConversionTracking;
use WeltPixel\GA4\Model\Api\Remarketing as ApiRemarketing;

/**
 * Class \WeltPixel\GA4\Model\JsonGenerator
 */
class JsonGenerator extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var ApiCore
     */
    protected $apiCore;

    /**
     * @var ApiRemarketing
     */
    protected $apiRemarketing;

    /**
     * @var ApiConversionTracking
     */
    protected $apiConversionTracking;

    /**
     * @var integer
     */
    protected $fingerprint;

    /**
     * @var string
     */
    protected $accountId;

    /**
     * @var string
     */
    protected $containerId;

    /**
     * @var string
     */
    protected $measurementId;

    /**
     * @var boolean
     */
    protected $conversionEnabled;

    /**
     * @var string
     */
    protected $conversionId;

    /**
     * @var string
     */
    protected $conversionLabel;

    /**
     * @var string
     */
    protected $conversionCurrencyCode;

    /**
     * @var boolean
     */
    protected $remarketingEnabled;

    /**
     * @var string
     */
    protected $remarketingConversionCode;

    /**
     * @var string
     */
    protected $remarketingConversionLabel;

    /**
     * @var string
     */
    protected $publicId;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $jsonFileName;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ApiCore $apiCore
     * @param ApiRemarketing $apiRemarketing
     * @param ApiConversionTracking $apiConversionTracking
     * @param Filesystem $filesystem
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ApiCore $apiCore,
        ApiRemarketing $apiRemarketing,
        ApiConversionTracking $apiConversionTracking,
        Filesystem $filesystem
    ) {
        parent::__construct($context, $registry);
        $this->apiCore = $apiCore;
        $this->apiConversionTracking = $apiConversionTracking;
        $this->apiRemarketing = $apiRemarketing;
        $this->filesystem = $filesystem;
        $this->jsonFileName = 'ga4Export' . DIRECTORY_SEPARATOR . 'gtm.json';
    }

    /**
     * @param $accountId
     * @param $containerId
     * @param $measurementId
     * @param $conversionEnabled
     * @param $conversionId
     * @param $conversionLabel
     * @param $conversionCurrencyCode
     * @param $remarketingEnabled
     * @param $remarketingConversionCode
     * @param $remarketingConversionLabel
     * @param $publicId
     * @return string
     */
    public function generateItemJson(
        $accountId,
        $containerId,
        $measurementId,
        $conversionEnabled,
        $conversionId,
        $conversionLabel,
        $conversionCurrencyCode,
        $remarketingEnabled,
        $remarketingConversionCode,
        $remarketingConversionLabel,
        $publicId
    ) {
        $this->fingerprint = time();
        $this->accountId = $accountId;
        $this->containerId = $containerId;
        $this->measurementId = $measurementId;
        $this->conversionEnabled = $conversionEnabled;
        $this->conversionId = $conversionId;
        $this->conversionLabel = $conversionLabel;
        $this->conversionCurrencyCode = $conversionCurrencyCode;
        $this->remarketingEnabled = $remarketingEnabled;
        $this->remarketingConversionCode = $remarketingConversionCode;
        $this->remarketingConversionLabel = $remarketingConversionLabel;
        $this->publicId = $publicId;

        $variables = $this->getVariablesForJsonGeneration();
        $triggers = $this->getTriggersForJsonGeneration();
        $tags = $this->getTagsForJsonGeneration($triggers);

        $containerVersionOptions = [
            "path" => "accounts/$this->accountId/containers/$this->containerId/versions/0",
            "accountId" => $this->accountId,
            "containerId" => $this->containerId,
            "containerVersionId" => "0",
            "container" => [
                "path" => "accounts/$this->accountId/containers/$this->containerId",
                "accountId" => $this->accountId,
                "containerId" => $this->containerId,
                "name" => "WeltPixel_GA4_JsonExport",
                "publicId" => $this->publicId,
                "usageContext" => [
                    "WEB"
                ],
                "fingerprint" => $this->fingerprint,
                "tagManagerUrl" => "https://tagmanager.google.com/#/container/accounts/$this->accountId/containers/$this->containerId/workspaces?apiLink=container"
            ],
            "builtInVariable" => [
                [
                    "accountId" => $this->accountId,
                    "containerId" => $this->containerId,
                    "type" => "PAGE_URL",
                    "name" => "Page URL"
                ],
                [
                    "accountId" => $this->accountId,
                    "containerId" => $this->containerId,
                    "type" => "PAGE_HOSTNAME",
                    "name" => "Page Hostname"
                ],
                [
                    "accountId" => $this->accountId,
                    "containerId" => $this->containerId,
                    "type" => "PAGE_PATH",
                    "name" => "Page Path"
                ],
                [
                    "accountId" => $this->accountId,
                    "containerId" => $this->containerId,
                    "type" => "REFERRER",
                    "name" => "Referrer"
                ],
                [
                    "accountId" => $this->accountId,
                    "containerId" => $this->containerId,
                    "type" => "EVENT",
                    "name" => "Event"
                ],
            ],
            "variable" => array_values($variables),
            "trigger" => array_values($triggers),
            "tag" => array_values($tags),
            "fingerprint" => $this->fingerprint
        ];

        $jsonOptions = [
            "exportFormatVersion" => 2,
            "exportTime" => date("Y-m-d h:i:s"),
            "containerVersion" => $containerVersionOptions,
            "fingerprint" => $this->fingerprint,
            "tagManagerUrl" => "https://tagmanager.google.com/#/versions/accounts/$this->accountId/containers/$this->containerId/versions/0?apiLink=version"
        ];

        $jsonExportDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $jsonExportDir->writeFile($this->jsonFileName, json_encode($jsonOptions, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        return true;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getGeneratedJsonContent()
    {
        $jsonExportDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        return $jsonExportDir->readFile($this->jsonFileName);
    }

    /**
     * @return array
     */
    protected function getVariablesForJsonGeneration()
    {
        $variablesToCreate = $this->apiCore->getVariablesList($this->measurementId);
        if ($this->conversionEnabled) {
            $variablesToCreate = array_merge($variablesToCreate, $this->apiConversionTracking->getConversionVariablesList());
        }
        if ($this->remarketingEnabled) {
            $variablesToCreate = array_merge($variablesToCreate, $this->apiRemarketing->getRemarketingVariablesList());
        }

        $variableId = 1;
        foreach ($variablesToCreate as $variableName => &$variableOptions) {
            if (isset($variableOptions['parameter'])) {
                foreach ($variableOptions['parameter'] as &$paramOptions) {
                    if (isset($paramOptions['type'])) {
                        $paramOptions['type'] = strtoupper($paramOptions['type']);
                    }
                }
            }

            $variableOptions['accountId'] = $this->accountId;
            $variableOptions['containerId'] = $this->containerId;
            $variableOptions['variableId'] = $variableId;
            $variableOptions['fingerprint'] = $this->fingerprint;
            $variableOptions['formatValue'] = new \stdClass();
            $variableId+=1;
        }

        return $variablesToCreate;
    }

    /**
     * @return array
     */
    protected function getTriggersForJsonGeneration()
    {
        $triggersToCreate = $this->apiCore->getTriggersList();
        if ($this->conversionEnabled) {
            $triggersToCreate = array_merge($triggersToCreate, $this->apiConversionTracking->getConversionTriggersList());
        }

        $triggerId = 1;
        foreach ($triggersToCreate as $triggerName => &$triggerOptions) {
            if (isset($triggerOptions['customEventFilter'])) {
                foreach ($triggerOptions['customEventFilter'] as &$eventFilterOptions) {
                    if (isset($eventFilterOptions['parameter'])) {
                        foreach ($eventFilterOptions['parameter'] as &$paramOptions) {
                            if (isset($paramOptions['type'])) {
                                $paramOptions['type'] = strtoupper($paramOptions['type']);
                            }
                        }
                    }
                    if (isset($eventFilterOptions['type'])) {
                        if (isset($eventFilterOptions['type'])) {
                            $eventFilterOptions['type'] = strtoupper($eventFilterOptions['type']);
                        }
                    }
                }
            }
            if (isset($triggerOptions['filter'])) {
                foreach ($triggerOptions['filter'] as &$filterOptions) {
                    if (isset($filterOptions['parameter'])) {
                        foreach ($filterOptions['parameter'] as &$paramOptions) {
                            if (isset($paramOptions['type'])) {
                                $paramOptions['type'] = strtoupper($paramOptions['type']);
                            }
                        }
                    }
                    if (isset($filterOptions['type'])) {
                        $filterOptions['type'] = strtoupper($filterOptions['type']);
                    }
                }
            }
            if (isset($triggerOptions['type'])) {
                $triggerOptions['type'] = strtoupper(preg_replace('/(.)([A-Z])/', '$1_$2', $triggerOptions['type']));
            }

            $triggerOptions['accountId'] = $this->accountId;
            $triggerOptions['containerId'] = $this->containerId;
            $triggerOptions['triggerId'] = $triggerId;
            $triggerOptions['fingerprint'] = $this->fingerprint;
            $triggerId+=1;
        }

        return $triggersToCreate;
    }

    /**
     * @param array $triggers
     * @return array
     */
    public function getTagsForJsonGeneration($triggers)
    {
        $triggersMap = [];

        foreach ($triggers as $trigger) {
            $triggersMap[$trigger['name']] = $trigger['triggerId'];
        }
        $tagsToCreate = $this->apiCore->getTagsList($triggersMap);

        if ($this->conversionEnabled) {
            $params = [
                'conversion_id' => $this->conversionId,
                'conversion_currency_code' => $this->conversionCurrencyCode,
                'conversion_label' => $this->conversionLabel
            ];
            $tagsToCreate = array_merge($tagsToCreate, $this->apiConversionTracking->getConversionTagsList($triggersMap, $params));
        }
        if ($this->remarketingEnabled) {
            $params = [
                'conversion_code' => $this->remarketingConversionCode,
                'conversion_label' => $this->remarketingConversionLabel
            ];
            $tagsToCreate = array_merge($tagsToCreate, $this->apiRemarketing->getRemarketingTagsList($triggersMap, $params));
        }

        $tagId = 1;
        foreach ($tagsToCreate as $tagName => &$tagOptions) {
            if (isset($tagOptions['parameter'])) {
                foreach ($tagOptions['parameter'] as $key => &$paramOptions) {
                    if (empty($paramOptions)) {
                        unset($tagOptions['parameter'][$key]);
                        continue;
                    }
                    if (isset($paramOptions['type'])) {
                        $paramOptions['type'] = strtoupper($paramOptions['type']);
                    }
                    if (isset($paramOptions['list'])) {
                        foreach ($paramOptions['list'] as &$listOptions) {
                            if (isset($listOptions['type'])) {
                                $listOptions["type"] = strtoupper($listOptions["type"]);
                            }
                            foreach ($listOptions["map"] as &$mapOptions) {
                                if (isset($mapOptions['type'])) {
                                    $mapOptions['type'] = strtoupper($mapOptions['type']);
                                }
                            }
                        }
                    }
                }
            }
            if (isset($tagOptions['tagFiringOption'])) {
                $tagOptions['tagFiringOption'] = strtoupper(preg_replace('/(.)([A-Z])/', '$1_$2', $tagOptions['tagFiringOption']));
            }

            $tagOptions['accountId'] = $this->accountId;
            $tagOptions['containerId'] = $this->containerId;
            $tagOptions['tagId'] = $tagId;
            $tagOptions['fingerprint'] = $this->fingerprint;
            $tagId+=1;
        }

        return $tagsToCreate;
    }
}
