<?php

class Excellence_Fee_Model_Sales_Quote_Address_Total_Fee extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	protected $_code = 'fee';
	protected $_amount = 0;

	protected $feeMount = -1;

	protected $productName = '';

	protected $manufacturerName = '';
	protected $hasCouponRule = false;
	protected $added = false;


	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);
		$quote = $address->getQuote();


		$this->_setAmount(0);
		$this->_setBaseAmount(0);

		$items = $this->_getAddressItems($address);
		if (!count($items)) {
			return $this; //this makes only address type shipping to come through
		}

		$obj = new Excellence_Fee_Model_Fee();
		$this->added = false;






		
		$this->added = false;
		//			if ($obj->canApply($item)) {
		//
		//				$discountAmount= $obj->getFee();
		//
		//				$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
		//
		//
		//
		//
		//				$finalPrice =0;
		//				$item->setPrice();
		//
		//				$item->setCustomPrice($finalPrice);
		//				$item->setOriginalCustomPrice($finalPrice);
		//
		//				$item->setPrice($finalPrice)
		//				->setBaseOriginalPrice($finalPrice);
		//				$item->calcRowTotal();
		//				$item->getProduct()->setIsSuperMode(true);
		//				// $quote_item->setMessage("korting voor volle stampkart");
		//
		//
		//				$item->save();
		//			}
		$cartItems = $quote->getAllVisibleItems();


/*
		foreach ($cartItems as $item) {

			$productName = $item->getProduct()->getName();
			$productPrice = $item->getProduct()->getPrice();
			if ($obj->canApply($item)) {

			//	$this->updateCart($obj->getProductId());
				$this->added = false;

				$exist_amount = $quote->getFeeAmount();
				Mage::log(" in canApply check  $exist_amount", null, custom . log);

				$this->feeMount = $obj->getFee();
				$this->productName = $obj->getProductName();
				$balance = $this->feeMount - $exist_amount;

				/*$address->setFeeAmount($balance);
				$address->setGrandTotal($address->getGrandTotal() + $address->getFeeAmount());
				$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseFeeAmount());
				
				$item->setDiscountAmount($this->feeMount);
				
			}
		}
		$this->hasCouponRule = $obj->hasCouponRule();
		$this->manufacturerName = $obj->getBrand();
		*/
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		
		if ($this->hasCouponRule) {
			if ($this->added) {
				Mage::log("Het is al toegevoegd. ", null, custom . log);
				return $this;

			}
			$this->added = true;

			Mage::log("in fetch " . $this->productName, null, custom . log);
			Mage::log($this->feeMount, null, custom . log);
			if ($this->feeMount >= 0) {
				$address->addTotal(array(
                    'code' => $this->getCode(),
                    'title' => Mage::helper('fee')->__('een punt gespaart voor ' . $this->manufacturerName),
                    'value' => '0'
                    ));

			} else {
				$address->addTotal(array(
                    'code' => $this->getCode(),
                    'title' => Mage::helper('fee')->__('I.v.m. volle stample kaart'),
                    'value' => '0'
				));

			}
			$address->save();
			return $this;
		} else {

			Mage::log("This product had no coupon rule " . $this->manufacturerName, null, 'custom' . 'log');
			return $this;
		}

		return this;
	}



}