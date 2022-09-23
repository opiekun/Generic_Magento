<?php

namespace WeltPixel\OwlCarouselSlider\Block\Adminhtml\Banner;

/**
 * Banner block edit form container.
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'WeltPixel_OwlCarouselSlider';
        $this->_controller = 'adminhtml_banner';

        $this->buttonList->update('save', 'label', __('Save Banner'));

        if ($this->getRequest()->getParam('loaded_slider_id')) {
            $this->buttonList->remove('back');
            $this->buttonList->remove('save');
            $this->buttonList->remove('delete');
            $this->buttonList->add(
                'close_window',
                [
                    'label'   => __('Close Window'),
                    'onclick' => 'window.close();',
                ],
                10
            );

            $this->buttonList->add(
                'save_and_continue',
                [
                    'label'   => __('Save and Continue Edit'),
                    'class'   => 'save',
                    'onclick' => 'winSaveAndContinueEdit()',
                ],
                10
            );

            $this->buttonList->add(
                'save_and_close',
                [
                    'label'   => __('Save and Close'),
                    'class'   => 'save_and_close',
                    'onclick' => 'winSaveAndCloseWindow()',
                ],
                10
            );

            $this->_formScripts[] = "
				require(['jquery'], function($){
					$(document).ready(function(){
						var input = $('<input class=\"custom-button-submit\" type=\"submit\" hidden=\"true\" />');
						$(edit_form).append(input);

						window.winSaveAndContinueEdit = function (){
							edit_form.action = '" . $this->getWinSaveAndContinueUrl() . "';
							$('.custom-button-submit').trigger('click');
				        }
			    		window.winSaveAndCloseWindow = function (){
			    			edit_form.action = '" . $this->getWinSaveAndCloseWindowUrl() . "';
							$('.custom-button-submit').trigger('click');
			            }
					});
				});
			";

            $bannerId = $this->getRequest()->getParam('id');

            if ($bannerId) {
                $this->_formScripts[] = 'window.id = ' . $bannerId . ';';
            }

        } else {
            $this->buttonList->add(
                'save_and_continue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init'  => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]
                    ],
                ],
                10
            );
        }

        if ($this->getRequest()->getParam('saveandclose')) {
            $this->_formScripts[] = 'window.close();';
        }
    }

    /**
     * Get save and continue edit url.
     *
     * @return string
     */
    protected function getWinSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save',
            [
                '_current' => true,
                'back'     => 'edit',
                'tab'      => '{{tab_id}}',
                'id'       => $this->getRequest()->getParam('id'),
                'loaded_slider_id' => $this->getRequest()->getParam('loaded_slider_id'),
            ]
        );
    }

    /**
     * Get save and close window Url.
     *
     * @return string
     */
    protected function getWinSaveAndCloseWindowUrl()
    {
        return $this->getUrl(
            '*/*/save',
            [
                '_current' => true,
                'back'     => 'edit',
                'tab'      => '{{tab_id}}',
                'id'       => $this->getRequest()->getParam('id'),
                'loaded_slider_id' => $this->getRequest()->getParam('loaded_slider_id'),
                'saveandclose'     => 1,
            ]
        );
    }
}
