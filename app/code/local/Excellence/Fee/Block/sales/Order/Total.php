<?php
include_once (__DIR__.'../../../../Model/Sales/coupon/CouponDao.php');
include_once(__DIR__.'../../../../Model/Sales/coupon/Coupon.php');
include_once(__DIR__.'../../../../Model/Sales/coupon/CouponSaving.php');
include_once(__DIR__.'../../../../Model/Sales/coupon/Stamp.php');
include_once(__DIR__.'../../../../Model/Sales/coupon/CouponState.php');
class Excellence_Fee_Block_Sales_Order_Total extends Mage_Core_Block_Template
{


    public function getStampCard()
    {
        $couponDao = new CouponDao();
        $customerId = Mage::getSingleton('customer/session')->getId();
        $coupons = $couponDao->getOverviewByCustomerId($customerId);
        foreach ($coupons as $coupon) {

            $aantal = $coupon['aantalproducten'];
            $duurste = $coupon['duursteproduct'];
            $value = array_values($coupon);
        }
        return $coupons;
    }
    /**
     * Get label cell tag properties
     *
     * @return string
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get order store object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get value cell tag properties
     *
     * @return string
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize reward points totals
     *
     * @return Enterprise_Reward_Block_Sales_Order_Total
     */
    public function initTotals()
    {
        if ((float) $this->getOrder()->getBaseFeeAmount()) {
            $source = $this->getSource();
            $value  = $source->getFeeAmount();

            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'fee',
                'strong' => false,
                'label'  => Mage::helper('fee')->formatFee($value),
                'value'  => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value
            )));
        }

        return $this;
    }
}
