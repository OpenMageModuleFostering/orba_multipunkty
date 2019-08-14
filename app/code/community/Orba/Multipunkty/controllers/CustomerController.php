<?php
/*
 * Controller that handles action from My Account -> Multipunkty section
 * 
 * @author
 * @created
 * 
 * @lastModified 30-11-2013
 * @lastModifiedBy Michał Zabielski <michal.zabielski@orba.pl>
 */
class Orba_Multipunkty_CustomerController extends Mage_Core_Controller_Front_Action {
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();
 
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl))
        {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }
    
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $block = $this->getLayout()->getBlock('multipunkty_account_content');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $data = $this->_getSession()->getCustomerFormData(true);
        $customer = $this->_getSession()->getCustomer();
		
        if (!empty($data)) {
            $customer->addData($data);
        }

        $this->getLayout()->getBlock('head')->setTitle($this->__('MultiPunkty'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
    }
    
    public function editPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('multipunkty/customer');
        }

        if ($this->getRequest()->isPost()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getSession()->getCustomer();

            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_edit')
                ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());

            $errors = array();
            $customerErrors = $customerForm->validateData($customerData);
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
            } else {
                $customerForm->compactData($customerData);
                $errors = array();

                // Validate account and compose list of errors if any
                $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                    $errors = array_merge($errors, $customerErrors);
                }
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('multipunkty/customer');
                return $this;
            }

            try {
                $customer->setConfirmation(null);
                $customer->save();
                $this->_getSession()->setCustomer($customer)
                    ->addSuccess($this->__('Edycja przebiegła pomyślnie.'));

                $this->_redirect('multipunkty/customer');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Wystąpił błąd podczas zapisu danych.'));
            }
        }

        $this->_redirect('multipunkty/customer');
    }
    
    /*
     * Handles register request from Multipunkty Javascript API
     */
    public function registerAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $helper = Mage::helper('multipunkty');
        
        try {
            $postData = $this->getRequest()->getPost();
            
            $customer->setMultipunktyUserid($postData['mpid']);
            $customer->setMultipunktyUserlogin($postData['login']);
            $customer->setMultipunktyActive(1);
            $customer->save();
            
            $this->_getSession()->setCustomer($customer)
                    ->addSuccess($helper->__('User was successfully registered.'));
        }catch (Exception $e) {
            $this->_getSession()->setCustomer($customer)
                    ->addException($e, $helper->__('There was an error when processing your request, try again.'));
        }
        
        $this->_redirect('multipunkty/customer');
    }
    
    /*
     * Handles unregister request from My account -> Multipunkty section
     */
    public function unregisterAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $helper = Mage::helper('multipunkty');
        
        try {
            if(is_null($customer->getMultipunktyUserid()))
            {
                $this->_getSession()->setCustomer($customer)
                    ->addException($helper->__('User already unregistered.'));
            }else{
                $customer->setMultipunktyUserid(null);
                $customer->setMultipunktyUserlogin(null);
                $customer->setMultipunktyActive(0);
                $customer->save();

                $this->_getSession()->setCustomer($customer)
                        ->addSuccess($helper->__('User was successfully unregistered.'));
            }
        }catch (Exception $e) {
            $this->_getSession()->setCustomer($customer)
                    ->addError($e, $helper->__('There was an error when processing your request, try again.'));
        }
        
        $this->_redirect('multipunkty/customer');
    }
}