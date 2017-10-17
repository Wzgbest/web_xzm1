<?php if (!defined('THINK_PATH')) exit(); /*a:7:{s:62:"F:\myproject\webcall\public/../app/index\view\index\index.html";i:1506159991;s:63:"F:\myproject\webcall\public/../app/common\view\index\index.html";i:1506159989;s:71:"F:\myproject\webcall\public/../app/common\view\common\headersource.html";i:1506159989;s:63:"F:\myproject\webcall\public/../app/common\view\common\side.html";i:1506159989;s:65:"F:\myproject\webcall\public/../app/common\view\common\header.html";i:1506159989;s:65:"F:\myproject\webcall\public/../app/common\view\common\footer.html";i:1506159989;s:71:"F:\myproject\webcall\public/../app/common\view\common\footersource.html";i:1506159989;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>销掌门</title>
    <!--公共顶部资源-->
    <!--存放顶部加载的资源-->
<link rel="stylesheet" href="/static/css/reset.css" />
<link rel="stylesheet" href="/static/css/index.css" />
<link rel="stylesheet" href="/static/css/divBlock.css" />
<link rel="stylesheet" href="/static/css/form.css" />
<link rel="stylesheet" href="/static/css/table.css" />
<link rel="stylesheet" href="/static/css/font-awesome.min.css" />
<link rel="stylesheet" href="/static/css/popUp.css" />
<link rel="stylesheet" href="/static/css/response.css" />

<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.reveal.js"></script>
<!--右键菜单插件-->
<script type="text/javascript" src="/static/js/jquery.smartmenus.min.js"></script>
    <!--分页面顶部资源-->
    
</head>
<body>
<!--左侧区域-->
<!--侧边栏-->
<section id="side">
    <aside class="_panel-box">
        <div id="logo">
            <!--<img src="/static/images/logo.png" />-->
        </div>
        <dl id="#">
            <dt><i class="fa fa-line-chart"></i><span>&nbsp;&nbsp;数据统计</span><i class="fa fa-angle-right"></i></dt>
            <div class="ddcontent">
                <dd data-subid="index" _src="<?php echo url('datacount/index/summary'); ?>">简报</dd>
                <dd data-subid ="day-count" _src="<?php echo url('datacount/index/summary'); ?>">日报统计</dd>
                <dd data-subid="sales-funnel" _src="/index/index/developing.html">销售漏斗</dd>
                <dd data-subid = "call-count" _src="/index/index/developing.html">通话记录统计</dd>
                <dd data-subid = "cilents-count" _src="/index/index/developing.html">客户状态统计</dd>
            </div>
        </dl>
        <dl id="#">
            <dt><i class="fa fa-flag"></i><span>&nbsp;&nbsp;任务管理</span><i class="fa fa-angle-right"></i></dt>
            <div class="ddcontent">
                <dd data-subid="task-hall" _src="<?php echo url('task/employee_task/hot_task'); ?>">任务大厅</dd>
                <dd data-subid="going-task" _src="<?php echo url('task/employee_task/direct_participation'); ?>">进行中的任务</dd>
                <dd data-subid="historical-task" _src="<?php echo url('task/employee_task/historical_task'); ?>">历史任务</dd>
            </div>
        </dl>
        <dl id="#">
            <dt><i class="fa fa-cubes"></i><span>&nbsp;&nbsp;员工成长</span><i class="fa fa-angle-right"></i></dt>
            <div class="ddcontent">
                <dd data-subid="employeegrowth-xxx" _src="/index/index/developing.html">我的成长</dd>
            </div>
        </dl>
        <dl id="#">
            <dt><i class="fa fa-user-plus"></i><span>&nbsp;&nbsp;客户搜集</span><i class="fa fa-angle-right"></i></dt>
            <div class="ddcontent">
                <dd data-subid="search-engines" _src="/index/index/developing.html">搜索引擎搜索</dd>
                <dd data-subid="search-zone" _src="/index/index/developing.html">按地区搜索</dd>
                <dd data-subid="zx-library" _src="/index/index/developing.html">中迅库</dd>
                <dd data-subid="my-search-cilents" _src="/index/index/developing.html">我搜集的客户</dd>
            </div>
        </dl>
        <dl id="#">
            <dt><i class="fa fa-pie-chart"></i><span>&nbsp;&nbsp;CRM</span><i class="fa fa-angle-right"></i></dt>
            <div class="ddcontent">
                <dd data-subid="cilents-manage" _src="<?php echo url('crm/customer/customer_manage'); ?>">客户管理</dd>
                <dd data-subid="mycliets" _src="<?php echo url('crm/customer/my_customer'); ?>">我的客户</dd>
                <dd data-subid="high-sea" _src="<?php echo url('crm/customer/public_customer_pool'); ?>">公海池</dd>
                <dd data-subid="number-screening" _src="<?php echo url('crm/sale_chance/index'); ?>">销售机会</dd>
                <dd data-subid="pending-cilents" _src="<?php echo url('crm/customer/customer_pending'); ?>">待处理客户</dd>
                <dd data-subid="my-order" _src="<?php echo url('/crm/order/index'); ?>">我的订单</dd>
                <dd data-subid="my-contract" _src="<?php echo url('crm/contract/index'); ?>">我的合同</dd>
                <dd data-subid="my-bill" _src="<?php echo url('crm/bill/index'); ?>">我的发票</dd>
                <dd data-subid="subordinate-cilents" _src="<?php echo url('crm/customer/customer_subordinate'); ?>">下属的客户</dd>
                <dd data-subid="subordinate-sales" _src="<?php echo url('crm/sale_chance/sale_chance_subordinate'); ?>">下属的销售机会</dd>
                <dd data-subid="mails" _src="<?php echo url('crm/mailer/index'); ?>">邮件</dd>
                <dd data-subid="call-assistant" _src="<?php echo url('workerman/index/assistant'); ?>">通话助手</dd>
            </div>
        </dl>
        <dl id="#">
            <dt><i class="fa fa-book"></i><span>&nbsp;&nbsp;知识库</span><i class="fa fa-angle-right"></i></dt>
            <div class="ddcontent">
                <dd data-subid="zx-lessons" _src="/index/index/developing.html">中讯大课堂</dd>
                <dd data-subid="live-telecast" _src="/index/index/developing.html">直播</dd>
                <dd data-subid="speech-database" _src="<?php echo url('knowledgebase/speech_craft/index'); ?>">话术库</dd>
                <dd data-subid="company-library" _src="<?php echo url('knowledgebase/corporation_share/index'); ?>">企业库</dd>
                <dd data-subid="company-cloud-space" _src="/index/index/developing.html">企业云盘</dd>
            </div>
        </dl>
        <dl id="#">
            <dt><i class="fa fa-gavel"></i><span>&nbsp;&nbsp;待我审核</span><i class="fa fa-angle-right"></i></dt>
            <div class="ddcontent">
                <dd data-subid="verification-contract" _src="<?php echo url('verification/contract/index'); ?>">合同申请</dd>
                <dd data-subid="verification-index" _src="<?php echo url('verification/index/index'); ?>">成单申请</dd>
                <dd data-subid="verification-bill" _src="<?php echo url('verification/bill/index'); ?>">发票申请</dd>
            </div>
        </dl>
        <dl id="#">
            <dt><i class="fa fa-gears"></i><span>&nbsp;&nbsp;系统管理</span><i class="fa fa-angle-right"></i></dt>
            <div class="ddcontent">
                <dd data-subid="company-information" _src="<?php echo url('systemsetting/corporation/showCorpInfo'); ?>">公司信息</dd>
                <dd data-subid="division-management" _src="<?php echo url('systemsetting/structure/index'); ?>">部门管理</dd>
                <dd data-subid="role-management" _src="<?php echo url('systemsetting/role/index'); ?>">职位管理</dd>
                <dd data-subid="staff-management" _src="<?php echo url('systemsetting/employee/manage'); ?>">员工管理</dd>
                <dd data-subid="business-flow" _src="<?php echo url('systemsetting/business_flow/index'); ?>">业务流管理</dd>
                <dd data-subid="contract-setting" _src="<?php echo url('systemsetting/contract/index'); ?>">合同设置</dd>
                <dd data-subid="bill-setting" _src="<?php echo url('systemsetting/bill/index'); ?>">发票设置</dd>
                <dd data-subid="customer-setting" _src="<?php echo url('systemsetting/customer/index'); ?>">工作参数设置</dd>
                <dd data-subid="change-my-password" _src="/index/index/developing.html">密码修改</dd>
            </div>
        </dl>
    </aside>
</section>
<!--顶部右侧区域-->
<!--存放顶部右上方-->
<header class="header">
    <div id="x-layout">
        <i class="fa fa-navicon fa-2x"></i>
    </div>
    <div id="r-nav">
        <div id="nav-user"><i class="fa fa-2x fa-user"></i><span><?php echo $userinfo['truename']; ?></span></div>
        <div id="nav-shop"><i class="fa fa-2x fa-shopping-cart"></i><span>商城</span></div>
        <div id="nav-commu"><i class="fa fa-2x fa-commenting"></i><span>消息</span></div>
        <div id="nav-notice"><i class="fa fa-2x fa-bullhorn"></i><span>通知</span></div>
        <div id="nav-call" data-subid="phone-call" _src="<?php echo url('workerman/index/index'); ?>"><i class="fa fa-2x fa-volume-control-phone"></i><span>拨号</span></div>
        <!--<div class="clearfix"></div>-->
    </div>
</header>
<!--右侧区域-->

<section id="subt">
    <div id="subtitle">
        <div id="index"  class="active"><span>首页</span></div>
    </div>
</section>
<div id="frames">
    <div id="indexfr" class="once">
        页面加载中...
    </div>
</div>

<!-- 消息中心 -->
<div class="message-box hide">
    <div class="message-class">
        <h4>系统消息（<span class="color-red">5</span>）</h4>
        <h4>任务消息（<span class="color-red">5</span>）</h4>
        <h4>CRM消息（无）</h4>
        <h4 class="current">知识库消息（无）</h4>
    </div>
    <div class="message-content">
        <div class="message-content-header">
            知识库消息
        </div>
        <ul class="message-content-container">
            <li class="current"><p><span> * </span>你报名的在线直播课程《销售技巧》还有15分 钟开课啦，快快去学习</p><div class="message-content-container-time">今天16:20</div></li>
            <li><p><span> * </span>你报名的在线直播课程《销售技巧》还有15分 钟开课啦，快快去学习</p><div class="message-content-container-time">今天09:20</div></li>
            <li><p><span> * </span>你报名的在线直播课程《销售技巧》还有15分 钟开课啦，快快去学习</p><div class="message-content-container-time">9月12日 16:20</div></li>
            <li><p><span> * </span>你报名的在线直播课程《销售技巧》还有15分 钟开课啦，快快去学习</p><div class="message-content-container-time">9月12日 10:20</div></li>
        </ul>
        <div class="message-content-footer color-blue">
            设为已读
        </div>
    </div>
</div>

<!--电话盒子-->
<div class="phone-box hide">
    <i class="fa fa-close"></i>
    <div class="m-divBlock m-divBlock2 m-divBlock4">
        <h1 class="call-number">17186761671</h1>
        <h4><i class="fa fa-phone color-green"></i>&nbsp;&nbsp;通话中...&nbsp;&nbsp;<span class="color-yellow">00:00:00</span></h4>
        <div class="dial">
            <div class="clearfix"></div>
            <div class="row1">
                <div class="u-block1 current">主通话</div>
                <div class="u-block1">三方通话</div>
                <div class="u-block1">请求协助</div>
            </div>
            <div class="row1">
                <input id="phone-number" value="" class="row1 row2 "/>
                <div class="delete" id="num-dele">
                    <i class="fa fa-backward fa-2x"></i>
                </div>
            </div>
            <div class="row1 row3">
                <button class="u-block1 u-block2 num">1</button>
                <button class="u-block1 u-block2 num">2</button>
                <button class="u-block1 u-block2 num">3</button>
            </div>
            <div class="row1 row3">
                <button class="u-block1 u-block2 num">4</button>
                <button class="u-block1 u-block2 num">5</button>
                <button class="u-block1 u-block2 num">6</button>
            </div>
            <div class="row1 row3">
                <button class="u-block1 u-block2 num">7</button>
                <button class="u-block1 u-block2 num">8</button>
                <button class="u-block1 u-block2 num">9</button>
            </div>
            <div class="row1 row3">
                <button class="u-block1 u-block2 num">*</button>
                <button class="u-block1 u-block2 num">0</button>
                <button class="u-block1 u-block2 num">#</button>
            </div>
            <h1 class="row1 row3 row4">
                <div class="on"><i class="fa fa-phone"></i>&nbsp;&nbsp;拨打</div>
                <div class="off hide"><i class="fa fa-phone"></i>&nbsp;&nbsp;挂断</div>
            </h1>
        </div>
    </div>
    <div class="m-divBlock m-divBlock2 m-divBlock4">
        <div class="click-node">
            <i class="fa fa-tags color-green"></i>&nbsp;&nbsp;打点<i class="fa fa-plus fr"></i>
        </div>
        <ul class="nodes"></ul>
    </div>
</div>


<!--分页面底部信息-->
<!--存放底部信息-->
<!--分页面底部资源-->

<!--公共底部资源-->
<!--存放底部资源，css，js等-->
<script src="/static/js/index.js"></script>
<script src="/static/js/call.js"></script>
<script src="/static/js/message.js"></script>
</body>

<script src="/vendor/layer/layer.js"></script>
<script src="/crm/js/remark.js"></script>

</html>

