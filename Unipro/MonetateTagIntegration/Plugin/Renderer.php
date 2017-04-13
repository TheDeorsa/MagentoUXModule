<?php

namespace Unipro\MonetateTagIntegration\Plugin;

class Renderer
{
    /**
     * @var \Monetate\TrackingTag\Helper\Data
     */
    protected $_helper;

    /**
     * Breadcrumbs constructor.
     *
     * @param \Unipro\MonetateTagIntegration\Helper\Data $helper
     */
    public function __construct(\Unipro\MonetateTagIntegration\Helper\Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * afterRenderHeadContent
     *
     * @param \Magento\Framework\View\Page\Config\Renderer $subject
     * @param $result
     * @return string
     */
    public function afterRenderHeadContent(\Magento\Framework\View\Page\Config\Renderer $subject, $result)
    {

        $loopAndPush = '<script>
        var dataToMonetate = function () {
            for (key in UNI) {
                if (key !== "user") {
                    window.monetateQ.push([key, UNI[key]]);
                }
            }
            window.monetateQ.push(["trackData"]);
            return "function return";
        }
        </script>';

        if (!$this->_helper->isTagAvailable()) {
            return $result;
        }

        return $this->_helper->getTag() . $loopAndPush . $result;
    }
}
