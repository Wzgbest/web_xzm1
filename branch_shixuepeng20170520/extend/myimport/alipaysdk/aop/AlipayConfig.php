<?php
/**
 * Created by messhair
 * Date: 17-3-20
 */
class AlipayConfig{
    public function getAlipaySetting(){

        $alipay_config['partner']		= '2088102169636639';

        $alipay_config['appid']			= '2016080200150817';

        $alipay_config['public_key']    = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxYwW5cEkeWF4pzkOh1lfm6e+KIKGVzm0xl2SJjCQgJBb0XOrI+92VE5EepLoWjXlKBm8B0vlyvQX+5KpqK2gMdd9kzwi9XQmENKTYULl9s2UmYDl7Kp5WkcY5y1TOF6M+n5hOIBoYOFQSSfj2glQvw9/JUwk36Ano5jvJdh3ewwm/zwzRTtmlASzjvAuLpX5GTTb87QFOxac3GrymiJkL9fPYqONksfaJHVvROOLK3XcGiHQ9NFsgWvO0J4hDOYhowO1Vippk74rODe9OmJlVbGM5fqa79I6JSaVI44pmQonIm8kUk2qdT5UEf9Dlt/K73xKHX/cJAdDUj+IvJ3NJwIDAQAB';

        $alipay_config['private_key_path'] = dirname(__FILE__).'/cacert/app_private_key.pem';
        return $alipay_config;
    }
}