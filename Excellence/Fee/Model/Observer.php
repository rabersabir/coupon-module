<?php

include_once(__DIR__ . '/Sales/coupon/CouponDao.php');
include_once(__DIR__ . '/Sales/coupon/Coupon.php');
include_once(__DIR__ . '/Sales/coupon/CouponSaving.php');
include_once(__DIR__ . '/Sales/coupon/Stamp.php');
include_once(__DIR__ . '/Sales/coupon/CouponState.php');


class Excellence_Fee_Model_Observer
{

    protected $_code = 'fee';
    protected $_amount = 0;

    protected $feeMount = -1;

    protected $productName = '';

    protected $manufacturerName = '';
    protected $hasCouponRule = false;
    protected $added = false;

    public function invoiceSaveAfter(Varien_Event_Observer $observer)
    {
        Mage::log(" : invoiceSaveAfter stap 1   ", null, 'custom.log');

        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getBaseFeeAmount()) {
            $order = $invoice->getOrder();
            $order->setFeeAmountInvoiced($order->getFeeAmountInvoiced() + $invoice->getFeeAmount());
            $order->setBaseFeeAmountInvoiced($order->getBaseFeeAmountInvoiced() + $invoice->getBaseFeeAmount());
        }
        return $this;
    }

    public function onSalesOrderInvoicePay($observer)
    {


        Mage::log(" : onSalesOrderInvoicePay stap 1   ", null, 'custom.log');

        try {


            $couponDao = new CouponDao();

            $invoice = $observer->getEvent()->getInvoice();
            $order = $invoice->getOrder();
            /* @var $item Mage_Sales_Model_Order_Item */
            foreach ($order->getAllItems() as $item) {
                // Do something with $item here...
                $name = $item->getName();
                $price = $item->getPrice();
                $sku = $item->getSku();

                $_product = Mage::getModel('catalog/product')->load($item->getProductId());
                $manufacturerName = $_product->getAttributeText('manufacturer');
                Mage::log($manufacturerName, null);

                $coupon = $couponDao->loadCouponByBrand($manufacturerName);

                if ($coupon != null) {
                    Mage::log($coupon, null);

                    Mage::log("aantal " . $item->getQtyOrdered(), null);
                    Mage::log("item->getOrderId() " . $order->getOrderId(), null);

                    for ($i = 1; $i <= $item->getQtyOrdered(); $i++) {

                        $couponSaving = $couponDao->loadInInVoiceCouponSaving($coupon->getCouponId(), $order->getCustomerId());
                        Mage::log("invoice?", null);
                        if ($couponSaving != null) {
                            Mage::log("Het is niet null", null);
                            Mage::log($couponSaving, null);
                            $couponDao->setCouponSavingToUsed($couponSaving);
                        } else {
                            if ($price >= $coupon->getMinPrice() && !$this->discountCoupnUsed($order, $manufacturerName)) {
                                $couponSaving = $couponDao->loadCouponSaving($coupon->getCouponId(), $order->getCustomerId(), CouponState::OPEN);
                                Mage::log("  Er was niks in kaart.  ", null);

                                if ($couponSaving == null) {
                                    Mage::log(" Hier wordt een nieuw gecreeerd is null", null);
                                    $date = NOW();
                                    $couponSavingNew = CouponSaving:: startCouponSaving($coupon, $order->getCustomerId());
                                    Mage::log(" just created $couponSavingNew  ", null);
                                    $couponSaving = $couponDao->createCouponSaving($couponSavingNew);
                                }
                                //Mage::log($couponSaving, null);
                                $couponSavingId = $couponSaving->getCouponSavingId();
                                if (NOW() >= $couponSaving->getValidUntil()) {
                                    //	echo " het was invalid";
                                    $couponDao->updateCouponSaving($couponSaving);
                                    $couponSavingNew = CouponSaving:: startCouponSaving($coupon, $order->getCustomerId());
                                    $couponSavingNew = $couponDao->createCouponSaving($couponSavingNew);
                                    $couponSavingIdNew = $couponSavingNew->getCouponSavingId();

                                    $couponDao->createStamp($couponSavingIdNew, $name, $price, $order->getIncrementId());

                                } else {

                                    $couponDao->createStamp($couponSavingId, $name, $price, $order->getIncrementId());
                                    $couponDao->updateCouponSaving($couponSaving);
                                }

                            }
                        }
                    }

                }

            }

        } catch (Exception $e) {
            Mage::log("This is product is " + $e, null);
        }
        return $this;
    }


    function discountCoupnUsed($order, $brand)
    {

        $couponCode = $order->getCouponCode();
        if ($couponCode) {
            if (strpos($couponCode, $brand) !== FALSE) {
                return true;
            }
        }
        return false;

    }

    public function  onSaleBeforeOrderPlace($observer)
    {
        //controller_action_predispatch_checkout_onepage_index
    }

    public function onSalesOrderPlaceAfter($observer)
    {

        try {

            $couponDao = new CouponDao();
            $invoice = $observer->getEvent()->getInvoice();
            $order = $observer->getOrder();
            foreach ($order->getAllItems() as $item) {
                $_product = Mage::getModel('catalog/product')->load($item->getProductId());
                $manufacturerName = $_product->getAttributeText('manufacturer');
                Mage::log($manufacturerName, null);

                $coupon = $couponDao->loadCouponByBrand($manufacturerName);

                if ($coupon != null) {
                    Mage::log($coupon, null);

                    $couponSaving = $couponDao->loadInCartCouponSaving($coupon->getCouponId(), $order->getCustomerId());
                    if ($couponSaving != null) {
                        Mage::log("  Er was een in cart voor order $order->getIncrementId()   en $manufacturerName  ", null);
                        $couponDao->setCouponSavingToInVoice($couponSaving, $order->getIncrementId());
                    }
                }
            }
        } catch
        (Exception $e) {
            Mage::log("This is product is " + $e, null);
        }
        return $this;
    }


    public function checktForCouponSaving($observer)
    {

        $couponDao = new CouponDao();
        $customerId = Mage::getSingleton('customer/session')->getId();
        $couponDao->resetInCartCouponSavingsForCustomer($customerId);
    }


    public function setDiscount($observer)
    {


        try {

            $array = array();
            Mage::log(" we zijn in  setDiscount ", null, 'custom.log');

            $quote = $observer->getEvent()->getQuote();
            $quoteId = $quote->getId();
            $cartItems = $quote->getAllVisibleItems();

            $obj = new Excellence_Fee_Model_Fee();
            $customerId = Mage::getSingleton('customer/session')->getId();
            $discountAmount = 0;
            $brand = '';
            $discountDescription = "Voor spaarkaart voor";


            foreach ($cartItems as $item) {

                $productName = $item->getProduct()->getName();
                $productPrice = $item->getProduct()->getPrice();

                $store = Mage::app()->getStore('default');
                $request = Mage::getSingleton('tax/calculation')->getRateRequest(null, null, null, $store);
                $taxclassId = $item->getData('tax_class_id');
                $percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxclassId));
                $tax = ($percent * 0.01) * $productPrice;
                $discountAmount = $productPrice + $tax;



                Mage::log("mogelijk korting  $discountAmount , product prijs $productPrice en btw is $tax ", null, 'custom.log');

                $this->productId = $item->getProductId();
                $_product = Mage::getModel('catalog/product')->load($item->getProductId());
                $brand = $_product->getAttributeText('manufacturer');
                $couponDao = new CouponDao();

                $couponSaving = $couponDao->loadCouponSavingByOrderId($brand, $quoteId, $customerId);

                Mage::log($couponSaving, null, 'custom.log');

                if (!in_array($brand, $array)) {
                    if ($couponSaving != null) {
                        Mage::log("setDiscount : stap voor 1   ", null, 'custom.log');
                        if ($discountAmount > 0) {
                            $discountDescription = "$discountDescription en  $brand";
                        } else {
                            $discountDescription = "$discountDescription voor $brand";
                        }
                        $discountAmount =  -1 * $couponSaving->getDiscountAmount();
                        array_push($array, $brand);
                    } else if ($obj->canApply($item, $quoteId)) {
                        Mage::log("setDiscount : stap voor 2   ", null, 'custom.log');
                        $brand = $obj->getBrand();
                        array_push($array, $brand);
                        Mage::log(" in canApply check for setDiscount  $exist_amount", null, 'custom.log');

                        if ($discountAmount > 0) {
                            $discountDescription = "$discountDescription en  $brand";
                        } else {
                            $discountDescription = "$discountDescription voor $brand";
                        }

                    }
                }


            }
            if ($quoteId) {

                Mage::log("setDiscount : stap 1   ", null, 'custom.log');


                if ($discountAmount > 0) {
                    Mage::log("setDiscount : stap 2   ", null, 'custom.log');


                    //we calculate the Ratio of taxes between GrandTotal & Discount Amount to know how much we need to remove.


                    $quote->setGrandTotal($quote->getGrandTotal() - $discountAmount)
                        ->setBaseGrandTotal($quote->getBaseGrandTotal() - $discountAmount)
                        ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                        ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                        ->save();

                    $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
                    foreach ($quote->getAllAddresses() as $address) {
                        $address->setSubtotal(0);
                        $address->setBaseSubtotal(0);
                        $address->setGrandTotal(0);
                        $address->setBaseGrandTotal(0);
                        $address->collectTotals();
                        if ($address->getAddressType() == $canAddItems) {
                            $address->setSubtotal((float)$quote->getSubtotal());
                            $address->setBaseSubtotal((float)$quote->getBaseSubtotal());
                            $address->setSubtotalWithDiscount((float)$quote->getSubtotalWithDiscount());
                            $address->setBaseSubtotalWithDiscount((float)$quote->getBaseSubtotalWithDiscount());
                            $address->setGrandTotal((float)$quote->getGrandTotal());
                            $address->setBaseGrandTotal((float)$quote->getBaseGrandTotal());
                            $address->setDiscountAmount(-$discountAmount);
                            $address->setDiscountDescription($discountDescription);
                            $address->setBaseDiscountAmount(-$discountAmount);
                            $address->setTaxAmount($tax);
                            $address->setBaseTaxAmount($tax);
                            $address->save();
                            Mage::log("setDiscount : stap 6   ", null, 'custom.log');

                        }
                        //end: if
                    } //end: foreach

                }

            }

            Mage::log("setDiscount : stap 8   ", null, 'custom.log');
        } catch (Exception $e) {
            Mage::log("Set discount  exception " . $e, null);
        }
    }


}