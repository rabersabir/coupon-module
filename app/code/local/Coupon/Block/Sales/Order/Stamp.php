<?php
include_once(__DIR__ . '../../../../Model/Sales/coupon/CouponDao.php');
include_once(__DIR__ . '../../../../Model/Sales/coupon/Coupon.php');
include_once(__DIR__ . '../../../../Model/Sales/coupon/CouponSaving.php');
include_once(__DIR__ . '../../../../Model/Sales/coupon/Stamp.php');
include_once(__DIR__ . '../../../../Model/Sales/coupon/CouponState.php');

class Coupon_Fee_Block_Sales_Order_Stamp extends Mage_Core_Block_Template
{
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getStampCard()
    {
        $couponDao = new CouponDao();
        $customerId = Mage::getSingleton('customer/session')->getId();
        $coupons = $couponDao->getOverviewByCustomerId($customerId);

        return $coupons;
    }

    public function getState($state)
    {

        switch ($state) {
            case 1:
                return "Open";

            case 2:
                return "Niet geldig meer";
            case 3:
                return "Vol";
            case 4:
                return "In bestelling";
            case 5:
                return "In bestelling ";
            case 6:
                return "Gebruikt";
        }
    }

    public function getPrimaryShippingAddressEditUrl()
    {
        return Mage::getUrl('customer/address/edit', array('id' => $this->getCustomer()->getDefaultShipping()));
    }
}