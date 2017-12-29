$("").ready(function() {
		$(".structure_list .employee_import_from .content .nc").click(function() { //最上边的下拉按钮
			$(".select_box").toggleClass("active")
		})
		$(".structure_list .employee_import_from ul li .drop").click(function() { //点击第一、二层下拉按钮
			$(this).parent('li').children(".yincang").toggleClass("active");
			if($(this).parent('li').children(".yincang").hasClass("active")) {
				$(this).attr('src', "/systemsetting/img/tra_dowm.png")
			} else {
				$(this).attr('src', "/systemsetting/img/tra_right.png")
			}
		});

		//相应到右边的框中

		$('.structure_list .employee_import_from .select_box').on('click', "input:checkbox", function() {
			$('.structure_list .employee_import_from .selected ul').html("")
			$(".structure_list .employee_import_from input:checked").each(function(a,b) {
				var index = $(b).attr('index');
				$('.structure_list .employee_import_from .selected ul').append('<li index = ' + index + '>' + $(b).prev("label").html() + '<img src=' + "/systemsetting/img/del.png" + ' index = "' + index + '"/></li>')
				$(this).prev("label").html()
			})
			$(".structure_list .employee_import_from .renshu").html($(".selected ul li").length);

			//从右边移除
			$('.structure_list .employee_import_from .selected').on('click', "ul li img", function() {
				var index1 = $(this).attr("index");
				var T = $(this);
				//		console.log($(this).attr("index"))
				$('.structure_list .employee_import_from .select_box input:checkbox').each(function() {
					if($(this).attr('index') == index1) {
						$(this).prop('checked', false);
						//		$(this).parents('.bumen').find("input.selectAll").prop('checked', false);
						//		$(this).parents('.sanceng').prev("input:checkbox").prop('checked', false);					
						$(T).parent().remove();
					}
				})
				$(".structure_list .employee_import_from .renshu").html($(".selected ul li").length)
			});
		})

		//搜索
		$(".structure_list .employee_import_from .search").on("click", function() {
			var str = $(".ipt1").val();
			if(str == "") {
				alert("请输入搜索内容")
			} else {
				$(".structure_list .employee_import_from .yincang").removeClass("active");
				$(".structure_list .employee_import_from img.drop").attr('src', "/systemsetting/img/tra_right.png");
				$(".structure_list .employee_import_from .bumen label").each(function(index, b) {
					var reg = new RegExp(str, 'igm');
					if(reg.test($(b).html())) {
						$(b).parents(".yincang").addClass("active");
						$(b).parents(".yincang").prevAll('img.drop').attr('src', '/systemsetting/img/tra_dowm.png')
					}
				})
			}
		})

		$('.structure_list .employee_import_from .content').click(function() {

			$('.structure_list .employee_import_from .content .nc').toggleClass('rotate');
			$('.structure_list .employee_import_from .select_box').toggle();
			$('.structure_list .employee_import_from .selected').toggle();
		})
		$(".wancheng").click(function() {
			$(".structure_list .employee_import_from .content").empty();
			$(".structure_list .employee_import_from .content").html('<img src="/systemsetting/img/tra_dowm.png" class="nc" />');
			$(".structure_list .employee_import_from .selected ul li").each(function() {
				var content = $(this).text();
				$('.structure_list .employee_import_from .content').append('<span>' + content + '</span>');
			});
			$('.structure_list .employee_import_from .select_box').toggle();
			$('.structure_list .employee_import_from .selected').toggle();
		})

	})