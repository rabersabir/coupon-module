<?php

class Coupon_Fee_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function formatFee($amount){
		return Mage::helper('fee')->__('Fee');
	}
}