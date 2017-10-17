<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"F:\myproject\webcall\public/../app/knowledgebase\view\corporation_share\index.html";i:1506159992;}*/ ?>
<div class="knowledgebase knowledgebase_company_library knowledgebase_company_library_index">
	<div class="index_panel index">
		<header>
            <ul class="m-firNav">
                <li in_column="0" class="in_column current"><div>企业库</div></li>
            </ul>
        </header>
        <section class="m-divBlock m-divBlock2">
        	<div class="m-filterNav">
                <form class="search_form fl" onsubmit="return false;">
                    <input type="text" name="key_word" placeholder="请输入关键字" value=""/>
                    <button class="u-btnSearch" onclick="search_share();">查询</button>
                </form>
                <div class="new-company-library fr" style="display: none;">
            		<i class="fa fa-plus"></i><span>新建</span>
            	</div>
            	<div class="clearfix"></div>
            </div>
        </section>
    	<ul class="library-list">
    	<?php if(is_array($share_list) || $share_list instanceof \think\Collection || $share_list instanceof \think\Paginator): $i = 0; $__LIST__ = $share_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['img'] == ''): ?>
    		<li class="m-divBlock m-divBlock2 lib" share_id="<?php echo $vo['id']; ?>">
    			<div class="lib-content">
        			<div class="face">
        				<img src="<?php echo $vo['userpic']; ?>" />
        			</div>
        			<h1 class="name"><?php echo $vo['truename']; ?></h1>
        			<h2 class="time"><?php echo time_format($vo['create_time']); ?></h2>
        			<h3 class="description"><?php echo $vo['content']; ?></h3>
        			<?php if($vo['text'] != ''): ?>
        			<div class="content">
        				<div class="link">
        					<span class="color-blue">立即查看 >></span>
        				</div>
        			</div>
        			<?php endif; ?>
	        	</div>
	        	<ul class="lib-operator">
	        		<li class="comment"><i class="fa fa-commenting"></i><span>评论</span></li>
	        		<li <?php if($vo['is_like'] == '1'): ?> class="praise active" <?php else: ?> class="praise" <?php endif; ?>><i class="fa fa-thumbs-up"></i><span>赞</span></li>	        		
	        		<li <?php if($vo['is_tip'] == '1'): ?> class="reward active" <?php else: ?> class="reward" <?php endif; ?>><i class="fa fa-dollar"></i><span>打赏</span></li>
	        		<div class="clearfix"></div>
	        	</ul>
	        	<div class="clearfix"></div>
	        	<div class="lib-reply hide">
	        		<ul class="reply-now">
	        			<li>
	        				<div class="face"><img src="<?php echo $userinfo['userpic']; ?>" /></div>
	        				<input type="text" name="reply-now"/>
	        				<div class="reply-operator">
	        					<i class="fa fa-smile-o color-yellow fa-2x"></i>	        					
	        					<button class="fr">评论</button>
	        				</div>
	        			</li>	        			
	        		</ul>
	        		<ul class="reply-ago">
	        		<?php if(is_array($vo['comment_list']) || $vo['comment_list'] instanceof \think\Collection || $vo['comment_list'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['comment_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$com): $mod = ($i % 2 );++$i;if($com['reply_commont_id'] == '0'): ?>
	        			<li comment_id="<?php echo $com['id']; ?>">
	        				<div class="face"><img src="<?php echo $com['replyer_pic']; ?>" /></div>
	        				<div class="reply-ago-content">
	        					<span class="name color-blue2"><?php echo $com['replyer_name']; ?></span><span>：</span><span class="content"><?php echo $com['reply_content']; ?></span>
	        				</div>
	        				<div class="reply-ago-operator">
	        					<span class="datetime"><?php echo time_format($com['commont_time']); ?></span>
	        					<ul class="fr">
	        						<li>回复</li>
	        						
	        					</ul>
	        				</div>
	        			</li>
	        			<?php else: ?>
	        			<li comment_id="<?php echo $com['id']; ?>">
	        				<div class="face"><img src="<?php echo $com['replyer_pic']; ?>" /></div>
	        				<div class="reply-ago-content">
	        					<span class="name color-blue2" ><?php echo $com['replyer_name']; ?></span><span class="reply-reply">&nbsp;回复&nbsp;<span class="name2 color-blue2"><?php echo $com['reviewer_name']; ?></span></span><span>：</span><span class="content"><?php echo $com['reply_content']; ?></span>
	        				</div>
	        				<div class="reply-ago-operator">
	        					<span class="datetime"><?php echo time_format($com['commont_time']); ?></span>
	        					<ul class="fr">
	        						<li>回复</li>
	        						
	        					</ul>
	        				</div>
	        			</li>
	        			<?php endif; endforeach; endif; else: echo "" ;endif; ?>
	        		</ul>
	        	</div>
    		</li>
    		<?php else: ?>
    		<li class="m-divBlock m-divBlock2 lib" share_id="<?php echo $vo['id']; ?>">
    			<div class="lib-content">
        			<div class="face">
        				<img src="<?php echo $vo['userpic']; ?>" />
        			</div>
        			<h1 class="name"><?php echo $vo['truename']; ?></h1>
        			<h2 class="time"><?php echo time_format($vo['create_time']); ?></h2>
        			<h3 class="description"><?php echo $vo['content']; ?></h3>
        			<div class="content">
        				<div class="picture">
        					<ul class="pic-grid">
        					<?php if(is_array($vo['img']) || $vo['img'] instanceof \think\Collection || $vo['img'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['img'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$im): $mod = ($i % 2 );++$i;?>
        						<li><img src="<?php echo $im; ?>"/></li>
        					<?php endforeach; endif; else: echo "" ;endif; ?>
        						<div class="clearfix"></div>
        					</ul>
        					<div class="pic-show hide">
        						<button class="pack-up-btn">收起</button>
        						<img src=""/>
        					</div>
        					<ul class="pic-list hide">
        						<?php if(is_array($vo['img']) || $vo['img'] instanceof \think\Collection || $vo['img'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['img'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$im): $mod = ($i % 2 );++$i;?>
        						<li><img src="<?php echo $im; ?>"/></li>
        						<?php endforeach; endif; else: echo "" ;endif; ?>
        						<div class="clearfix"></div>
        					</ul>
        				</div>
        			</div>
	        	</div>
	        	<ul class="lib-operator">
	        		<li class="comment"><i class="fa fa-commenting"></i><span>评论</span></li>
	        		<li <?php if($vo['is_like'] == '1'): ?> class="praise active" <?php else: ?> class="praise" <?php endif; ?>><i class="fa fa-thumbs-up"></i><span>赞</span></li>	        		
	        		<li <?php if($vo['is_tip'] == '1'): ?> class="reward active" <?php else: ?> class="reward" <?php endif; ?>><i class="fa fa-dollar"></i><span>打赏</span></li>
	        		<div class="clearfix"></div>
	        	</ul>
	        	<div class="clearfix"></div>
	        	<div class="lib-reply hide">
	        		<ul class="reply-now">
	        			<li>
	        				<div class="face"><img src="<?php echo $userinfo['userpic']; ?>" /></div>
	        				<input type="text" name="reply-now"/>
	        				<div class="reply-operator">
	        					<i class="fa fa-smile-o color-yellow fa-2x"></i>	        					
	        					<button class="fr">评论</button>
	        				</div>
	        			</li>	        			
	        		</ul>
	        		<ul class="reply-ago">
	        		<?php if(is_array($vo['comment_list']) || $vo['comment_list'] instanceof \think\Collection || $vo['comment_list'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['comment_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$com): $mod = ($i % 2 );++$i;if($com['reply_commont_id'] == '0'): ?>
	        			<li comment_id="<?php echo $com['id']; ?>">
	        				<div class="face"><img src="<?php echo $com['replyer_pic']; ?>" /></div>
	        				<div class="reply-ago-content">
	        					<span class="name color-blue2"  ><?php echo $com['replyer_name']; ?></span><span>：</span><span class="content"><?php echo $com['reply_content']; ?></span>
	        				</div>
	        				<div class="reply-ago-operator">
	        					<span class="datetime"><?php echo time_format($com['commont_time']); ?></span>
	        					<ul class="fr">
	        						<li>回复</li>
	        						
	        					</ul>
	        				</div>
	        			</li>
	        			<?php else: ?>
	        			<li comment_id="<?php echo $com['id']; ?>">
	        				<div class="face"><img src="<?php echo $com['replyer_pic']; ?>" /></div>
	        				<div class="reply-ago-content">
	        					<span class="name color-blue2"><?php echo $com['replyer_name']; ?></span><span class="reply-reply">&nbsp;回复&nbsp;<span class="name2 color-blue2"><?php echo $com['reviewer_name']; ?></span></span><span>：</span><span class="content"><?php echo $com['reply_content']; ?></span>
	        				</div>
	        				<div class="reply-ago-operator">
	        					<span class="datetime"><?php echo time_format($com['commont_time']); ?></span>
	        					<ul class="fr">
	        						<li>回复</li>
	        						
	        					</ul>
	        				</div>
	        			</li>
	        			<?php endif; endforeach; endif; else: echo "" ;endif; ?>
	        		</ul>
	        	</div>
    		</li>
    		<?php endif; endforeach; endif; else: echo "" ;endif; ?>
    	</ul>
    	<div class="reward-pop popUp hide">
    		
    	</div>
    	<div class="pay-pop popUp hide">
    		
    	</div>
    </div>
</div>
<link rel="stylesheet" href="/knowledgebase/css/index.css" />
<link rel="stylesheet" href="/static/css/APlayer.min.css" />
<script src="/knowledgebase/js/index.js"></script>
<script src="/static/js/Aplayer.min.js"></script>
<script type="text/javascript">
	function search_share () {
		var key_word = $("input[name='key_word']").val();
	    // alert(key_word);return;
	    var url = "/knowledgebase/corporation_share/index/key_word/"+key_word;
	    var panel = 'company-libraryfr';
	    loadPage(url,panel);
		
	}
</script>
<script src="/static/js/PopUp.js"></script>
<script>
$(".knowledgebase_company_library_index .reward").click(function(){

	var share_id = $(this).parents(".lib").attr("share_id");
	$(".knowledgebase_company_library_index .reward-pop").attr("share_id",share_id);
	var pop3 = new popLoad(".knowledgebase_company_library_index .reward-pop","/knowledgebase/corporation_share/reward/share_id/"+share_id);
});	
</script>
<script>
	var ap = new APlayer({ 
		element: document.getElementById('player1'), 
		narrow:false, 
		autoplay: false, 
		showlrc: false, 
		music: { 
		    title: 'Preparation', 
		    author: 'Hans Zimmer/Richard Harvey', 
		    url: '/knowledgebase/music/1.mp3', 
		    pic: '/webroot/sdzhongxun/images/knowledge/9.jpg' 
		} 
	}); 
	ap.init();
</script>

