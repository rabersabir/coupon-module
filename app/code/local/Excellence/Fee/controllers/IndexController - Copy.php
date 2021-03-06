<?php
class Excellence_Fee_IndexController extends Mage_Core_Controller_Front_Action {        
    public function indexAction()
    {
        $this->loadLayout();

        $session = Mage::getSingleton('customer/session');
        $block   = $this->getLayout()->getBlock('customer.stempelkaart');
        $referer = $session->getAddActionReferer(true);
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
            if ($referer) {
                $block->setRefererUrl($referer);
            }
        }

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('wishlist/session');

        $this->renderLayout();
    }
}