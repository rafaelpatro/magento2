<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml catalog product edit action attributes update tab block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Action\Attribute\Tab;

use Magento\Data\Form\Element\AbstractElement;

class Attributes
    extends \Magento\Catalog\Block\Adminhtml\Form
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Helper\Product\Edit\Action\Attribute
     */
    protected $_attributeAction;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeAction
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeAction,
        array $data = array()
    ) {
        $this->_attributeAction = $attributeAction;
        $this->_productFactory = $productFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * @return void
     */
    protected function _prepareForm()
    {
        $this->setFormExcludedFieldList(array(
            'category_ids',
            'gallery',
            'group_price',
            'image',
            'media_gallery',
            'quantity_and_stock_status',
            'recurring_profile',
            'tier_price',
        ));
        $this->_eventManager->dispatch('adminhtml_catalog_product_form_prepare_excluded_field_list', array(
            'object' => $this,
        ));

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('fields', array(
            'legend' => __('Attributes'),
        ));
        $attributes = $this->getAttributes();
        /**
         * Initialize product object as form property
         * for using it in elements generation
         */
        $form->setDataObject($this->_productFactory->create());
        $this->_setFieldset($attributes, $fieldset, $this->getFormExcludedFieldList());
        $form->setFieldNameSuffix('attributes');
        $this->setForm($form);
    }

    /**
     * Retrieve attributes for product mass update
     *
     * @return \Magento\Object[]
     */
    public function getAttributes()
    {
        return $this->_attributeAction
            ->getAttributes()->getItems();
    }

    /**
     * Additional element types for product attributes
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'price' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Price',
            'weight' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight',
            'image' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Image',
            'boolean' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Boolean',
        );
    }

    /**
     * Custom additional element html
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        // Add name attribute to checkboxes that correspond to multiselect elements
        $nameAttributeHtml = $element->getExtType() === 'multiple' ? 'name="' . $element->getId() . '_checkbox"' : '';
        $elementId = $element->getId();
        $checkboxLabel = __('Change');
        $html = <<<HTML
<span class="attribute-change-checkbox">
    <label>
        <input type="checkbox" $nameAttributeHtml onclick="toogleFieldEditMode(this, '{$elementId}')" />
        {$checkboxLabel}
    </label>
</span>
<script>initDisableFields("{$elementId}")</script>
HTML;
        if ($elementId === 'weight') {
            $html .= <<<HTML
<script>jQuery(function($) {
    $('#weight_and_type_switcher, label[for=weight_and_type_switcher]').hide();
});</script>
HTML;
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Attributes');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return __('Attributes');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
