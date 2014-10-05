<?php

include_once (__DIR__.'/../../../coupon/CouponDao.php');
include_once(__DIR__.'/../../../coupon/Coupon.php');
include_once(__DIR__.'/../../../coupon/CouponSaving.php');
include_once(__DIR__.'/../../../coupon/Stamp.php');
include_once(__DIR__.'/../../../coupon/CouponState.php');

class Excellence_Fee_Model_Sales_Order_Total_Invoice_Fee extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	/**
	 * Enter description here ...
	 * @param Mage_Sales_Model_Order_Invoice $invoice
	 * @return Excellence_Fee_Model_Sales_Order_Total_Invoice_Fee
	 */
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{


		
		return $this;
		}
	}
