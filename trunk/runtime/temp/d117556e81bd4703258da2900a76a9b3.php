<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"F:\myproject\webcall\public/../app/login\view\index\index.html";i:1506159991;}*/ ?>
<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Login Form</title>
		<link rel="stylesheet" href="/login/css/style.css" />
		<script src="/login/js/prefixfree.min.js"></script>  
		<script src="/static/js/jquery-1.11.1.min.js"></script>  
		    <script>
     /*
     11     ① 用setInterval(draw, 33)设定刷新间隔
     12
     13     ② 用String.fromCharCode(1e2+Math.random()*33)随机生成字母
     14
     15     ③ 用ctx.fillStyle=’rgba(0,0,0,.05)’; ctx.fillRect(0,0,width,height); ctx.fillStyle=’#0F0′; 反复生成opacity为0.5的半透明黑色背景
     16
     17     ④ 用x = (index * 10)+10;和yPositions[index] = y + 10;顺序确定显示字母的位置
     18
     19     ⑤ 用fillText(text, x, y); 在指定位置显示一个字母 以上步骤循环进行，就会产生《黑客帝国》的片头效果。
     20 */
         $(document).ready(function() {
                //设备宽度
                var s = window.screen;
                 var width = q.width = s.width;
                var height = q.height;
                var yPositions = Array(300).join(0).split('');
                 var ctx = q.getContext('2d');
                var draw = function() {
                         ctx.fillStyle = 'rgba(0,0,0,.05)';
                         ctx.fillRect(0, 0, width, height);
                         ctx.fillStyle = 'green';/*代码颜色*/
                         ctx.font = '10pt Georgia';
                         yPositions.map(function(y, index) {
                                 text = String.fromCharCode(1e2 + Math.random() * 330);
                                 x = (index * 10) + 10;
                                 q.getContext('2d').fillText(text, x, y);
                                 if (y > Math.random() * 1e4) {
                                        yPositions[index] = 0;
                                     } else {
                                        yPositions[index] = y + 10;
                                     }
                            });
                     };
                RunMatrix();
               function RunMatrix() {
                        Game_Interval = setInterval(draw,30);
                     }
             });
     </script>
 </head>
  <body>

<div align="center" style=" position:fixed; left:0; top:0px; width:100%; height:100%;">
           <canvas id="q" width="1440" height="1080"></canvas>
</div>
 <div class="login">
	<h1>Login</h1>
    <form action="/login/index/verifylogin" method="post">
    	<input type="text" name="telephone" placeholder="手机号码" value=""/>
        <input type="text" name="password" placeholder="密码" id="password" value=""/>
        <button type="submit" class="btn btn-primary btn-block btn-large">登录</button>
    </form>
</div>
</body>
</html>
