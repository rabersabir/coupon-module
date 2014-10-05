<?php

/**
 * Created by PhpStorm.
 * User: zuzana
 * Date: 31-03-14
 * Time: 20:48
 */

//include 'Coupon.php';
class Coupon
{




    protected $couponId;
    protected $requiredNumberOfProducts;
    protected $brand;
    protected $validity;
    protected $minPrice;


    function __construct()
    {

    }


    public static function  fillWithData(array $couponDbRecord)
    {

        $instance = new self();
        $instance->couponId =$couponDbRecord["coupon_id"];
        $instance->brand =$couponDbRecord["brand"];
        $instance->validity =$couponDbRecord["validity"];
        $instance->requiredNumberOfProducts=$couponDbRecord["number_of_products"];
        $instance->minPrice=$couponDbRecord["min_price"];


        return $instance;

    }

    /**
     * @param mixed $minPrice
     */
    public function setMinPrice($minPrice)
    {
        $this->minPrice = $minPrice;
    }

    /**
     * @return mixed
     */
    public function getMinPrice()
    {
        return $this->minPrice;
    }



    /**
     * @param mixed $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param mixed $couponId
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
     * @param mixed $validity
     */
    public function setValidity($validity)
    {
        $this->validity = $validity;
    }

    /**
     * @return mixed
     */
    public function getValidity()
    {
        return $this->validity;
    }


}