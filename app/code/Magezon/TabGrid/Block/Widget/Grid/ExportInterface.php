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

namespace Magezon\TabGrid\Block\Widget\Grid;

interface ExportInterface
{
    /**
     * Retrieve grid export types
     *
     * @return array|bool
     * @api
     */
    public function getExportTypes();

    /**
     * Retrieve grid id
     *
     * @return string
     * @api
     */
    public function getId();

    /**
     * Render export button
     *
     * @return string
     */
    public function getExportButtonHtml();

    /**
     * Add new export type to grid
     *
     * @param   string $url
     * @param   string $label
     * @return  \Magezon\TabGrid\Block\Widget\Grid
     */
    public function addExportType($url, $label);

    /**
     * Retrieve a file container array by grid data as CSV
     *
     * Return array with keys type and value
     *
     * @return array
     * @api
     */
    public function getCsvFile();

    /**
     * Retrieve Grid data as CSV
     *
     * @return string
     * @api
     */
    public function getCsv();

    /**
     * Retrieve data in xml
     *
     * @return string
     * @api
     */
    public function getXml();

    /**
     * Retrieve a file container array by grid data as MS Excel 2003 XML Document
     *
     * Return array with keys type and value
     *
     * @param string $sheetName
     * @return array
     * @api
     */
    public function getExcelFile($sheetName = '');

    /**
     * Retrieve grid data as MS Excel 2003 XML Document
     *
     * @return string
     * @api
     */
    public function getExcel();
}
