<?php
/*
 * Observer that handles operation after order is placed.
 * 
 * Description of status numbers in transaction DB:
 * 1 - User have account in MultiPunkty service, it's registered in store and
 *      API request was completed with success. Then MP$ are automatically added to him, 
 *      without clicking on assign URL.
 * 2 - User have account in MultiPunkty, but assigning transaction to user went false.
 *      MP$ were not added and user will obtain assign URL
 * 3 - User is not logged in store - he received assign URL and none attempt to
 *      register MP$ was started
 * 
 * This class is created based on deleted Transaction/Observer.php file
 * 
 * @author
 * 
 * @created
 * @lastModified 6-12-2013
 * @lastModifiedBy Michał Zabielski <michal.zabielski@orba.pl>
 */
class Orba_Multipunkty_Model_Observer extends Mage_Core_Model_Abstract {
    
    /*
     * Method that handles transaction
     */
    protected function _handleTransaction($observer)
    {
        $event = $observer->getEvent();
        $order = $event->getOrder();
        
        $helper = Mage::helper('multipunkty');

        $total = $order->getSubtotal();
        $reward = $helper->getPurchaseReward();

        $paymentCode = $order->getPayment()->getMethodInstance()->getCode();
        $cashOnDelivery = (!$helper->isCashOnDeliveryAccepted() && $paymentCode == 'cashondelivery') ? false : true;
        $points = ($helper->getPointsAmount() == 0) ? false : true;
        
        if ($helper->isActive() AND $helper->getPurchaseReward() > 0 AND $helper->serviceActive() AND $cashOnDelivery AND $points)
        {
            $points = ceil($total*$reward);

            $api = Mage::getModel('multipunkty/api_client');
			
            $partner_id = $helper->getPartnerId();
            
            $value = $total * 100;
            
            //registering transaction in MultiPunkty service
            $data = (array) $api->registerTransaction($partner_id, $order->getId(), (int)$points, $value);
            
            if (is_array($data))
            {

                //save transaction in database for future use
                $newMultipunkty = Mage::getModel('multipunkty/transaction');
                $newMultipunkty->setOrderId($order->getId());
                $newMultipunkty->setPoints($points);
                $newMultipunkty->setValue($value);
                $newMultipunkty->setTransactionHash($data['transactionId']);
                $newMultipunkty->setAssignUrl($data['assignUrl']);
                //accept transaction
                $api->changeTransactionStatus($data['transactionId'], $helper->getTransactionStatus('Accept'));
                
                //check if user is logged in
                if(Mage::getSingleton('customer/session')->isLoggedIn())
                {
                    $customerData = Mage::getSingleton('customer/session')->getCustomer();

                    //if MPID exist
                    if ($customerData->multipunkty_userid)
                    {
                        if ($api->assignTransactionToUser($data['transactionId'], $customerData->getMultipunktyUserId()))
                        {
                            $newMultipunkty->setStatus(1);
                        }
                        else
                        {
                            $newMultipunkty->setStatus(2);
                        }
                    }
                }
                else
                {
                    $newMultipunkty->setStatus(3);
                }

                $newMultipunkty->save();

            }

        }
    }
    
    /*
     * Method that sends an email after order is placed
     */
    protected function _handleEmailSending($observer)
    {
        $helper = Mage::helper('multipunkty');
        $order = $observer->getEvent()->getOrder();
        $receiveEmail = $order->getCustomerEmail();
        $receiveName = $order->getCustomerName();
        $storeId = Mage::app()->getStore()->getId();
        $vars = Array('order' => $order);
        $mailSubject = $helper->__('%s: You received MultiPoints for an order #%d', Mage::app()->getStore()->getFrontendName(), $order->getIncrementId());
        
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('multipunkty_email_template');
        $emailTemplate->setSenderName(Mage::getStoreConfig('general/store_information/name', $storeId));
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
        $emailTemplate->setTemplateSubject($mailSubject, $storeId);
        
        $emailTemplate->send($receiveEmail,$receiveName, $vars);
    }
    
    public function placeOrder($observer)
    {
        $helper = Mage::helper('multipunkty');
        
        if($helper->isActive() && $helper->serviceActive())
        {
            $this->_handleTransaction($observer);
            
            $order = $observer->getEvent()->getOrder();
            $payment_code = $order->getPayment()->getMethodInstance()->getCode();
            
            if(($payment_code != 'cashondelivery') OR ($payment_code == 'cashondelivery' AND $helper->isCashOnDeliveryAccepted()))
            {
                $this->_handleEmailSending($observer);
            }
        }
    }
}
