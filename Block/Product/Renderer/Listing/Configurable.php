<?php
namespace Groomershop\SwatchTable\Block\Product\Renderer\Listing;

use Amasty\Conf\Model\ResourceModel\Inventory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\ObjectManager;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Listing\Configurable
{
    public function getProductChildrenTableData()
    {
        $websiteCode = $this->_storeManager->getWebsite()->getCode();
        $products = $this->getAllowProducts();
        $productsSku = array_map(function($product) {
            return $product->getSku();
        }, $products);
        $inventory = ObjectManager::getInstance()->get(Inventory::class);
        $stockValues = $inventory->getStocks($productsSku, $websiteCode);
        $qtyValues = $inventory->getQty($productsSku, $websiteCode);
        $priceCurrency = ObjectManager::getInstance()->get(PriceCurrencyInterface::class);
        $attributesData = $this->swatchHelper->getSwatchAttributesAsArray($this->getProduct());
        $attributes = iterator_to_array($this->helper->getAllowAttributes($this->getProduct()));
        $attributeName = count($attributesData) === 1
            ? current($attributesData)['frontend_label']
            : null;

        $products = array_map(function($product) use(
            $stockValues,
            $qtyValues,
            $priceCurrency,
            $attributes,
            $attributesData,
            $attributeName
        ) {
            $isInStock = (int)($stockValues[$product->getSku()] ?? 0);
            $qty = (float)($qtyValues[$product->getSku()] ?? 0);
            $availability = ($isInStock && $qty > 4)
                ? __('In stock')
                : (
                    $isInStock
                    ? str_replace('%d', $qty, __('%d in stock'))
                    : __('Out of stock')
                );

            $productAttributes = array_filter(
                array_map(function($attribute) use($product, $attributesData, $attributeName) {
                    if (!array_key_exists($attribute->getAttributeId(), $attributesData)) {
                        return;
                    }
                    $attributeData = $attributesData[$attribute->getAttributeId()];
                    $productAttribute = $attribute->getProductAttribute();
                    $productAttributeId = $productAttribute->getId();
                    $productAttributeName = $attributeData['frontend_label'];
                    $productAttributeValueId = $product->getData($productAttribute->getAttributeCode());
                    $productAttributeValueName = $attributeData['options'][$productAttributeValueId];
                    $label = $attributeName
                        ? $productAttributeValueName
                        : $productAttributeName . ': ' . $productAttributeValueName;
                    return [
                        'label' => $label,
                        'attributeId' => $attribute->getAttributeId(),
                        'attributeValueId' => $productAttributeValueId
                    ];
                }, $attributes)
            );

            return [
                'product' => $product,
                'name' => join(', ', array_map(function ($productAttribute) {
                    return $productAttribute['label'];
                }, $productAttributes)),
                'attributes' => $productAttributes,
                'availability' => $availability,
                'price' => $priceCurrency->convertAndFormat(
                    $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue()
                ),
            ];
        }, $products);

        return [
            'products' => $products,
            'attributeName' => $attributeName
        ];
    }
}
