<?php

namespace Groomershop\SwatchTable\Block\Tile;

class Fragment extends \Magento\Catalog\Block\Product\ListProduct
{
  private $product;

  public function getProduct()
  {
    return $this->product;
  }

  public function setProduct($product)
  {
    $this->product = $product;
    return $this;
  }
}