<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Core
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Block\Adminhtml\Product\Widget;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Conditions extends Template implements RendererInterface
{
    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var \Magento\CatalogWidget\Model\Rule
     */
    protected $rule;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $elementFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var AbstractElement
     */
    protected $element;

    /**
     * @var \Magento\Framework\Data\Form\Element\Text
     */
    protected $input;

    /**
     * @var string
     */
    protected $_template = 'Magento_CatalogWidget::product/widget/conditions.phtml';

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Rule\Block\ConditionsFactory $conditionsFactory,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->elementFactory    = $elementFactory;
        $this->conditions        = $conditions;
        $this->conditionsFactory = $conditionsFactory;
        $this->registry          = $registry;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $this->element = $element;
        return $this->toHtml();
    }

    /**
     * @return string
     */
    public function getNewChildUrl()
    {
        return $this->getUrl(
            'catalog_widget/product_widget/conditions',
            [
                'form'        => $this->getHtmlId(),
                'mgz_builder' => 1
            ]
        );
    }

    /**
     * @return AbstractElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        if ($this->hasData('htmlid')) {
            return $this->getData('htmlid');
        }
        return $this->getElement()->getContainer()->getHtmlId();
    }

    public function getCurrentRule() {
        if (!$this->rule) {
            $this->rule = $this->getRule();
        }
        return $this->rule;
    }

    /**
     * @return string
     */
    public function getInputHtml()
    {
        $rule = $this->getRule();
        $parameters = $this->getData('parameters');
        if ($parameters) {
            if (isset($parameters['conditions'])) {
                $parameters['conditions'] = json_decode($parameters['conditions'], TRUE);
                $rule->loadPost($parameters);
            }
        }
        $this->input = $this->elementFactory->create('text');
        $this->input->setRule($rule)->setRenderer($this->conditions);

        $this->setConditionFormName($rule->getConditions(), $this->getHtmlId(), $this->getHtmlId());
        return $this->input->toHtml();
    }

    /**
     * @param AbstractCondition $conditions
     * @param string $formName
     * @param string $jsFormName
     * @return void
     */
    private function setConditionFormName($conditions, $formName, $jsFormName)
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormName);
            }
        }
    }

    /**
     * Escape string for the JavaScript context
     *
     * @param string $string
     * @return string
     * @since 101.0.0
     */
    public function escapeJs($string)
    {
        return $this->_escaper->escapeJs($string);
    }
}
