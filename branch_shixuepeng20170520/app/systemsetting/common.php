<?php
/**
 * Created by messhair.
 * Date: 2017/5/15
 */
use think\Hook;

Hook::add('check_customer_transmit','app\\systemsetting\\behavior\\checkCustomerTransmit');