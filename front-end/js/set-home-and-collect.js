	// 设置为主页
	function SetHome(obj,url){
		try{
			obj.style.behavior='url(#default#homepage)';obj.setHomePage(url);
		}
		catch(e){
			if(window.netscape) {
				try {
					netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
				}
				catch (e) {
					alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将 [signed.applets.codebase_principal_support]的值设置为'true',双击即可。");
				}
				var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
				prefs.setCharPref('browser.startup.homepage',url);
			}else{
				alert("您的浏览器不支持，请按照下面步骤操作：1.打开浏览器设置。2.点击设置网页。3.输入："+url+"点击确定。");
			}
		}
	}
	// 加入收藏 兼容360和IE6
	function collect(title,url) {
		try
		{
			window.external.addFavorite(url, title);
		}
		catch (e)
		{
			try
			{
				window.sidebar.addPanel(title, url, "");
			}
			catch (e)
			{
				alert("加入收藏失败，您的浏览器不支持，请关闭该提示框后使用Ctrl+D进行添加");
			}
		}
	}

	//导航栏初始化
	var menu = new cbpTooltipMenu( document.getElementById( 'cbp-tm-menu' ) );

	$('#cbp-tm-menu>li>a').on('mouseover', function(){
		$(this).css({
			'background-color' : '#fff',
			'color' : '#90000a',
		});
	}).on('mouseleave', function(){
		$(this).css({
			'background-color' : '#90000a',
			'color' : '#fff',
		});
	});

	$(function(){
			$('#slides').slides({
				preload: true,
				preloadImage: 'imgs/loading.gif',
				play: 5000,
				pause: 2500,
				hoverPause: true,
				animationStart: function(current){
					if (window.console && console.log) {
						// example return of current slide number
						//console.log('animationStart on slide: ', current);
					};
				},
				animationComplete: function(current){
					if (window.console && console.log) {
						// example return of current slide number
						//console.log('animationComplete on slide: ', current);
					};
				},
				slidesLoaded: function() {
				}
			});
		});