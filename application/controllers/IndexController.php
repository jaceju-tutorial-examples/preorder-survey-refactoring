<?php
class IndexController extends Zend_Controller_Action
{
    protected $_decorationDisplayNameList = array(
        'kitchen' => '廚房',
        'bathroom' => '浴室',
    );

    public function indexAction()
    {
    }

    protected function _createDecorationTable($decorationName)
    {
        $tableName = 'Application_Model_DbTable_Decoration' . ucfirst($decorationName) . 's';
        return new $tableName();
    }

    public function step2Action()
    {
        $decorationName = strtolower($this->getRequest()->getParam('decoration'));
        $error = false;
        $messenger = $this->_helper->flashMessenger;
        /* @var $messenger Zend_Controller_Action_Helper_FlashMessenger */

        if ($this->getRequest()->isPost()) {

            $filter = new Zend_Filter_StripTags();
            $callback = array($filter, 'filter');
            $formData = array_map($callback, $this->getRequest()->getPost());
            $formData = array_map('trim', $formData);

            // 檢查表單必填值
            $checkFunctionName = '_check' . ucwords(strtolower($decorationName)) . 'FormData';
            $this->$checkFunctionName($formData, $error, $messenger);

            if (!$error) {
                $decorationTable = $this->_createDecorationTable($decorationName);
                $decorationRow = $decorationTable->createRow($formData);
                $decorationRow->save();
                $this->_helper->redirector->gotoSimple('step3', null, null, array(
                    'decoration' => $decorationName,
                    'id' => $decorationRow->id,
                ));
            } else {
                $params = array(
                    'decoration' => $decorationName,
                );
                $this->_helper->redirector->gotoSimple('step2', null, null, $params);
            }
        }

        $this->view->decorationName = $decorationName;
        $this->view->decorationDisplayName =
                $this->_decorationDisplayNameList[strtolower($decorationName)];
        $this->view->messages = $messenger->getMessages();
    }

    protected function _checkKitchenFormData($formData, &$error, Zend_Controller_Action_Helper_FlashMessenger &$messenger)
    {
        if (0 === strlen($formData['name'])) {
            $error = true;
            $messenger->addMessage('請輸入姓名');
        }
        if (0 === strlen($formData['phone'])) {
            $error = true;
            $messenger->addMessage('請輸入電話');
        }
        if (0 === strlen($formData['address'])) {
            $error = true;
            $messenger->addMessage('請輸入地址');
        }
        if (!array_key_exists('kitchenQuestion01', $formData)
                && !array_key_exists('kitchenQuestion02', $formData)) {
            $error = true;
            $messenger->addMessage('請選擇裝修內容');
        }
        if (!array_key_exists('kitchenQuestion03', $formData)
                && !array_key_exists('kitchenQuestion04', $formData)) {
            $error = true;
            $messenger->addMessage('請選擇設備是否保留');
        }
        if (!array_key_exists('kitchenQuestion05', $formData)) {
            $error = true;
            $messenger->addMessage('請選擇現有廚具');
        }
    }

    protected function _checkBathroomFormData($formData, &$error, Zend_Controller_Action_Helper_FlashMessenger &$messenger)
    {
        if (0 === strlen($formData['name'])) {
            $error = true;
            $messenger->addMessage('請輸入姓名');
        }
        if (0 === strlen($formData['phone'])) {
            $error = true;
            $messenger->addMessage('請輸入電話');
        }
        if (0 === strlen($formData['address'])) {
            $error = true;
            $messenger->addMessage('請輸入地址');
        }
        if (!array_key_exists('bathroomQuestion01', $formData)) {
            $error = true;
            $messenger->addMessage('請選擇坪數');
        }
        if (!array_key_exists('bathroomQuestion02', $formData)) {
            $error = true;
            $messenger->addMessage('請選擇馬桶');
        }
        if (!array_key_exists('bathroomQuestion03', $formData)) {
            $error = true;
            $messenger->addMessage('請選擇面盆');
        }
    }

    public function step3Action()
    {
        $decorationName = strtolower($this->getRequest()->getParam('decoration'));
        $decorationId = (int) $this->getRequest()->getParam('id');

        $decorationTable = $this->_createDecorationTable($decorationName);
        $decorationRow = $decorationTable->find($decorationId)->current();

        if (!$decorationRow) {
            $this->_redirect('/');
        }

        $this->view->decorationName = $decorationName;
        $this->view->decorationDisplayName =
                $this->_decorationDisplayNameList[strtolower($decorationName)];
        $this->view->decorationRow = $decorationRow;
        $this->view->decorationMap = $this->_exportMapData($decorationName);
        $this->view->actionController = $this;
    }

    protected function _exportMapData($decorationName) // _getDecorationMap
    {
        $includeFile = __DIR__ . '/DecorationMap/' . $decorationName . '.php';
        if (!file_exists($includeFile)) {
            $this->_redirect('/');
        } else {
            $decorationMap = include($includeFile);
            return $decorationMap;
        }
    }

    public function buildOptionString($data, $yn)
    {
        return isset($data[$yn]) ? $data[$yn] : null;
    }
}

