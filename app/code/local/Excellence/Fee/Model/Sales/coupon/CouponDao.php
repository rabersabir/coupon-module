<?php

/**
 * Created by PhpStorm.
 * User: zuzana
 * Date: 05-04-14
 * Time: 07:33
 */
class CouponDao
{

	protected $readConnection;
	protected $writeConnection;


	function __construct()
	{

		$resource = Mage::getSingleton('core/resource');
		$this->readConnection = $resource->getConnection('core_read');
		$this->writeConnection = $resource->getConnection('core_write');

	}

	public function createCouponSaving(CouponSaving $couponSaving)
	{
		$queryInsert = "INSERT INTO coupon_saving (coupon_id, customer,creation_date, valid_Until, state,required_number_of_products) values
        ( " . $couponSaving->getCouponId() . ",
        " . $couponSaving->getCustomerId() . ",
        '" . $couponSaving->getCreationDate() . "',
        '" . $couponSaving->getValidUntil() . "',
        '" . $couponSaving->getState() . "',
        " . $couponSaving->getRequiredNumberOfProducts() . " )";

		Mage::log("createCouponSaving insert query $queryInsert", null, 'custom.log');


		$this->writeConnection->query($queryInsert);


		$couponSaving->setCouponSavingId($this->writeConnection->lastInsertId());

		return $couponSaving;
	}

	public function getOverviewByCustomerId($customerId){


		$sqlQuery="SELECT cs.*, c.*, count(*) aantalproducten, max(st.prices) duursteproduct FROM coupon_saving cs, stamp st, coupon c
		WHERE st.coupon_saving_id=cs.coupon_saving_id and customer=$customerId and c.coupon_id=cs.coupon_id group by cs.coupon_saving_id
		LIMIT 0, 30 ";

        Mage::log("was is de query $sqlQuery",null,'custom.log');
		$data  = $this->readConnection->fetchAll($sqlQuery);
		if  (sizeof($data) == 0) {
			return null;
		} else {
			return $data;
		}
	}


	public function updateCouponSaving(CouponSaving $couponSaving)
	{
		$today=NOW();
		if ($couponSaving->getState() == CouponState::OPEN) {

			list($requiredNumberOfProducts, $numberOfProductsOrdered,$maxPaid) = $this->getNumberOfProductsOrderdForSavingCoupon($couponSaving,$couponSaving->getCustomerId());
			if ($requiredNumberOfProducts != 0) {
				if ($numberOfProductsOrdered >= $requiredNumberOfProducts) {
					$queryInsert = "UPDATE coupon_saving set  state='" . CouponState::COMPLETED . "' , completed_date='$today'  where  coupon_saving_id=" . $couponSaving->getCouponSavingId();
					$this->writeConnection->query($queryInsert);
				}
			}

			if (NOW() >= $couponSaving->getValidUntil()) {
				$queryInsert = "UPDATE coupon_saving set    completed_date =NULL ,  state='" . CouponState::INVALID . "'  where  coupon_saving_id=" . $couponSaving->getCouponSavingId();
				$this->writeConnection->query($queryInsert);

			}

		}

		return $couponSaving;
	}


	public function setCouponSavingToUsed(CouponSaving $couponSaving)
	{

		Mage::log(" start  setCouponSavingToUsed", null, 'custom.log');
		$today=NOW();
		$state=CouponState::USED;
		$id=$couponSaving->getCouponSavingId();
		if ($couponSaving->getState() == CouponState::IN_INVOICE) {

			$updateQuery = "UPDATE coupon_saving set    used_date ='$today' ,  state='$state'  where  coupon_saving_id=$id";

			Mage::log(" update query setCouponSavingToUsed  $updateQuery", null, 'custom.log');

			$this->writeConnection->query($updateQuery);

		}

		
	}


	public function setCouponSavingToInVoice(CouponSaving $couponSaving, $orderId)
	{

		$today=NOW();
		$state=CouponState::IN_INVOICE;
		$id=$couponSaving->getCouponSavingId();
		if ($couponSaving->getState() == CouponState::IN_CART) {

			$updateQuery = "UPDATE coupon_saving set    used_date ='$today' ,  order_id=$orderId,  state='$state'  where  coupon_saving_id=$id";
			echo $updateQuery;
			Mage::log(" update query to used query $updateQuery", null, 'custom.log');

			$this->writeConnection->query($updateQuery);

		}

		return $couponSaving;
	}

	public function setCouponSavingInUse(CouponSaving $couponSaving, $discount, $orderId)
	{

		$state=CouponState::IN_CART;
		$id=$couponSaving->getCouponSavingId();
		if ($couponSaving->getState() == CouponState::COMPLETED || $couponSaving->getState() == CouponState::IN_CART) {

			$updateQuery = "UPDATE coupon_saving set    discount_amount=$discount , order_id=$orderId, state='$state'  where  coupon_saving_id=$id" ;

		//	echo $updateQuery;
			Mage::log(" update query to used query $updateQuery", null, 'custom.log');

			$this->writeConnection->query($updateQuery);

		}

		return $couponSaving;
	}

	public function createStamp($couponSavingId, $productName, $productPrice, $orderId)
	{
		$date = NOW();
		$queryInsert = "INSERT INTO stamp (coupon_saving_id, purchased_product ,prices,purchased_date, order_id) VALUES ( $couponSavingId,'$productName',$productPrice,'$date',$orderId )";
		Mage::log("stamp insert query $queryInsert", null, 'custom.log');

		$this->writeConnection->query($queryInsert);

	}


	public function loadCouponByBrand($brand)
	{
		$query= "SELECT * FROM coupon   WHERE brand='$brand'";
		$results = $this->readConnection->fetchAll($query);

		$num_rows = count($results);
		if ($num_rows == 0) {
			return null;

		} else {
			$coupon;
			foreach($results as $couponDbRecord){

				$coupon= Coupon::fillWithData($couponDbRecord);
				break;
			}
			return $coupon;
		}

	}

	public function loadCouponById($couponId)
	{
		$query= "SELECT * FROM coupon   WHERE coupon_id=$couponId";
		$results = $this->readConnection->fetchAll($query);

		$num_rows = count($results);
		if ($num_rows == 0) {
			return null;

		} else {
			$coupon;
			foreach($results as $couponDbRecord){

				$coupon= Coupon::fillWithData($couponDbRecord);
				break;
			}
			return $coupon;
		}

	}

	public function loadCouponSaving($couponId, $customerId, $state)
	{
		$query = "SELECT * FROM coupon_saving
         WHERE coupon_id= $couponId
         AND customer = $customerId
         AND state= $state";

		Mage::log("loadCouponSaving select  query $query", null, 'custom.log');

		$data  = $this->readConnection->fetchAll($query);
		$savingCoupon;
		if  (sizeof($data) == 0) {
			return null;

		} else {

			foreach($data as $savingCouponData){
				$savingCoupon= CouponSaving::fillWithData($savingCouponData);
			}
			return $savingCoupon;


		}
	}


	public function loadCompletedCouponSaving($couponId, $customerId)
	{

		return  $this->loadCouponSaving($couponId,$customerId,CouponState::COMPLETED);
	}


	public function loadInCartCouponSaving($couponId, $customerId)
	{

		return  $this->loadCouponSaving($couponId,$customerId,CouponState::IN_CART);
	}

	public function resetInCartCouponSavingsForCustomer( $customerId)
	{

		$oldState=CouponState::IN_CART;
		$newState=CouponState::COMPLETED;
		
		

			$updateQuery = "UPDATE coupon_saving set    discount_amount=null , order_id=null, state=$newState  where  state=$oldState    AND customer =$customerId" ;

			echo $updateQuery;
			Mage::log(" update query to used query $updateQuery", null, 'custom.log');
			$this->writeConnection->query($updateQuery);
	}
	

	public  function  loadInInVoiceCouponSaving($couponId, $customerId)
	{

		return  $this->loadCouponSaving($couponId,$customerId,CouponState::IN_INVOICE);
	}

	public function loadCouponSavingByOrderId($brand, $orderId,$customerId)
	{


		$query = "SELECT * FROM coupon_saving cs, coupon co
         WHERE order_id= $orderId 
         AND customer =$customerId
         AND co.coupon_id= cs.coupon_id
       	 AND brand='$brand'";
		 
		Mage::log("loadCouponSaving select  query $query", null, 'custom.log');

		$results  = $this->readConnection->fetchAll($query);
		$savingCoupon =null;
		if  (sizeof($results) == 0) {
			return null;

		} else {
			Mage::log("in else ", null, 'custom.log');
			foreach($results as $savingCouponData){
				Mage::log("in for loop", null, 'custom.log');
				$savingCoupon= CouponSaving::fillWithData($savingCouponData);
				Mage::log($savingCoupon, null, 'custom.log');
				break;
			}
			return $savingCoupon;
		}
	}

	/**
	 * @param CouponSaving $couponSaving
	 * @return array
	 */
	public function getNumberOfProductsOrderdForSavingCoupon(CouponSaving $couponSaving, $customerId)
	{

		//	couponId=$couponSaving->getCouponSavingId();
		$query = "SELECT  COUNT(*) number_of_products_ordered, co_sa.required_number_of_products , max(st.prices) maxPaid
            FROM coupon_saving co_sa, stamp st
            WHERE co_sa.coupon_saving_id=st.coupon_saving_id
            AND customer =$customerId  AND
            co_sa.coupon_saving_id=".$couponSaving->getCouponSavingId();
		Mage::log("query voor max paid $query ", null, 'custom.log');

		$results = $this->readConnection->fetchAll($query);
		$foundData;
		foreach($results as $savingCouponData){




			$requiredNumberOfProducts = $savingCouponData['required_number_of_products'];
			$numberOfProductsOrdered = $savingCouponData['number_of_products_ordered'];
			$maxPaid = $savingCouponData['maxPaid'];

			Mage::log("requiredNumberOfProducts $requiredNumberOfProducts ", null, 'custom.log');


			Mage::log(" numberOfProductsOrdered $numberOfProductsOrdered", null, 'custom.log');

			Mage::log(" numberOfProductsOrdered $numberOfProductsOrdered", null, 'custom.log');


			$foundData= array($requiredNumberOfProducts, $numberOfProductsOrdered,$maxPaid);
			break;
		}
		return $foundData;
	}

}