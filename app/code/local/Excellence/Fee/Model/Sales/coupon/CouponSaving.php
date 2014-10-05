<?php

/**
 * Created by PhpStorm.
 * User: zuzana
 * Date: 31-03-14
 * Time: 20:48
 */

//include 'CouponState.php';
class CouponSaving
{



    protected $state;
    protected $couponSavingId;
    protected $customerId;
    protected $creationDate;
    protected $completedDate;
    protected $usedDate;
    protected $validUntil;
    protected $validateState;
    protected $couponId;
    protected $requiredNumberOfProducts;
    protected $mostExpensiveItem;
    protected $orderId;
    protected $discountAmount;
    



    function __construct()
    {

    }


    public static function  startCouponSaving($coupon, $customerId)
    {

        $instance = new self();
        $instance->couponId = $coupon->getCouponId();
        $instance->requiredNumberOfProducts=$coupon->getRequiredNumberOfProducts();
        $validUntil=Date('Y:m:d', strtotime($coupon->getValidity()));
        $instance->customerId = $customerId;
        $instance->creationDate = NOW();
        $instance->validUntil=$validUntil;
        $instance->state = CouponState::OPEN;
      return $instance;

    }


    public static function  fillWithData(array $couponSavingDbRecord)
    {

        //coupon_saving_id | coupon_id | customer | valid_State | valid_Until         | creation_date       | used_date | completed_date      | state
        $instance = new self();
        $instance->couponId =$couponSavingDbRecord["coupon_id"];
        $instance->customerId =$couponSavingDbRecord["customer"];
        $instance->couponSavingId=$couponSavingDbRecord["coupon_saving_id"];
        $instance->creationDate = $couponSavingDbRecord["creation_date"];
        $instance->completedDate = $couponSavingDbRecord["completed_date"];
        $instance->state = $couponSavingDbRecord["state"];
        $instance->validateState = $couponSavingDbRecord["valid_State"];
        $instance->validUntil = $couponSavingDbRecord["valid_Until"];
        $instance->requiredNumberOfProducts=$couponSavingDbRecord["required_number_of_products"];
        $instance->discountAmount=$couponSavingDbRecord["discount_amount"];
        $instance->orderId=$couponSavingDbRecord["order_id"];
        


        return $instance;

    }

    /**
     * @param mixed $couponSavingId
     */
    public function setCouponSavingId($couponSavingId)
    {
        $this->couponSavingId = $couponSavingId;
    }

    /**
     * @return mixed
     */
    public function getCouponSavingId()
    {
        return $this->couponSavingId;
    }

    /**
     * @param mixed $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param mixed $coupon
     */
    public function setCouponId($couponId)
    {
        $this->couponId = $couponId;
    }

    /**
     * @return mixed
     */
    public function getCouponId()
    {
        return $this->couponId;
    }


    /**
     * @param mixed $validUntil
     */
    public function setValidUntil($validUntil)
    {
        $this->validUntil = $validUntil;
    }

    /**
     * @return mixed
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    protected $priceFreeItem;
    protected $productNameFreeItem;


    /**
     * @param mixed $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }


    /**
     * @param mixed $priceFreeItem
     */
    public function setPriceFreeItem($priceFreeItem)
    {
        $this->priceFreeItem = $priceFreeItem;
    }

    /**
     * @return mixed
     */
    public function getPriceFreeItem()
    {
        return $this->priceFreeItem;
    }

    /**
     * @param mixed $productNameFreeItem
     */
    public function setProductNameFreeItem($productNameFreeItem)
    {
        $this->productNameFreeItem = $productNameFreeItem;
    }

    /**
     * @return mixed
     */
    public function getProductNameFreeItem()
    {
        return $this->productNameFreeItem;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $usedDate
     */
    public function setUsedDate($usedDate)
    {
        $this->usedDate = $usedDate;
    }

    /**
     * @return mixed
     */
    public function getUsedDate()
    {
        return $this->usedDate;
    }

    /**
     * @param mixed $validateState
     */
    public function setValidateState($validateState)
    {
        $this->validateState = $validateState;
    }

    /**
     * @return mixed
     */
    public function getValidateState()
    {
        return $this->validateState;
    }

    /**
     * @param mixed $requiredNumberOfProducts
     */
    public function setRequiredNumberOfProducts($requiredNumberOfProducts)
    {
        $this->requiredNumberOfProducts = $requiredNumberOfProducts;
    }

    /**
     * @return mixed
     */
    public function getRequiredNumberOfProducts()
    {
        return $this->requiredNumberOfProducts;
    }

    /**
     * @param mixed $mostExpensiveItem
     */
    public function setMostExpensiveItem($mostExpensiveItem)
    {
        $this->mostExpensiveItem = $mostExpensiveItem;
    }

    /**
     * @return mixed
     */
    public function getMostExpensiveItem()
    {
        return $this->mostExpensiveItem;
    }

     /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
    
     /**
     * @return mixed
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }
    

}