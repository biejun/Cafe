(function(c,r){
  var navPath = c.path + 'welcome';
  var Nav = function(title, url, child){
	if(url instanceof Array 
	  && typeof child === 'undefined') {
		child = url;
		url = 'none';
	}
    this.title = title;
    this.url = url;
    this.child = child || [];
	this.hasChild = !!(child && child.length > 0);
  }
  var Header = function(){
    this.currentPath = r.path;
    this.nav = ko.observableArray([
        new Nav('首页',navPath+'/index')
        ,new Nav('用户',navPath+'/users')
		,new Nav('控制台', [
			new Nav('系统设置',navPath+'/console/config'),
			new Nav('数据备份',navPath+'/console/backup'),
		])
    ]);
	this.userNav = ko.observableArray([
		new Nav('个人资料',navPath+'/profile?do=edit')
		,new Nav('网站前台',c.path)
		,new Nav('退出登录',c.path+'/logout')
	]);
	this.userName = _CONFIG_.username;
  }

  ko.applyBindings(new Header(),document.getElementById('ko-nav'));
  
  $('#ko-nav .ui.dropdown').dropdown({
  	on: 'click',
	action: 'nothing'
  });

})(_CONFIG_,new UrlRequest);