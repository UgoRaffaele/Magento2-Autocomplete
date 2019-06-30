<?php

namespace UgoRaffaele\Autocomplete\Block;

class Color extends \Magento\Config\Block\System\Config\Form\Field {

    /**
    @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
    Input  : add color picker in admin configuration fields
    Output : return string script
     */

    public function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {

        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
            require(["jquery","ugoraffaele/colorpicker"], function ($) {
                $(document).ready(function () {
                    var $el = $("#'.$element->getHtmlId().'");
                    $el.css("backgroundColor", "'.$value.'");

                    // Attach the color picker
                    $el.ColorPicker({
                        color: "'.$value.'",
                        onChange: function (hsb, hex, rgb) {
                            $el.css("backgroundColor", "#" + hex).val("#" + hex);
                        }
                    });
                });
            });
        </script>';

        return $html;

    }
    
}
