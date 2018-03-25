(function(global){

	function Json(data){
		var rvalidchars = /^[\],:{}\s]*$/,
			rvalidescape = /\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,
			rvalidtokens = /"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,
			rvalidbraces = /(?:^|:|,)(?:\s*\[)+/g;

		if(typeof data!== "string" || !data) return null;

		data = data.trim();

		if(global.JSON && global.JSON.parse){
			return global.JSON.parse( data );
		}
		if(rvalidchars.test(data.replace(rvalidescape,"@").replace(rvalidtokens,"]").replace(rvalidbraces,""))){
			return (new Function("return " + data))();
		}
	}

	function Ajax(){

	}

	Ajax.prototype = {
		get : function(url, data, success, fail, async){
			var query = [];
			for (var key in data) {
				query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
			}

			this.send(url + (query.length ? '?' + query.join('&') : ''), 'GET', null, success, fail, async)
		},
		post : function(url, data, success, fail, async){
			var query = [];
			for (var key in data) {
				query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
			}
			this.send(url,'POST', query.join('&'), success, fail, async)
		},
		send : function(url, method, data, success, fail, async){
			if (async === undefined) async = true;
			var xhr;
			if (typeof XMLHttpRequest !== 'undefined') {
				xhr = new XMLHttpRequest();
			}else{
				var versions = [
					"MSXML2.XmlHttp.6.0",
					"MSXML2.XmlHttp.5.0",
					"MSXML2.XmlHttp.4.0",
					"MSXML2.XmlHttp.3.0",
					"MSXML2.XmlHttp.2.0",
					"Microsoft.XmlHttp"
				];
				for (var i = 0; i < versions.length; i++) {
					try {
						xhr = new ActiveXObject(versions[i]);
						break;
					} catch (e) {
					}
				}
			}
			xhr.open(method, url, async);
			xhr.onreadystatechange = function () {
				if (xhr.readyState == 4) {
					var status = xhr.status;
					if (status >= 200 && status < 300) {
						success && success(Json(xhr.responseText),xhr.responseXML,xhr);
					} else {
						fail && fail(status);
					}
				}
			};
			if(method == 'POST'){
				xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			}
			xhr.send(data);
		}
	}

	global.Ajax = Ajax;

})(this);