<?php
/**
 * Created by messhair.
 * Date: 2016/12/21
 */
// Ӧ������ļ�

// ���PHP����
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// ��������ģʽ ���鿪���׶ο��� ����׶�ע�ͻ�����Ϊfalse
define('APP_DEBUG',True);
define('BIND_MODULE','Admin');
// ����Ӧ��Ŀ¼
define('APP_PATH','./App/');

// ����ThinkPHP����ļ�
require './ThinkPHP/ThinkPHP.php';

// ��^_^ ���治��Ҫ�κδ����� ������˼�