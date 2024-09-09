<?php
namespace Abs\CustomerPasswordUpdate\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class PasswordUpdateHtml extends Column
{
    protected $escaper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (array_key_exists('entity_id', $item)) {
                    $entityId = $item['entity_id'];
                    $html = '<div class="admin__field _required">
                                            <input type="password" class="admin__control-text updatepasswordtextbox" name="field_name" id="textbox-'.$entityId.'" data-value="'.$entityId.'">
                                            <a id="btn-'.$entityId.'" href="javascript:void(0)" data-value="'.$entityId.'" class="adminPassword action-default primary add">Update</a>
                                            <span class="msg" id="msg-'.$entityId.'"></span>
                                        </div>';
                        $item[$fieldName] = $html;
                }
            }
        }
        return $dataSource;
    }
}
