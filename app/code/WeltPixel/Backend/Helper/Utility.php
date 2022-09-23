<?php

namespace WeltPixel\Backend\Helper;

use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use \Magento\Store\Api\StoreRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Utility extends \Magento\Framework\App\Helper\AbstractHelper
{

    /** @var  ThemeProviderInterface */
    protected $themeProvider;

    /** @var  StoreRepositoryInterface */
    protected $storeRepository;

    /** @var array  */
    protected $storeThemesLocales = [];

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param ThemeProviderInterface $themeProvider
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        ThemeProviderInterface $themeProvider,
        StoreRepositoryInterface $storeRepository
    )
    {
        parent::__construct($context);
        $this->themeProvider = $themeProvider;
        $this->storeRepository = $storeRepository;
    }

    public function isPearlThemeUsed($storeCode = null)
    {
        $themeId = $this->scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeCode
        );

        $theme = $this->themeProvider->getThemeById($themeId);
        $isPearlTheme = $this->_validatePearlTheme($theme);
        return $isPearlTheme;
    }

    /**
     * @param \Magento\Theme\Model\Theme $theme
     * @return bool
     */
    protected function _validatePearlTheme($theme)
    {
        $pearlThemePath = 'Pearl/weltpixel';
        do {
            if ($theme->getThemePath() == $pearlThemePath) {
                return true;
            }
            $theme = $theme->getParentTheme();
        } while ($theme);

        return false;
    }

    /**
     * @return array
     */
    public function getStoreThemesLocales() {
        if (count($this->storeThemesLocales)) {
            return $this->storeThemesLocales;
        }

        $stores = $this->storeRepository->getList();
        $result = [];
        foreach ($stores as $store) {
            $storeId = $store["store_id"];
            if (!$storeId) continue;
            $storeCode = $store["code"];
            $themeId = $this->scopeConfig->getValue(
                \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeCode
            );

            $locale = $this->scopeConfig->getValue(
                \Magento\Directory\Helper\Data::XML_PATH_DEFAULT_LOCALE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeCode
            );

            $theme = $this->themeProvider->getThemeById($themeId);
            $result[$theme->getThemePath().'/'.$locale] = $storeCode;
        }

        $this->storeThemesLocales = $result;
        return $this->storeThemesLocales;
    }
}
