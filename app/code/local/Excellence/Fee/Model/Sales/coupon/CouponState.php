<?php
/**
 * Created by PhpStorm.
 * User: zuzana
 * Date: 04-04-14
 * Time: 21:33
 */

abstract class CouponState {

    const OPEN = 1;
    const INVALID=2;
    const COMPLETED=3;
    const IN_CART= 4;
    const IN_INVOICE= 5;
    const USED= 6;

}



