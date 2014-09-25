<?php
/**
 * Created by PhpStorm.
 * User: zuzana
 * Date: 31-03-14
 * Time: 20:48
 */

class Stamp {

    protected  $stampId;
    protected  $couponSavingId;
    protected  $purchasedDate;
    protected  $purchasedProductPrice;
    protected  $purchasedproductName;



    function __construct($couponSavingId, $purchasedProductPrice, $purchasedproductName)
    {
        $this->couponSavingId = $couponSavingId;
        $this->purchasedProductPrice = $purchasedProductPrice;
        $this->purchasedproductName = $purchasedproductName;
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
     * @param mixed $priceItem
     */
    public function setPriceItem($priceItem)
    {
        $this->priceItem = $priceItem;
    }

    /**
     * @return mixed
     */
    public function getPriceItem()
    {
        return $this->priceItem;
    }

    /**
     * @param mixed $productName
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;
    }

    /**
     * @return mixed
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @param mixed $stampId
     */
    public function setStampId($stampId)
    {
        $this->stampId = $stampId;
    }

    /**
     * @return mixed
     */
    public function getStampId()
    {
        return $this->stampId;
    }

} 