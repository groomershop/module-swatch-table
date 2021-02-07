define(["jquery", "underscore"], function ($, _) {
  return function (targetWidget) {
    $.widget("mage.SwatchRenderer", targetWidget, {
      _create() {
        this._super();

        // Default `_create()` only sets `form:first` as the `productForm`
        // However because of `.cs-swatch-table` we have multiple addtocart forms
        // per one swatch-renderer (`.cs-product-tile`),
        // so we need to change it to `form`, so it finds all the forms.
        this.productForm = this.element
          .parents(this.options.selectorProductTile)
          .find("form");
        this.inProductList = this.productForm.length > 0;
      },

      _RenderControls() {
        this._super();

        // Fill in the value of `.swatch-input`s in the `.cs-swatch-table--multiple`,
        // so that minicart correctly shows the product after it is added to the cart.
        var $widget = this;
        this.element
          .parents(this.options.selectorProductTile)
          .find(".cs-swatch-table--multiple tbody tr")
          .each(function (_i, row) {
            $widget.options.jsonConfig.attributes.forEach(function (attribute) {
              var attributeValueId = row.dataset["attribute-" + attribute.id];
              var input = row.querySelector(
                'form.cs-addtocart .swatch-input[name="super_attribute[' +
                  attribute.id +
                  ']"]'
              );
              if (!input) {
                console.error(
                  "product attribute input couldn't be found for attributeId " +
                    attribute.id +
                    " in ",
                  row
                );
                return;
              }
              $(input).val(attributeValueId);
            });
          });
      },
    });

    return $.mage.SwatchRenderer;
  };
});
