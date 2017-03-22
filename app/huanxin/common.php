<?php
use think\Hook;


//红包过期检查
Hook::add('check_over_time_red','app\\huanxin\\behavior\\CheckOverTimeRedEnvelope');