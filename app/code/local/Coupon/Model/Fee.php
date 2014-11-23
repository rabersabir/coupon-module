<?php

include_once(__DIR__ . '/Sales/coupon/CouponDao.php');
include_once(__DIR__ . '/Sales/coupon/Coupon.php');
include_once(__DIR__ . '/Sales/coupon/CouponSaving.php');
include_once(__DIR__ . '/Sales/coupon/Stamp.php');
include_once(__DIR__ . '/Sales/coupon/CouponState.php');


class Coupon_Fee_Model_Fee extends Varien_Object
{
	protected $feeMount = 0;

	protected $productName = '';

	protected $productId=-1;



	protected $coupon = false;

	protected $manufacturerName = '';


	public function  getBrand()
	{

		return $this->manufacturerName;
	}

	public function getFee()
	{
		return $this->feeMount;
	}


	public function hasCouponRule()
	{
		return $this->coupon;
	}

	public function getProductName()
	{
		return $this->productName;
	}


	/**
	 * @param int $productId
	 */
	public function setProductId($productId)
	{
		$this->productId = $productId;
	}

	/**
	 * @return int
	 */
	public function getProductId()
	{
		return $this->productId;
	}
	public function canApply($item, $orderId,$totalAmount)
	{

		Mage::log("canApply items ", null, 'custom.log');
		$this->productId=$item->getProductId();
		$_product = Mage::getModel('catalog/product')->load($item->getProductId());
		$this->manufacturerName = $_product->getAttributeText('manufacturer');
		$couponDao = new CouponDao();
		$customerId=Mage::getSingleton('customer/session')->getId();
		$coupon = $couponDao->loadCouponByBrand($this->manufacturerName);

		if ($customerId!=null && $coupon != null) {

			Mage::log("can apply: load completed  ", null, 'custom.log');

			$couponSaving = $couponDao->loadCompletedCouponSaving($coupon->getCouponId(),$customerId);
			Mage::log($couponSaving, null, 'custom.log');

			if ($couponSaving == null) {
				Mage::log($couponSaving, null, 'custom.log');
				Mage::log("can apply: completed was null  ", null, 'custom.log');
				$couponSaving = $couponDao->loadInCartCouponSaving($coupon->getCouponId(), $customerId);
			}

			if ($couponSaving != null) {

				list($requiredNumberOfProducts, $numberOfProductsOrdered, $maxPaid) = $couponDao->getNumberOfProductsOrderdForSavingCoupon($couponSaving,$customerId);
				Mage::log("stap 1   ", null, 'custom.log');
					
				if ($requiredNumberOfProducts != 0) {
					Mage::log("stap 2  ", null, 'custom.log');

					$this->coupon = true;
					Mage::log($maxPaid >= $_product->getPrice(), null, 'custom.log');
					
					Mage::log("max paid $maxPaid    ", null, 'custom.log');
					Mage::log("product->getPrice()".  $_product->getPrice(), null, 'custom.log');
					if ($numberOfProductsOrdered >= $requiredNumberOfProducts && $maxPaid >= $_product->getPrice()) {
						Mage::log("stap 3   ", null, 'custom.log');

						$this->productName = $_product->getName();
						$this->feeMount = -1 * $totalAmount;
						$couponDao->setCouponSavingInUse($couponSaving, $totalAmount,$orderId  );
							
						Mage::log("Komt in aanmerking voor gratis product" . $this->productName . " price" . $totalAmount, null, 'custom.log');

						return true;
					}
				} else {
					$this->coupon = false;
				}
			}


		}

		Mage::log("Komt niet in aanmerking voor gratis product", null, 'custom.log');
		return false;
	}


}