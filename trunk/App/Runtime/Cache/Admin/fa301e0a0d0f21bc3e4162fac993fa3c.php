<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>管理后台</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="/Public/Vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/Public/Vendor/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
<link href="/Public/Admin/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="/Public/Admin/css/style-metro.css" rel="stylesheet" type="text/css"/>
<link href="/Public/Admin/css/style.css" rel="stylesheet" type="text/css"/>
<link href="/Public/Admin/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="/Public/Admin/css/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="/Public/Admin/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES --><!-- BEGIN PAGE LEVEL STYLES -->
    
    <link href="/Public/Admin/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/Admin/css/daterangepicker.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/Admin/css/fullcalendar.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/Admin/css/jqvmap.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="/Public/Admin/css/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css" media="screen"/>

    <!-- END PAGE LEVEL STYLES -->
    <link rel="shortcut icon" href="/Public/Admin/image/favicon.ico"/>
</head>
<body class="page-header-fixed">
<div class="header navbar navbar-inverse navbar-fixed-top">
    <!-- BEGIN TOP NAVIGATION BAR -->
    <div class="navbar-inner">
        <div class="container-fluid">
            <!-- BEGIN LOGO -->
            <a class="brand" href="index.html"> <img src="/Public/Admin/image/logo.png" alt="logo"/> </a>
            <!-- END LOGO --><!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
                <img src="/Public/Admin/image/menu-toggler.png" alt=""/> </a>
            <!-- END RESPONSIVE MENU TOGGLER --><!-- BEGIN TOP NAVIGATION MENU -->
            <ul class="nav pull-right">
                <!-- BEGIN NOTIFICATION DROPDOWN -->
                <li class="dropdown" id="header_notification_bar">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-warning-sign"></i>
                        <span class="badge">6</span> </a>
                    <ul class="dropdown-menu extended notification">
                        <li>
                            <p>You have 14 new notifications</p>
                        </li>
                        <li>
                            <a href="#">
                                <span class="label label-success"><i class="icon-plus"></i></span> New user registered.
                                <span class="time">Just now</span> </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="label label-important"><i class="icon-bolt"></i></span> Server #12 overloaded.
                                <span class="time">15 mins</span> </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="label label-warning"><i class="icon-bell"></i></span> Server #2 not respoding.
                                <span class="time">22 mins</span> </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="label label-info"><i class="icon-bullhorn"></i></span> Application error.
                                <span class="time">40 mins</span> </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="label label-important"><i class="icon-bolt"></i></span> Database overloaded 68%.
                                <span class="time">2 hrs</span> </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="label label-important"><i class="icon-bolt"></i></span> 2 user IP blocked.
                                <span class="time">5 hrs</span> </a>
                        </li>
                        <li class="external">
                            <a href="#">See all notifications <i class="m-icon-swapright"></i></a>
                        </li>
                    </ul>
                </li>
                <!-- END NOTIFICATION DROPDOWN --><!-- BEGIN INBOX DROPDOWN -->
                <li class="dropdown" id="header_inbox_bar">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-envelope"></i>
                        <span class="badge">5</span> </a>
                    <ul class="dropdown-menu extended inbox">
                        <li>
                            <p>You have 12 new messages</p>
                        </li>
                        <li>
                            <a href="inbox.html?a=view">
                                <span class="photo"><img src="/Public/Admin/image/avatar2.jpg" alt=""/></span>
								<span class="subject">
								<span class="from">Lisa Wong</span>
								<span class="time">Just Now</span>
								</span>
								<span class="message">
								Vivamus sed auctor nibh congue nibh. auctor nibh
								auctor nibh...
								</span> </a>
                        </li>
                        <li>
                            <a href="inbox.html?a=view">
                                <span class="photo"><img src=".//Public/Admin/image/avatar3.jpg" alt=""/></span>
								<span class="subject">
								<span class="from">Richard Doe</span>
								<span class="time">16 mins</span>
								</span>
								<span class="message">
								Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh
								auctor nibh...
								</span> </a>
                        </li>
                        <li>
                            <a href="inbox.html?a=view">
                                <span class="photo"><img src=".//Public/Admin/image/avatar1.jpg" alt=""/></span>
								<span class="subject">
								<span class="from">Bob Nilson</span>
								<span class="time">2 hrs</span>
								</span>
								<span class="message">
								Vivamus sed nibh auctor nibh congue nibh. auctor nibh
								auctor nibh...
								</span> </a>
                        </li>
                        <li class="external">
                            <a href="inbox.html">See all messages <i class="m-icon-swapright"></i></a>
                        </li>
                    </ul>
                </li>
                <!-- END INBOX DROPDOWN --><!-- BEGIN TODO DROPDOWN -->
                <li class="dropdown" id="header_task_bar">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-tasks"></i>
                        <span class="badge">5</span> </a>
                    <ul class="dropdown-menu extended tasks">
                        <li>
                            <p>You have 12 pending tasks</p>
                        </li>
                        <li>
                            <a href="#">
								<span class="task">
								<span class="desc">New release v1.2</span>
								<span class="percent">30%</span>
								</span>
								<span class="progress progress-success ">
								<span style="width: 30%;" class="bar"></span>
								</span> </a>
                        </li>
                        <li>
                            <a href="#">
								<span class="task">
								<span class="desc">Application deployment</span>
								<span class="percent">65%</span>
								</span>
								<span class="progress progress-danger progress-striped active">
								<span style="width: 65%;" class="bar"></span>
								</span> </a>
                        </li>
                        <li>
                            <a href="#">
								<span class="task">
								<span class="desc">Mobile app release</span>
								<span class="percent">98%</span>
								</span>
								<span class="progress progress-success">
								<span style="width: 98%;" class="bar"></span>
								</span> </a>
                        </li>
                        <li>
                            <a href="#">
								<span class="task">
								<span class="desc">Database migration</span>
								<span class="percent">10%</span>
								</span>
								<span class="progress progress-warning progress-striped">
								<span style="width: 10%;" class="bar"></span>
								</span> </a>
                        </li>
                        <li>
                            <a href="#">
								<span class="task">
								<span class="desc">Web server upgrade</span>
								<span class="percent">58%</span>
								</span>
								<span class="progress progress-info">
								<span style="width: 58%;" class="bar"></span>
								</span> </a>
                        </li>
                        <li>
                            <a href="#">
								<span class="task">
								<span class="desc">Mobile development</span>
								<span class="percent">85%</span>
								</span>
								<span class="progress progress-success">
								<span style="width: 85%;" class="bar"></span>
								</span> </a>
                        </li>
                        <li class="external">
                            <a href="#">See all tasks <i class="m-icon-swapright"></i></a>
                        </li>
                    </ul>
                </li>
                <!-- END TODO DROPDOWN -->
                <!-- BEGIN USER LOGIN DROPDOWN -->
                <li class="dropdown user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img alt="" src="/Public/Admin/image/avatar1_small.jpg"/>
                        <span class="username">Bob Nilson</span> <i class="icon-angle-down"></i> </a>
                    <ul class="dropdown-menu">
                        <li><a href="extra_profile.html"><i class="icon-user"></i> My Profile</a></li>
                        <li><a href="page_calendar.html"><i class="icon-calendar"></i> My Calendar</a></li>
                        <li><a href="inbox.html"><i class="icon-envelope"></i> My Inbox(3)</a></li>
                        <li><a href="#"><i class="icon-tasks"></i> My Tasks</a></li>
                        <li class="divider"></li>
                        <li><a href="extra_lock.html"><i class="icon-lock"></i> Lock Screen</a></li>
                        <li><a href="login.html"><i class="icon-key"></i> Log Out</a></li>
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
            </ul>
            <!-- END TOP NAVIGATION MENU -->
        </div>
    </div>
    <!-- END TOP NAVIGATION BAR -->
</div>
<div class="page-container">
    <div class="page-sidebar nav-collapse collapse">
    <!-- BEGIN SIDEBAR MENU -->
    <ul class="page-sidebar-menu">
        <li>
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            <div class="sidebar-toggler hidden-phone"></div>
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
        </li>
        <li>
            <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
            <form class="sidebar-search">
                <div class="input-box">
                    <a href="javascript:;" class="remove"></a>
                    <input type="text" placeholder="Search..."/>
                    <input type="button" class="submit" value=" "/>
                </div>
            </form>
            <!-- END RESPONSIVE QUICK SEARCH FORM -->
        </li>
        <li class="start active ">
            <a href="<?php echo U('Index/index');?>">
                <i class="icon-home"></i>
                <span class="title">主菜单</span>
                <span class="selected"></span>
            </a>
        </li>
        <li class="">
            <a href="javascript:;">
                <i class="icon-cogs"></i>
                <span class="title">用户管理</span>
                <span class="arrow "></span>
            </a>
            <ul class="sub-menu">
                <li>
                    <a href="<?php echo U('Manage/index');?>">
                        管理员</a>
                </li>
                <li>
                    <a href="layout_horizontal_menu1.html">
                        作者列表</a>
                </li>
                <li>
                    <a href="layout_horizontal_menu2.html">
                        会员列表</a>
                </li>
            </ul>
        </li>
        <li class="">
            <a href="javascript:;">
                <i class="icon-bookmark-empty"></i>
                <span class="title">书籍管理</span>
                <span class="arrow "></span>
            </a>
            <ul class="sub-menu">
                <li>
                    <a href="ui_general.html">
                        分类管理</a>
                </li>
                <li>
                    <a href="ui_buttons.html">
                        书籍列表</a>
                    <ul class="sub-menu">
                        <li>
                            <a href="javascript:;">
                                <i class="icon-user"></i>
                                武侠小说
                                <span class="arrow"></span>
                            </a>
                        </li>
                        <li><a href="#"><i class="icon-user"></i>言情小说</a></li>
                        <li><a href="#"><i class="icon-external-link"></i>经典文学</a></li>
                    </ul>
                </li>
                <li>
                    <a href="ui_general.html">
                        审核管理</a>
                </li>
            </ul>
        </li>
        <li class="">
            <a href="javascript:;">
                <i class="icon-table"></i>
                <span class="title">交易管理</span>
                <span class="arrow "></span>
            </a>
            <ul class="sub-menu">
                <li>
                    <a href="form_layout.html">
                        套餐类型</a>
                </li>
                <li>
                    <a href="form_samples.html">
                        支付配置</a>
                </li>
                <li>
                    <a href="form_component.html">
                        交易记录</a>
                </li>
            </ul>
        </li>
        <li class="">
            <a href="javascript:;">
                <i class="icon-briefcase"></i>
                <span class="title">打赏管理</span>
                <span class="arrow "></span>
            </a>
            <ul class="sub-menu">
                <li>
                    <a href="page_timeline.html">
                        <i class="icon-time"></i>
                        道具管理</a>
                </li>
                <li>
                    <a href="page_coming_soon.html">
                        <i class="icon-cogs"></i>
                        粉丝等级设置</a>
                </li>
            </ul>
        </li>
        <li class="">
            <a href="javascript:;">
                <i class="icon-gift"></i>
                <span class="title">其他设置</span>
                <span class="arrow "></span>
            </a>
            <ul class="sub-menu">
                <li>
                    <a href="extra_profile.html">
                        User Profile</a>
                </li>
                <li>
                    <a href="extra_lock.html">
                        Lock Screen</a>
                </li>
                <li>
                    <a href="extra_faq.html">
                        FAQ</a>
                </li>
                <li>
                    <a href="inbox.html">
                        Inbox</a>
                </li>
            </ul>
        </li>
    </ul>
    <!-- END SIDEBAR MENU -->
</div>
    <!-- BEGIN PAGE -->
    <div class="page-content">
        <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
        <div id="portlet-config" class="modal hide">
            <div class="modal-header">
                <button data-dismiss="modal" class="close" type="button"></button>
                <h3>Widget Settings</h3>
            </div>
            <div class="modal-body">
                Widget settings form goes here
            </div>
        </div>
        <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM--><!-- BEGIN PAGE CONTAINER-->
        <div class="container-fluid">
            <!-- BEGIN PAGE HEADER-->
            <div class="row-fluid">
                <div class="span12">
                    <!-- BEGIN STYLE CUSTOMIZER -->
                    <div class="color-panel hidden-phone">
                        <div class="color-mode-icons icon-color"></div>
                        <div class="color-mode-icons icon-color-close"></div>
                        <div class="color-mode">
                            <p>THEME COLOR</p>
                            <ul class="inline">
                                <li class="color-black current color-default" data-style="default"></li>
                                <li class="color-blue" data-style="blue"></li>
                                <li class="color-brown" data-style="brown"></li>
                                <li class="color-purple" data-style="purple"></li>
                                <li class="color-grey" data-style="grey"></li>
                                <li class="color-white color-light" data-style="light"></li>
                            </ul>
                            <label> <span>Layout</span> <select class="layout-option m-wrap small">
                                <option value="fluid" selected>Fluid</option>
                                <option value="boxed">Boxed</option>
                            </select> </label> <label> <span>Header</span> <select class="header-option m-wrap small">
                            <option value="fixed" selected>Fixed</option>
                            <option value="default">Default</option>
                        </select> </label> <label> <span>Sidebar</span> <select class="sidebar-option m-wrap small">
                            <option value="fixed">Fixed</option>
                            <option value="default" selected>Default</option>
                        </select> </label> <label> <span>Footer</span> <select class="footer-option m-wrap small">
                            <option value="fixed">Fixed</option>
                            <option value="default" selected>Default</option>
                        </select> </label>
                        </div>
                    </div>
                    <!-- END BEGIN STYLE CUSTOMIZER --><!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    
    <h3 class="page-title">
        管理员信息
        <small>statistics and more</small>
    </h3>
    <ul class="breadcrumb">
        <li>
            <i class="icon-home"></i> <a href="index.html">用户管理</a> <i class="icon-angle-right"></i>
        </li>
        <li><a href="#">管理员</a></li>
        <li class="pull-right no-text-shadow">
            <div id="dashboard-report-range" class="dashboard-date-range tooltips no-tooltip-on-touch-device responsive" data-tablet="" data-desktop="tooltips" data-placement="top" data-original-title="Change dashboard date range">
                <i class="icon-calendar"></i> <span></span> <i class="icon-angle-down"></i>
            </div>
        </li>
    </ul>

                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE HEADER-->
            
    <div id="manage-admin">
        <!-- BEGIN DASHBOARD STATS -->
        <div class="row-fluid">
            <div class="span3 responsive" data-tablet="span6" data-desktop="span3">
                <table width="95%">
                    <tr>
                        <td width="15%">ID</td>
                        <td width="20%">用户名</td>
                        <td width="20%">登录次数</td>
                        <td width="20%">最后登录时间</td>
                        <td width="20%">登录ip</td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- END DASHBOARD STATS -->
        <div class="clearfix"></div>
    </div>

        </div>
        <!-- END PAGE CONTAINER-->
    </div>
    <!-- END PAGE -->
</div>
<!-- END CONTAINER --><!-- BEGIN FOOTER -->
<div class="footer">
    <div class="footer-inner">
        2013 &copy; Metronic by keenthemes.
    </div>
    <div class="footer-tools">
        <span class="go-top">
        <i class="icon-angle-up"></i>
        </span>
    </div>
</div>
<!-- BEGIN CORE PLUGINS -->
<script src="/Public/Vendor/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="/Public/Vendor/jquery/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="/Public/Vendor/jquery/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script src="/Public/Vendor/bootstrap/bootstrap.min.js" type="text/javascript"></script>
<!--[if lt IE 9]>
<script src="/Public/Admin/js/excanvas.min.js"></script>
<script src="/Public/Admin/js/respond.min.js"></script><![endif]-->
<script src="/Public/Admin/js/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.blockui.min.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.cookie.min.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS --><!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="/Public/Admin/js/jquery.vmap.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.vmap.russia.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.vmap.world.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.vmap.europe.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.vmap.germany.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.vmap.usa.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.vmap.sampledata.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.flot.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.flot.resize.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.pulsate.min.js" type="text/javascript"></script>
<script src="/Public/Admin/js/date.js" type="text/javascript"></script>
<script src="/Public/Admin/js/daterangepicker.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.gritter.js" type="text/javascript"></script>
<script src="/Public/Admin/js/fullcalendar.min.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.easy-pie-chart.js" type="text/javascript"></script>
<script src="/Public/Admin/js/jquery.sparkline.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS --><!-- BEGIN PAGE LEVEL SCRIPTS -->

    <script src="/Public/Admin/js/app.js" type="text/javascript"></script>
    <!--<script src="/Public/Admin/js/index.js" type="text/javascript"></script>-->

<script>
    $(function () {
        App.init(); // initlayout and core plugins
//        Index.init();
//        Index.initJQVMAP(); // init index page's custom scripts
//        Index.initCalendar(); // init index page's custom scripts
//        Index.initCharts(); // init index page's custom scripts
//        Index.initChat();
//        Index.initMiniCharts();
//        Index.initDashboardDaterange();
//        Index.initIntro();
    });
</script>
</body>
</html>