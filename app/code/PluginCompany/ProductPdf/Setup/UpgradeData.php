<?php
/**
 * Created by:  Milan Simek
 * Company:     Plugin Company
 *
 * LICENSE: http://plugin.company/docs/magento-extensions/magento-extension-license-agreement
 *
 * YOU WILL ALSO FIND A PDF COPY OF THE LICENSE IN THE DOWNLOADED ZIP FILE
 *
 * FOR QUESTIONS AND SUPPORT
 * PLEASE DON'T HESITATE TO CONTACT US AT:
 *
 * SUPPORT@PLUGIN.COMPANY
 */
namespace PluginCompany\ProductPdf\Setup;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var CollectionFactory
     */
    private $configCollectionFactory;
    /**
     * @var WriterInterface
     */
    private $configWriter;
    /**
     * @var TypeListInterface
     */
    private $cacheTypeList;

    public function __construct(
        CollectionFactory $configCollectionFactory,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList
    ) {
        $this->configCollectionFactory = $configCollectionFactory;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->addQrCodeToSectionSort();
        $setup->endSetup();
    }

    private function addQrCodeToSectionSort()
    {
        $collection = $this->configCollectionFactory->create();
        $collection->addFieldToFilter(
            'path',
            'plugincompany_productpdf/sectionsort/sort_order'
        );
        foreach($collection as $item) {
            /** @var $item Value */
            $value = json_decode($item->getValue(), true);
            $value['qr-code'] = 'QR Code';
            $value = json_encode($value);
            $this->configWriter->save($item->getPath(), $value, $item->getScope(), $item->getScopeId());
        }
        $this->cacheTypeList
            ->cleanType('config')
        ;
        return $this;
    }

}

