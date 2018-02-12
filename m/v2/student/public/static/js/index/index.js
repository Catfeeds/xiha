$(function() {
	'use strict';
	//首页
	$(document).on('pageInit', '#index', function(e, id ,page) {
		var $content = $(page).find('.content');

		//定位当前城市并获得城市id
		showCityInfo('current-place', 'current-city-point');

		//首页轮播图
		var config = {
			loop: true,
		   	autoplay: 3000,
		   	pagination: '.swiper-pagination',
		};
		$(".swiper-container").swiper(config);

		$(page).on('touchstart', '#city-choose', function(e) {
			e.preventDefault();
			if($(this).find('.icon').hasClass('icon-down')) {
				$(this).find('.icon').addClass('icon-up').removeClass('icon-down');
				$('.index-city-all').stop().animate({
					'top':'2.2rem',
				}, 200);
			} else {
				$(this).find('.icon').addClass('icon-down').removeClass('icon-up');
				$('.index-city-all').stop().animate({
					'top':'-12.8rem',
				}, 200);
			}
		})

		//选择城市
		$('.index-city-list').on('touchstart', '.city-cell', function(e) {
			e.preventDefault();
			$('#current-city-point').text($(this).text());
			$(this).addClass('city-active').parent().siblings().find('.city-cell').removeClass('city-active');
		});

	});
	//报名
	$(document).on('pageInit', "#signup", function(e, id, page) {
		e.preventDefault();
		var $content = $(page).find('.content');
		$(page).find('.signup-filter-all').on('touchstart', '.filter-list', function(e) {
			e.preventDefault();
			$(this).css('color', '#24CC7C').parent().siblings().find('.filter-list').css('color', '#555');
			if($(this).parent().find('.signup-filter-list').css('display') == 'none') {
				$(this).find('.icon').removeClass('icon-down').addClass('icon-up').parents('.col-33').siblings().find('.icon').removeClass('icon-up').addClass('icon-down');
				$(this).parent().find('.signup-filter-list').stop().show(200).parent().siblings().find('.signup-filter-list').hide();
			} else {
				$(this).find('.icon').removeClass('icon-up').addClass('icon-down');
				$(this).parent().find('.signup-filter-list').stop().hide(200);
			}
		});
	});

	//金币商城
	$(document).on('pageInit', '#coinstore', function(e, id, page) {
		e.preventDefault();
		var $content = $(page).find('.content');
		//签到
		$content.on('touchstart', '#coinstore-signin', function(e) {
			e.preventDefault();
			var text_html = '<div class="coinstore-signin-modal"><div class="coinstore-signin-img"><img src="'+_public+'/static/images/coinstore-signin-success-logo.png"></div><h1 style="font-size:1rem; color:#50B33E;">签到成功</h1><p style="font-size:0.8rem; color:#333; margin:10px 0px;">恭喜你获得了10嘻哈币</p><p style="font-size:0.6rem; color:#999;">金币可用于金币商城消费！</p></div>'

			$.modal({
	      		text: text_html,
	      		buttons: [
	      			{
	      				text:'<p class="coinstore-signin-btn" style="">确定</p>',
	      				onClick:function() {
	      					$('.modal').remove();
//	      					$.closeModal();
	      				}
	      			}
	      		]
	     	});
	     	//签到
//	      	$.ajax({
//	      		type:"get",
//	      		url:"",
//	      		beforeSend:function() {
//		 			$.showIndicator();
//	      		},
//	      		async:true,
//	      		dataType:"json",
//	      		data:{},
//	      		success:function(data) {
//	          		$.hideIndicator();
//	      			$.modal({
//			      		text: text_html,
//			      		buttons: [
//			      			{
//			      				text:'<p class="coinstore-signin-btn" style="">确定</p>',
//			      				onClick:function() {
//			      					$('.modal').remove();
//			      				}
//			      			}
//			      		]
//			     	});
//	      		},
//	      		error:function() {
//	          		$.hideIndicator();
//	      			$.toast('网络错误，请检查网络！');
//	      		}
//	      	});

		});

	});

	//金币规则
	$(document).on('pageInit', '#coinstore-rule', function(e, id, page) {
		var $content = $(page).find('.content');
		$('img.lazy').lazyload({
			effect : "fadeIn",
//			threshold : 200
		});
	});

	//金币商城商品详情
	$(document).on('pageInit', '#goodsdetail', function(e, id, page) {
		//首页轮播图
		var config = {
			loop: true,
		   	autoplay: 3000,
		   	pagination: '.swiper-pagination',
		};
		$(".swiper-container").swiper(config);
	});

	//选择城市列表
	$(document).on('pageInit', '#citylist', function(e, id, page) {
//		console.log('citylist')
		var $content = $(page).find('.content');
		//定位当前城市并获得城市id
		showCityInfo('citylist-current-place', '');
	});

	//常见问题
	$(document).on('pageInit', '#issue', function(e, id, page) {
  		var $content = $(page).find('.content');
	    $content.on('touchstart', ".issue-title", function(e) {
	    	e.preventDefault();
	      	$(this).next('.issue-content').slideToggle(1,function() {});
          	$(this).find('.img').toggleClass('imgtop');
	    });
  	});

	// 驾考
	$(document).on('pageInit', '#drive', function(e, id, page) {
  		var $content = $(page).find('.content');
		//选择科目一
		$('#'+id).on('click', '#lesson-choose', function(e) {
			e.preventDefault();
			if($(this).find('.icon').hasClass('icon-down')) {
				$(this).find('.icon').addClass('icon-up').removeClass('icon-down');
				$('.drive-lesson-all').stop().animate({
					'top':'2.2rem',
				}, 200);
			} else {
				$(this).find('.icon').addClass('icon-down').removeClass('icon-up');
				$('.drive-lesson-all').stop().animate({
					'top':'-3.8rem',
				}, 200);
			}
		});
	});

	//个人中心
	$(document).on('pageInit', '#ucenter', function() {

	});

	//用户信息
	$(document).on("pageInit", "#ucenter-info", function(e, pageId, page) {
		var $content = $(page).find('.content');
		//获取用户信息
		var params = "token="+token;
		var _func = function() {
			if(this.readyState === this.DONE) {
				var data = $.parseJSON(this.responseText);
//				console.log(data);
//				$.toast(data.msg);
				if(this.status == 200) {
					if(data.code == 200) {
					    var tpl = document.getElementById('info_temp').innerHTML;
					    var html = juicer(tpl, data.data);
					    document.getElementById('info_res').innerHTML = html;
					} else {

					}
				} else {

				}
			} else {

			}
		};

		ajaxReturn('GET', api_url+'v1/ucenter/profile/student?'+params, '', false, _func, 2)

//		//修改信息
//		$content.on('click','.info-cell', function () {
//	      	$.prompt('确定要修改信息？', function (value) {
//		      	$('.modal').remove();
//	      		if(value.trim() == '') {
//	      			$.toast('请填写修改信息');
//	      		} else {
//		      		$.toast('修改成功');
//	      		}
//
//	      	});
//	  	});

	  	//点击二维码
//	  	$('.info-qrcode').on('touchstart', function(e) {
//			e.preventDefault();
//			var text_html = '<div style="margin:10px;"><img src="'+_public+'/static/images/ucenter-index-qrcode-logo.png" style="width:80%; background:#000"></div>';
//	  		$.modal({
//		      title:  '我的二维码',
//		      text: text_html,
//		      onClick:function() {
//		      	$('.modal').remove();
//		      }
//		   });
//	  	});

	});

	//驾考圈等
	$(document).on('pageInit', '#article', function(e, id, page) {
		var $content = $(page).find('.content');
		console.log(lesson_id);
	});

	//登录
	$(document).on('pageInit', '#login', function(e, id, page) {
		var $content = $(page).find('.content');
		$content.on('touchstart', '.ucenter-login-btn', function(m) {
			m.preventDefault();
			var phone = $('.phone').val()
			var pass = $('.pass').val();
			if(phone.trim() == '' || pass.trim() == '') {
				$.toast('请完善登录信息');
				return false;
			}
			var params = {'phone':phone, 'pass':pass};
			var _func = function() {
				if(this.readyState === this.DONE) {
					var data = $.parseJSON(this.responseText);
					$.toast(data.msg);
					if(this.status == 200) {
						if(data.code == 200) {
							var _url = window.decodeURIComponent(redirect_url)+"?token="+data.data.token+"&device="+device;
							location.href=_url;
							if(storageItem != '') {
								sessionStorage.setItem(storageItem, JSON.stringify(data.data));
							}
						} else {
							return false;
						}
					} else {
						$.toast('网络错误，请检查网络');
					}
				} else {
					$.toast('网络错误，请检查网络');
				}
			};
			ajaxReturn('POST', api_url+'v1/ucenter/login/student', true, params, _func);
		})
	});

	//注册
	$(document).on('pageInit', '#register', function(e, id, page) {

		var $content = $(page).find('.content');
		//注册
		$('#register-btn').on('touchend', function(e) {
			e.preventDefault();

			var phone = $('.phone').val();
			var code = $('.code').val();
			var pass = $('.pass').val();
			if(phone.trim() == '' || code.trim() == '' || pass.trim() == '') {
				$.toast('请完善注册信息');
				return false;
			}
			var params = {
				'phone':phone,
				'code':code,
				'pass':pass,
			};
			var _func = function() {
				if(this.readyState === this.DONE) {
					var data = $.parseJSON(this.responseText);
					$.toast(data.msg);
					if(this.status == 200) {

					}
				}
			};
			ajaxReturn('POST', api_url+'v1/ucenter/register/student', true, params, _func);
		})

		//获取验证码
		$('#getcode-btn').on('touchstart', function(e) {
			e.preventDefault();
			var phone = $('.phone').val();
			if(phone.trim() == '') {
				$.toast('请输入手机号码');
				return false;
			}
			var params = "phone="+phone;
			var _func = function() {
				if(this.readyState === this.DONE) {
					var data = $.parseJSON(this.responseText);
					$.toast(data.msg);
					if(this.status == 200) {
						if(data.code == 200) {

						}
					} else {
						$.toast('网络错误，请检查网络');
					}
				} else {
					$.toast('网络错误，请检查网络');

				}
			}
			ajaxReturn('GET', site_url+'couponcode'+params, false, '', _func, 2);
		});
	});

	//嘻哈号外
	$(document).on('pageInit', '#article_article', function(e, id, page) {

		//获取首页广告
		var cityid = localStorage.getItem('cityid') ? localStorage.getItem('cityid') : '340100';
		var params = 'scene=102&location_type=2&location_id='+cityid+'&device='+device;
		var _func = function() {
			if(this.readyState === this.DONE) {
				var data = $.parseJSON(this.responseText);
				if(this.status == 200) {
					if(data.code == 200) {
						var tpl = document.getElementById('ads_list_tpl').innerHTML;
						var html = juicer(tpl, data);
						document.getElementById('ads_list').innerHTML = html;

						//首页轮播图
						var config = {
							loop: true,
						   	autoplay: 3000,
						   	pagination: '.swiper-pagination',
						};
						$(".swiper-container").swiper(config);
					}
				} else {
					$.toast('网络错误，请检查网络');
				}
			} else {
				// $.toast('网络错误，请检查网络');
			}
		}
		ajaxReturn('GET', api_url+'v1/ads/bannerlist?'+params, false, '', _func);

		//获取初始化分类列表
		var _func = function() {
			if(this.readyState === this.DONE) {
				var data = $.parseJSON(this.responseText);

				if(this.status == 200) {
					if(data.code == 200) {
						var tpl = $('#cate_list_tpl').html();
						var html = juicer(tpl, data.data);
						$('#cate_list').html(html);
						var _cate_id = cate_id == 1 ? data.data.cate_list['0'].id : cate_id;

						// 获取初始化首页分类下的文章列表
						params = 'cate_id='+_cate_id+'&page=1&type=1&device='+device;
						var _func = function() {
							if(this.readyState === this.DONE) {
								var data = $.parseJSON(this.responseText);
								if(this.status == 200) {
									if(data.code == 200) {
										tpl = $('#article_article_list_tpl').html();
										html = juicer(tpl, data.data);
										$('#_article_article_list').html(html);
										if(data.data.current_page == data.data.last_page || data.data.article_list.length == 0) {
											$('#page_more').css('display','none');
										}

										//获取更多文章
										$('#page_more').on('touchstart', function(e) {
											var next_page = parseInt($('#page_more').attr('data-page')) + 1;
											var cate_id = $('#cate_list').find('a.active').attr('data-id');
											params = 'cate_id='+cate_id+'&page=1&type=1&device='+device;
											var _func = function() {
												if(this.readyState === this.DONE) {
													var data = $.parseJSON(this.responseText);
													if(this.status == 200) {
														if(data.code == 200) {
															tpl = $('#article_article_list_tpl').html();
															html = juicer(tpl, data.data);
															$('#_article_article_list').append(html);
															$('#page_more').attr('data-page', next_page);
															if(data.data.current_page == data.data.last_page) {
																$('#page_more').css('display','none');
															}
														} else {
															$.toast(data.msg);
														}
													} else {
														$.toast('网络错误，请检查网络');
													}
												}
											};
											ajaxReturn('GET', api_url+'v1/category?'+params, false, '', _func);
										});
									}
								} else {
									$.toast('网络错误，请检查网络');
								}
							} else {
								// $.toast('网络错误，请检查网络');
							}
						}
						ajaxReturn('GET', api_url+'v1/category?'+params, false, '', _func);

						//点击获取分类下的文章 ajax
						$('#cate_list').find('a').click(function() {
							$('#page_more').attr('data-page', 1);
							var data_href = $(this).attr('data-href');
							var data_id = $(this).attr('data-id');
							//获取内容
							$(this).addClass('active').siblings().removeClass('active');
							var params = 'cate_id='+data_id+'&page=1&type=1&device='+device;
							var _func = function() {
								if(this.readyState === this.DONE) {
									var data = $.parseJSON(this.responseText);
									if(this.status == 200) {
										if(data.code == 200) {
											tpl = $('#article_article_list_tpl').html();
											html = juicer(tpl, data.data);
											$('#_article_article_list').html(html);
											if(data.data.current_page == data.data.last_page || data.data.article_list.length == 0) {
												$('#page_more').css('display','none');
											} else {
												$('#page_more').css('display','block');

											}
										} else {
											$.toast(data.msg);
										}
									} else {
										$.toast('网络错误，请检查网络');
									}
								} else {
									// $.toast('网络错误，请检查网络');
								}
							}
							ajaxReturn('GET', api_url+'v1/category?'+params, false, '', _func);
						});
					}
				} else {
					$.toast('网络错误，请检查网络');
				}
			} else {
//				$.toast('网络错误，请检查网络');
			}
		}
		ajaxReturn('GET', api_url+'v1/news', true, '', _func, 2);
	});

	//文章详情
	$(document).on('pageInit', '#article_article_detail', function(e, id, page) {

		var params = 'id='+article_id+'&device='+device;
		var _func = function() {
			if(this.readyState === this.DONE) {
				var data = $.parseJSON(this.responseText);
				if(this.status == 200) {
					if(data.code == 200) {
						var tpl = document.getElementById('article_article_content_tpl').innerHTML;
						var html = juicer(tpl, data.data);
						document.getElementById('article_article_content').innerHTML = html;
						$('#article_message').html(data.data.message);
//						$('.title').html(data.data.title);
						if(localStorage.getItem('votes_num')) {
							$('#votes_num').html(localStorage.getItem('votes_num'));
						}

						//点赞
						$('.article_votes_btn').on('touchstart', function(e) {
							$(this).addClass('active');
							$('#votes_num').html(parseInt($('#votes_num').text()) + 1);
							localStorage.setItem('votes_num', $('#votes_num').text());

							$('.download_app').css('display','block');
						});

					} else {
						$.toast(data.msg);
					}
				} else {
					$.toast('网络错误，请检查网络');
				}
			} else {
//				$.toast('网络错误，请检查网络');
			}
		};
		ajaxReturn('GET', api_url+'v1/article/detail?'+params, true, '', _func, 2);

	});

	//ajax获取数据
	function ajaxReturn(method, url, async, params='', _func, type=1) {
		var xhr = new XMLHttpRequest();
		xhr.open(method, url, async);
		xhr.setRequestHeader("Content-type","application/json");
		xhr.onreadystatechange = _func;
		xhr.onerror = function() {
			$.toast('网络错误，请检查网络');
		};
		if(type == 1) {
			xhr.withCredentials = true;
			xhr.send(JSON.stringify(params));
		} else {
			xhr.send(null);
		}
	}

	// $.init();
});


