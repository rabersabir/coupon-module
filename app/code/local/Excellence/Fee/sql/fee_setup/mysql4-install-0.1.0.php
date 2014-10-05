<?php

$installer = $this;
try{
Mage::log("start startSetup ", null);
$installer->startSetup();

$installer->run("
		

CREATE TABLE `coupon` (
  `coupon_id` int(11) NOT NULL,
  `validity` varchar(10) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `number_of_products` int(11) NOT NULL,
  `min_price` decimal(6,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




INSERT INTO `coupon` VALUES (0,'+6 months','Nieuw merk',3,9.95),(1,'+6 months','Vichy',10,8.00),(2,'+6 months','Avene',8,9.95),(3,'+6 months','La Roche-Posay',8,9.95),(4,'+6 months','Eucerin',8,9.95),(5,'+6 months','Inneov',3,0.00),(6,'+6 months','Olympus',5,9.95),(7,'+6 months','Nine West',8,9.95);

CREATE TABLE `coupon_saving` (
  `coupon_saving_id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) DEFAULT NULL,
  `customer` int(11) DEFAULT NULL,
  `valid_State` varchar(200) DEFAULT NULL,
  `valid_Until` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `used_date` timestamp NULL DEFAULT NULL,
  `completed_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` varchar(50) DEFAULT NULL,
  `required_number_of_products` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(6,2) DEFAULT NULL,
  PRIMARY KEY (`coupon_saving_id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;



CREATE TABLE `stamp` (
  `stamp_id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_saving_id` int(11) NOT NULL,
  `purchased_product` varchar(50) NOT NULL,
  `prices` decimal(6,2) NOT NULL,
  `purchased_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `order_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`stamp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=latin1;


    ");

$installer->endSetup(); 

}catch(Exception $e) {
		Mage::log("Iets fout gegaan " , null);
			Mage::log( $e, null);
		}

Mage::log("end startSetup ", null);