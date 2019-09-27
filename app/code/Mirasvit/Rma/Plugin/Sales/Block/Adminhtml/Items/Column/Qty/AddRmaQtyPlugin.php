<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rma
 * @version   2.0.53
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Rma\Plugin\Sales\Block\Adminhtml\Items\Column\Qty;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Block\Adminhtml\Items\Column\Qty;

class AddRmaQtyPlugin
{
    public function __construct(
        \Mirasvit\Rma\Api\Repository\ItemRepositoryInterface $itemRepository,
        \Mirasvit\Rma\Api\Service\Item\ItemManagement\QuantityInterface $itemQtyService
    ) {
        $this->itemRepository = $itemRepository;
        $this->itemQtyService = $itemQtyService;
    }

    /**
     * @param Qty $qtyColumn
     * @param \callable $proceed
     * @return string
     */
    public function aroundToHtml(Qty $qtyColumn, $proceed)
    {
        $html = $proceed();

        $item = $qtyColumn->getItem();

        if ($item) {
            try {
                $rmaItem = $this->itemRepository->getByOrderItemId($item->getId());
                $html .= $this->getRmaItemQtyHtml($rmaItem);
            } catch (NoSuchEntityException $e) {}
        }

        return $html;
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\ItemInterface $rmaItem
     * @return string
     */
    private function getRmaItemQtyHtml($rmaItem)
    {
        $qty = $this->itemQtyService->getItemQtyReturned($rmaItem);
        return '
        <table class="qty-table">
            <tr>
                <th>' . __('RMA') . '</th>
                <td>' . $qty . '</td>
            </tr>
        </table>
        ';
    }
}