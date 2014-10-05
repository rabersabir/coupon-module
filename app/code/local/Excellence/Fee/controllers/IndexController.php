<?php

include_once (__DIR__.'../../Model/Sales/coupon/CouponDao.php');
include_once(__DIR__.'../../Model/Sales/coupon/Coupon.php');
include_once(__DIR__.'../../Model/Sales/coupon/CouponSaving.php');
include_once(__DIR__.'../../Model/Sales/coupon/Stamp.php');
include_once(__DIR__.'../../Model/Sales/coupon/CouponState.php');

class Excellence_Fee_IndexController extends Mage_Core_Controller_Front_Action {        
    
	public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();
        $customerId=Mage::getSingleton('customer/session')->getId();
        $couponDao = new CouponDao();

        $coupons=$couponDao->getOverviewByCustomerId($customerId);

        foreach($coupons as $coupon){

            $aantal=$coupon['aantalproducten'];
            $duurste=$coupon['duursteproduct'];
            $value=array_values($coupon);
            echo(" copuon  $aantal en $duurste");
            Mage::log("data $value", null, 'custom.log');
       }

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }       
	public function indexAction()
    {
		$this->loadLayout();
		
		$customerId=Mage::getSingleton('customer/session')->getId();
		$couponDao = new CouponDao();
		
		$coupons=$couponDao->getOverviewByCustomerId($customerId);
		
        #$this->_initLayoutMessages('customer/session');
        #$this->_initLayoutMessages('catalog/session');

        #$this->getLayout()->getBlock('content')->append(
        #    $this->getLayout()->createBlock('customer/account_dashboard')
        #);
        #$this->getLayout()->getBlock('head')->setTitle($this->__('My Account'));
        $this->renderLayout();
    }
	public function getCoupons(){
		$couponDao = new CouponDao();
		$customerId=Mage::getSingleton('customer/session')->getId();
		$coupons=$couponDao->getOverviewByCustomerId($customerId);
		return $coupons;
	}
}