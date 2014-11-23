<?php
include_once (__DIR__.'../../../Model/Sales/coupon/CouponDao.php');
include_once(__DIR__.'../../../Model/Sales/coupon/Coupon.php');
include_once(__DIR__.'../../../Model/Sales/coupon/CouponSaving.php');
include_once(__DIR__.'../../../Model/Sales/coupon/Stamp.php');
include_once(__DIR__.'../../../Model/Sales/coupon/CouponState.php');

class Coupon_Fee_Block_Customer_StampCard extends Mage_Core_Block_Template
{
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getStampCard()
    {
        $stampcard = $this->getCustomer()->getPrimaryShippingAddress();
		$couponDao = new CouponDao();
		$customerId=Mage::getSingleton('customer/session')->getId();
		$coupons=$couponDao->getOverviewByCustomerId($customerId);
        foreach($coupons as $coupon){

            $aantal=$coupon['aantalproducten'];
            $duurste=$coupon['duursteproduct'];
            $value=array_values($coupon);
            }
		return $coupons;
    }

    public function getPrimaryShippingAddressEditUrl()
    {
        return Mage::getUrl('customer/address/edit', array('id'=>$this->getCustomer()->getDefaultShipping()));
    }
}