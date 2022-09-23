<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://magento.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_TabGrid
 * @copyright Copyright (C) 2017 Magezon (https://magezon.com)
 */

namespace Magezon\TabGrid\Block\Widget\Grid\Massaction;

class Additional extends \Magezon\TabGrid\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Framework\View\Layout\Argument\Interpreter\Options
     */
    protected $_optionsInterpreter;

    /**
     * @param \Magezon\TabGrid\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\View\Layout\Argument\Interpreter\Options $optionsInterpreter
     * @param array $data
     */
    public function __construct(
        \Magezon\TabGrid\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\View\Layout\Argument\Interpreter\Options $optionsInterpreter,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_optionsInterpreter = $optionsInterpreter;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        foreach ($this->getData('fields') as $itemId => $item) {
            $this->_prepareFormItem($item);
            $form->addField($itemId, $item['type'], $item);
        }
        $this->setForm($form);
        return $this;
    }

    /**
     * Prepare form item
     *
     * @param array &$item
     * @return void
     */
    protected function _prepareFormItem(array &$item)
    {
        if ($item['type'] == 'select' && is_string($item['values'])) {
            $modelClass = $item['values'];
            $item['values'] = $this->_optionsInterpreter->evaluate(['model' => $modelClass]);
        }
        $item['class'] = isset($item['class']) ? $item['class'] . ' absolute-advice' : 'absolute-advice';
    }
}
