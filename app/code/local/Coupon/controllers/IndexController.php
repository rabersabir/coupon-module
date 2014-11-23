<?php

include_once(__DIR__ . '../../Model/Sales/coupon/CouponDao.php');
include_once(__DIR__ . '../../Model/Sales/coupon/Coupon.php');
include_once(__DIR__ . '../../Model/Sales/coupon/CouponSaving.php');
include_once(__DIR__ . '../../Model/Sales/coupon/Stamp.php');
include_once(__DIR__ . '../../Model/Sales/coupon/CouponState.php');

class Coupon_Fee_IndexController extends Mage_Core_Controller_Front_Action
{

    public function preDispatch()
    {
        parent::preDispatch();
        $loginUrl = Mage::helper('customer')->getLoginUrl();
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function getCoupons()
    {
        $couponDao = new CouponDao();
        $customerId = Mage::getSingleton('customer/session')->getId();
        $coupons = $couponDao->getOverviewByCustomerId($customerId);
        return $coupons;
    }
}