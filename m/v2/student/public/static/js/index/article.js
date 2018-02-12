$(function() {
	'use strict';
	var xmlhttp;
	var data;
	var tpl;
	var html;
	var params;
	
	$(document).on('pageInit', '#article_article', function(e, id, $page) {
		$.showPreloader();
		
		//获取首页广告
		var cityid = localStorage.getItem('cityid') ? localStorage.getItem('cityid') : '340100';
		params = 'scene=102&location_type=2&location_id='+cityid+'&device='+device;
		
		//广告图
		ajaxReturn(api_url+'v1/ads/bannerlist?'+params, function() {
			if (xmlhttp.readyState==4) {// 4 = "loaded"
		  		if (xmlhttp.status==200) {// 200 = "OK"
					data = $.parseJSON(xmlhttp.responseText); 
					if(data.code == 200) {
			   			tpl = $('#ads_list_tpl').html();
						html = juicer(tpl, data);
						$('#ads_list').html(html);
						
						//首页轮播图
						var config = {
							loop: true,
						   	autoplay: 3000,
						   	pagination: '.swiper-pagination',
						};
						$(".swiper-container").swiper(config);
					} else {
						$.toast(data.msg);
					}
		    	} else {
//		    		alert("Problem retrieving XML data:" + xmlhttp.statusText);
					$.toast('网络错误，请检查网络');
				}
		  	}
		});
		
		//分类列表
		ajaxReturn(api_url+'v1/news', function() {
			if (xmlhttp.readyState==4) {
		  		if (xmlhttp.status==200) {
					data = $.parseJSON(xmlhttp.responseText); 
					if(data.code == 200) {
		  				tpl = $('#cate_list_tpl').html();
						html = juicer(tpl, data.data);
						$('#cate_list').html(html);
						var _cate_id = cate_id == 1 ? data.data.cate_list['0'].id : cate_id;
						
						//初始化分类下的文章
						params = 'cate_id='+_cate_id+'&page=1&type=1&device='+device;
						ajaxReturn(api_url+'v1/category?'+params, function() {
							if (xmlhttp.readyState==4) {
		  						if (xmlhttp.status==200) {
									data = $.parseJSON(xmlhttp.responseText); 
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
											params = 'cate_id='+cate_id+'&page='+next_page+'&type=1&device='+device;
											ajaxReturn(api_url+'v1/category?'+params, function() {
												if (xmlhttp.readyState==4) {
		  											if (xmlhttp.status==200) {
		  												data = $.parseJSON(xmlhttp.responseText); 
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
											});
										
										});
		  							}		
		  						}
		  					}
						});
						
						//点击获取分类下的文章 ajax
						$('#cate_list').find('a').click(function() {
							$.showPreloader();
							$('#page_more').attr('data-page', 1);
							var data_href = $(this).attr('data-href');
							var data_id = $(this).attr('data-id');
							//获取内容
							$(this).addClass('active').siblings().removeClass('active');
							var params = 'cate_id='+data_id+'&page=1&type=1&device='+device;
							ajaxReturn(api_url+'v1/category?'+params, function() {
								$.hidePreloader();
								if (xmlhttp.readyState==4) {
			  						if (xmlhttp.status==200) {
			  							data = $.parseJSON(xmlhttp.responseText); 
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
			  					}
							});
						});
			  		} else {
			  			$.toast(data.msg)
			  		}
	  			}	
			}
		});
		$.hidePreloader();
		
	});
	
	//文章详情
	$(document).on('pageInit', '#article_article_detail', function(e, id, $page) {
		params = 'id='+article_id+'&device='+device;
		ajaxReturn(api_url+'v1/article/detail?'+params, function() {
			if (xmlhttp.readyState==4) {
				if (xmlhttp.status==200) {
					data = $.parseJSON(xmlhttp.responseText); 
					if(data.code == 200) {
						var tpl = $('#article_article_content_tpl').html();
						var html = juicer(tpl, data.data);
						$('#article_article_content').html(html);
						$('#article_message').html(data.data.message);
						$('#article_title').html(data.data.title);
						$('.xiha_title').html(data.data.title);
						if(localStorage.getItem('votes_num')) {
							$('#votes_num').html(localStorage.getItem('votes_num'));
						}

						//点赞
						$('.article_votes_btn').on('touchstart', function(e) {
							e.preventDefault();
							$(this).unbind('touchstart');
							$(this).addClass('active');
							$('#votes_num').html(parseInt($('#votes_num').text()) + 1);
							localStorage.setItem('votes_num', $('#votes_num').text());
						});
						
						//分享
						$('#share').on('touchstart', function(e) {
							e.preventDefault();
							$('#share_guide').css('display', 'block');
						})
						$('#share_guide').on('touchstart', function(e) {
							e.preventDefault();
							$(this).css('display', 'none');			
						})
					} else {
						$.toast(data.msg);
					}
				} else {
					$.toast('网络错误，请检查网络');
				}
			}
		});
	});
	
	function ajaxReturn(url, _func) {
		xmlhttp=null;
		if (window.XMLHttpRequest) {// code for IE7, Firefox, Opera, etc.
		  	xmlhttp=new XMLHttpRequest();
	  	} else if (window.ActiveXObject) {// code for IE6, IE5
		  	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  	}
		if (xmlhttp!=null) {
		  	xmlhttp.onreadystatechange=_func;
		  	xmlhttp.open("GET", url, false);
		  	xmlhttp.send(null);
	  	} else {
		  	alert("Your browser does not support XMLHTTP.");
	  	}
	}
	//ajax获取数据
	$.init();

});
