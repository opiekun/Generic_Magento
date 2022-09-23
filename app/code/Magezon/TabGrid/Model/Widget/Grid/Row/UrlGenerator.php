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

namespace Magezon\TabGrid\Model\Widget\Grid\Row;

class UrlGenerator implements \Magezon\TabGrid\Model\Widget\Grid\Row\GeneratorInterface
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_urlModel;

    /**
     * @var string
     */
    protected $_path;

    /**
     * @var array
     */
    protected $_params = [];

    /**
     * @var array
     */
    protected $_extraParamsTemplate = [];

    /**
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param array $args
     * @throws \InvalidArgumentException
     */
    public function __construct(\Magento\Backend\Model\UrlInterface $backendUrl, array $args = [])
    {
        if (!isset($args['path'])) {
            throw new \InvalidArgumentException('Not all required parameters passed');
        }
        $this->_urlModel = isset($args['urlModel']) ? $args['urlModel'] : $backendUrl;
        $this->_path = (string)$args['path'];
        if (isset($args['params'])) {
            $this->_params = (array)$args['params'];
        }
        if (isset($args['extraParamsTemplate'])) {
            $this->_extraParamsTemplate = (array)$args['extraParamsTemplate'];
        }
    }

    /**
     * Create url for passed item using passed url model
     *
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function getUrl($item)
    {
        if (!empty($this->_path)) {
            $params = $this->_prepareParameters($item);
            return $this->_urlModel->getUrl($this->_path, $params);
        }
        return '';
    }

    /**
     * Convert template params array and merge with preselected params
     *
     * @param \Magento\Framework\DataObject $item
     * @return array
     */
    protected function _prepareParameters($item)
    {
        $params = [];
        foreach ($this->_extraParamsTemplate as $paramKey => $paramValueMethod) {
            $params[$paramKey] = $item->{$paramValueMethod}();
        }
        return array_merge($this->_params, $params);
    }
}
