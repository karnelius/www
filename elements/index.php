<?php
/***************************************************************************\
| Sypex Viewer for MySQL     version 1.0.0 Beta                             |
| (c) 2003-2010 zapimir      zapimir@zapimir.net       http://sypex.net/    |
| (c) 2005-2010 BINOVATOR    info@sypex.net                                 |
|---------------------------------------------------------------------------|
|     created: 2010.11.11 19:07              modified: 2010.11.12 15:04     |
|---------------------------------------------------------------------------|
| Sypex Viewer for MySQL is released under the terms of the BSD license     |
|   http://sypex.net/bsd_license.txt                                        |
\***************************************************************************/
header("Content-Type: text/html; charset=utf-8");
if(!ini_get('zlib.output_compression') && function_exists('ob_gzhandler')) ob_start('ob_gzhandler');

if(!empty($_POST['ajax'])){
	$ajax = $_POST['ajax'];
	$host = !empty($ajax['host']) ? $ajax['host'] : 'localhost';
	$user = !empty($ajax['user']) ? $ajax['user'] : 'root';
	$pass = isset($ajax['pass'])  ? $ajax['pass'] : '';
	$db   = isset($ajax['db'])  ? $ajax['db'] : 'test';
	mysql_connect($host, $user, $pass) or my_error();
	mysql_select_db($db) or my_error();
	mysql_query("/*!40101 SET NAMES 'utf8' */") or my_error();
	
	if(!empty($ajax['act'])){
		$r = mysql_query('SHOW TABLES') or my_error();
		$tables = array('--- Select Table ---');
		while($row = mysql_fetch_row($r)) {
			$tables[] = $row[0];
		}
		echo("miniSQL.tab(2); miniSQL.fillSelect(" . sxd_php2json($tables). ");");
	}
	else {
		$from  = !empty($ajax['from']) ? intval($ajax['from']) : 0;
		$limit = !empty($ajax['limit']) ? abs(intval($ajax['limit'])) : 50;
		$where = !empty($ajax['where']) ? ' WHERE ' . $ajax['where'] : '';
		$table = !empty($ajax['table']) ? $ajax['table'] : '';
		$orderBy  = !empty($ajax['orderBy']) ? $ajax['orderBy'] : '';
		$orderAsc = !empty($ajax['orderAsc']) ? 'ASC' : 'DESC';

		$r = mysql_query("SHOW COLUMNS FROM `{$table}`") or my_error();
		$fields = array();
		$f = array();
		// Перебираем поля, стиль для каждого типа, и обрезаем текстовые поля до 150 символов
		while($col = mysql_fetch_array($r)) {
		    if     (preg_match('/(tinyint|smallint|mediumint|bigint|int)/', $col['Type'])) $type = 1;
			elseif (preg_match('/(float|double|real|decimal|numeric)/', $col['Type'])) $type = 2;
			elseif (preg_match('/(blob|text|char)/', $col['Type'])) $type = 3;
			elseif (preg_match('/(time|date|year)/', $col['Type'])) $type = 4;
			else $type = 0;
		    $f[] = preg_match("/(blob|text|char)/", $col['Type']) ? "LEFT(`{$col['Field']}`, 150)" : "`{$col['Field']}`";
		    $fields[] = array($col['Field'], $type, $col['Key'] ? 1 : 0);
		}
		echo "zGrid.init('zGrid', {header:" . sxd_php2json($fields) . "});";
		// Достаем данные для счетчика, а потом и сами данные
		$r = mysql_query("SELECT COUNT(*) FROM `{$table}`{$where}") or my_error();
		$count = mysql_fetch_row($r);
		$total = $count[0];
		$last = floor($total / $limit) * $limit;
		$f = implode(',', $f);
		$order = !empty($orderBy) ? " ORDER BY `{$orderBy}` {$orderAsc}" : '';
		$r = mysql_query("SELECT {$f} FROM `{$table}`{$where}{$order} LIMIT {$from}, {$limit}") or my_error();
		$rows = array();
		while($row = mysql_fetch_row($r)){
			$rows[] = $row;
		}
		echo("miniSQL.last = {$last}; miniSQL.setTotal({$total});zGrid.add(" . sxd_php2json($rows). ");");
	}
}
else{
	echo tpl_main();
}

function my_error(){
	echo ('alert("MySQL Error (' . mysql_errno() . '): ' . mysql_escape_string(mysql_error()) . '");');exit;
}
	
function sxd_php2json($obj){
	if(count($obj) == 0) return '[]';
	$is_obj = isset($obj[count($obj) - 1]) ? false : true;
	$str = $is_obj ? '{' : '[';
    foreach ($obj AS $key  => $value) {
    	$str .= $is_obj ? "'" . addcslashes(htmlspecialchars($key, ENT_NOQUOTES, 'UTF-8'), "\n\r\t'\\/") . "'" . ':' : ''; 
        if     (is_array($value))   $str .= sxd_php2json($value);
        elseif (is_null($value))    $str .= 'null';
        elseif (is_bool($value))    $str .= $value ? 'true' : 'false';
		elseif (is_numeric($value)) $str .= $value;
		else                        $str .= "'" . addcslashes(htmlentities($value, ENT_NOQUOTES, 'UTF-8'), "\n\r\t'\\/") . "'";
		$str .= ',';
    }
	return  substr_replace($str, $is_obj ? '}' : ']', -1);
}

function tpl_main(){
	return <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Sypex Viewer for MySQL</title>
<style>
* {margin:0;padding:0;font:11px tahoma,arial;}
html, body {height:100%;background-color:#f0f0f0;overflow:auto;}
#main{
	position:absolute;
	left:50%;
	top:50%;
	margin:-342px 0 0 -493px;
	width:980px;
	border:1px solid #919B9C;
	padding:1px;
	text-align:left;
	cursor:default;
}
#cont {
	background-color: #fff;
	height:675px;
}
#head{
	font: 13px verdana,arial;
	color: #fff;
	padding: 3px 5px;
	height: 16px;
	font-weight: bold;
	margin-bottom: 1px;
	background-color: #306191;
}
#toolbar {
	background-color: #fff;
	padding: 3px 5px;
	height:20px;
	border-bottom: 1px solid #ccc9b8;
}
form{
	display:inline;
	margin:0;
}
input{
	width:90px;	
}
select{
	width:140px;	
}
input[type=button]{
	width:30px;	
}

.zGrid table {
	table-layout: fixed;
}
.zGrid {
	border:1px solid #828790;
	overflow:hidden;
	padding:1px;
	margin-top:2px;
	background-color:#fff;
	cursor: default;
}
.zGrid th, .zGrid td {
	font-weight: normal;
	text-align:left;
	padding: 2px 5px 2px 3px;
	text-overflow:ellipsis;
	white-space:nowrap;
    vertical-align:top;
	overflow:hidden;
}
.zGrid #row0{
	height:0;
}
.zGrid #row0 td {
	padding: 0 5px 0 3px;
	height:0;
	border-bottom: 0;
	border-top: 0;
}
.zGrid td.type0{
	color: black;
}
.zGrid td.type1{
	color: blue;
	text-align:right;
}
.zGrid td.type2{
	color: navy;
	text-align:right;
}
.zGrid td.type3{
	color: green;
}
.zGrid td.type4{
	color: brown;
}
.zGrid th:hover {
	
}
.zGrid .header div {
	float:left;
	height:22px;
}
.zGrid .header table {
	margin-right:25px;
	height:22px;
}
.zGrid td.wrap {
    white-space: normal;
}
.zGrid .header {
	overflow:hidden;
	background: url(data:image/gif;base64,R0lGODlhAQAWAKIAAP////b3+fHy9Pf4+vX2+PP09vT19/Lz9SH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QTA1REVFNzlFQzVCMTFERkI4MkZDQ0ExMTg5RDFDRTUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QTA1REVFN0FFQzVCMTFERkI4MkZDQ0ExMTg5RDFDRTUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpBMDVERUU3N0VDNUIxMURGQjgyRkNDQTExODlEMUNFNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpBMDVERUU3OEVDNUIxMURGQjgyRkNDQTExODlEMUNFNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAAAAAAALAAAAAABABYAAAMLCLo7E5AQU8oRIgEAOw==) repeat-x;
	background-color: #f5f6f8;
}
.zGrid #order {
	position:relative;
	width: 7px;
	height: 4px;
	left:100px;
	top:-22px;
	z-index:100;
	float:left;
	display:none;
	background: url(data:image/gif;base64,R0lGODlhBwAIAMQdAMTj9DxecnKryrXd8nCpxsPk9WGWtoO62V6IoUNfb1KLq1abwJrL5obI63Oy2LPb8Wes05rH4GKaucrm9ZnQ7pHL7JXG4Jq2xUBidLri9DxZbIexxabY8////wAAAAAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NkNDNTc5RjZFRTE2MTFERkI5NTA5RDI4REZEOEFCNUMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NkNDNTc5RjdFRTE2MTFERkI5NTA5RDI4REZEOEFCNUMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo2Q0M1NzlGNEVFMTYxMURGQjk1MDlEMjhERkQ4QUI1QyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo2Q0M1NzlGNUVFMTYxMURGQjk1MDlEMjhERkQ4QUI1QyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAEAAB0ALAAAAAAHAAgAAAUkYCdiYhksSKkZTXV1yZYNwFQEEiEcVtQpDsoDUIJwiKUOoxQCADs=) no-repeat 0 0;
}
.zGrid #order.desc{
	background-position: 0 -4px;
}
.zGrid th {
	border:1px solid;
	padding: 3px 5px 4px 3px;
	border-color: #fff #e3e4e6 #d5d5d5 #fcfdfd;
}
.zGrid td{
	border: 1px solid transparent;
}
.zGrid tr:hover td{
	background-color: #eff8fd;
	border: 1px solid #eff8fd;
}
.zGrid tr.sel td{
	background-color: #d7effc;
	border: 1px solid #d7effc;
}
</style>
<script>
var miniSQL = {
	init: function() {
		this.from  = z('from');
		this.limit = z('limit');
		this.where = z('where');
		this.table = z('table');
		this.sTotal = z('total');
		this.tab1 = z('tab1');
		this.tab2 = z('tab2');
		this.from.value  = 0;
		this.limit.value = 50;
		this.last = 999;
		this.total = 0;
		this.orderBy  = '';
		this.orderAsc = 1;
		this.orderPos = 0;

		var t = this;
		this.where.onkeypress = this.limit.onkeypress = this.from.onkeypress = function(e){
			e = e || window.event;
			if(e.keyCode == 13) {
				switch(e.id) {
					case 'where': t.select(1);break;
					default: t.select(0);break;
				}
			}
		};
		this.table.onchange = function(e){
			 t.changeTable();
		};
		zGrid.init('zGrid', {header:[]});
		this.tab(1);
	},
	connect: function(){
		this.host = z('host').value;
		this.user = z('user').value;
		this.pass = z('pass').value;
		this.db = z('db').value;
		ajax.post('index.php', null, {host: this.host, user: this.user, pass: this.pass, db: this.db, act: 'connect'},1);
	},
	disconnect: function(){
		z('host').value = 'localhost';
		this.user = this.pass = this.db = z('user').value = z('pass').value = z('db').value= '';
		this.tab(1);
		zGrid.clear();
	},
	setTotal: function(total){
		this.total = total;
		this.sTotal.innerHTML = this.total;
	},
	select: function(num){
		if(this.table.value == '--- Select Table ---') return;
		zGrid.clear();
		var from = this.from.value*1;
		var limit = this.limit.value*1;
		if (limit < 1 || limit > 500) limit = 50;

		switch(num){
			case 1: from = 0; break;
			case 2: from -= limit; break;
			case 3: from += limit; break;
			case 4: from = this.last; break;
		}
		if (from < 0) from = 0;
		this.from.value = from;
		this.limit.value = limit;

		ajax.post('index.php', null, {host: this.host, user: this.user, pass: this.pass, db: this.db, from: from, limit: limit, where: this.where.value, table: this.table.value, orderBy: this.orderBy, orderAsc: this.orderAsc ? 1 : 0},1);
	},
	tab: function(tab){
		this.tab1.style.display = tab == 1 ? '' : 'none';
		this.tab2.style.display = tab == 2 ? '' : 'none';
		document.onkeypress = tab == 1 ? function(e){
			e = e || window.event;
			if(e.keyCode == 13)	miniSQL.connect();
		} : function(){};
	},
	fillSelect: function(items){
		var sel = z('table');
		sel.options.length = 0;
		var to = items.length;
		for(var i in items){
			var newOpt = document.createElement("OPTION");
			newOpt.value = items[i];
			newOpt.text = items[i];
			sel.options.add(newOpt);
		}
	},
	changeTable: function(){
		this.orderBy  = '';
		this.orderAsc = 1;
		this.orderPos = 0;
		this.where.value = '';
		this.select(1);
	}

};
var ajax = {
	post: function(url, onload, data, sync){
		sync = sync || true;
		data = data || null;
		onload = onload || function(){};
		var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : (window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : null);
		if (!xhr){
			alert('Not work Ajax');
		}
		else{
	        xhr.open('POST', url, sync);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	        if (sync) {
	            xhr.onreadystatechange = function(){
					if (xhr.readyState == 4) {
						if (xhr.status == 200) {
							eval(xhr.responseText);
							onload.call(xhr);
						}
					}
				}
	        }
	        xhr.send(obj2php(data));
        }
	}
};
function obj2php(obj, depth){
	var s = '';
	depth = depth || '';
	for(var o in obj){
		s += typeof obj[o] == 'object' ? obj2php(obj[o], depth + '[' + o + ']') : 'ajax'+ depth + '[' + o + ']=' + encodeURIComponent(obj[o]) + '&';
	}
	return s;
}
function z(elem){
	return document.getElementById(elem);
}
var zGrid = {
	init: function (elem, cfg){
			this.el    = z(elem);
			this.name  = elem;
			cfg.width = cfg.width || 980;
			cfg.height = cfg.height || 620;
			this.cfg   = cfg;
			this.el.className = 'zGrid';
			var header = '';
			var dataRow = '';
			if(cfg.header.length == 0) cfg.header = [['', cfg.width-1]]; 
			this.cols = cfg.header.length;
			var width = 0;
			for(var i = 0; i < this.cols; i++) {
				header += '<th>' + cfg.header[i][0] + '</th>';
				dataRow += '<td></td>';
			}
			this.el.innerHTML = '<div class=header><div><table cellspacing=0><tr>' + header + '</tr></table><div id=order></div></div></div><div style="overflow-x:auto;overflow-y:auto;"><table cellspacing=0><tr id=row0>' + dataRow + '</tr></table></div>';

			this.dDiv = this.el.lastChild;
			this.hDiv = this.el.firstChild;
			this.data = this.dDiv.lastChild;
			this.head = this.hDiv.firstChild.firstChild;
			this.oDiv = z('order');
			this.dDiv.style.height  = (cfg.height - 23) + 'px';
			this.dDiv.style.width   = (cfg.width-1) + 'px';

			var _this = this;
			this.selected = null;
			this.add = function(rows){
				if(typeof rows[0] == 'object') { // Получили несколько строк
					for(var row in rows) this.addRow(rows[row]);
				}
				else this.addRow(rows);
				this.recountWidth();
			};
			this.dDiv.onclick = function(){
				if(_this.selected){
					_this.selected.className = '';
					_this.selected = null;
				}
			};
			this.hDiv.onclick = function(e){
				e = e || window.event;
				var t = e.target || e.srcElement;
				if(miniSQL.orderBy != _this.cfg.header[t.cellIndex][0]) miniSQL.orderAsc = 0;
				else {miniSQL.orderAsc = !miniSQL.orderAsc;}
				miniSQL.orderBy = _this.cfg.header[t.cellIndex][0];
				miniSQL.orderPos = t.cellIndex;
				miniSQL.select(0);
				if (e.stopPropagation) e.stopPropagation();
				e.cancelBubble = true;
			};
			this.dDiv.onscroll = function(e){
				e = e || window.event;
				var t = e.target || e.srcElement;
				_this.hDiv.scrollLeft = t.scrollLeft;
			};
		},
		addRow: function(row){
			if(row.length == 0) return;
			var newrow = this.data.insertRow(-1);
			var td;
			for(var i = 0; i < this.cols; i++){
				td = newrow.insertCell(-1);
				if(this.cfg.header[i][1]) td.className = 'type' + this.cfg.header[i][1];
				if(this.cfg.header[i][2] == 1) td.style.fontWeight = 'bold';
				td.innerHTML = row[i];
				td.title     = td.innerText || td.textContent;
			}
			newrow.file = row[this.cols];
			var _this = this;
			newrow.onmousedown = function(e){
				e = e || window.event;
				var t = e.target || e.srcElement;
				if(_this.selected) _this.selected.className = '';
				this.className = 'sel';
				_this.selected = this;
				var op = '=';
				var value = t.textContent || t.innerText;
				switch(t.className){
					case 'type0':
					case 'type4': value = "'" + value + "'";
					case 'type1':
					case 'type2': if(e.ctrlKey || e.shiftKey) op = (e.ctrlKey ? '<' : '') + (e.shiftKey ? '>' : ''); break;
					case 'type3': value = "'" + (e.ctrlKey ? '' : '%') + value + (e.shiftKey ? '' : '%') + "'";
								  op = e.ctrlKey && e.shiftKey ? '!=' : 'LIKE';
					              break;
				}
				if(e.altKey) miniSQL.where.value = '';
				miniSQL.where.value += (miniSQL.where.value.length > 0 ? (e.button > 0 ? ' OR ' : ' AND ') : '') +  '`' + _this.cfg.header[t.cellIndex][0] + '` ' + op + ' ' + value;
				miniSQL.where.focus();
				if (e.stopPropagation) e.stopPropagation();
				e.cancelBubble = true;
				return false;
			};
		},
		recountWidth: function(){
			var totalWidth = 0;
			var w = 0;
			var a = [];
			var l = this.cfg.header.length;
			for (var i = 0; i < l; i++) {
				w = (this.data.rows[0].cells[i].offsetWidth > this.head.rows[0].cells[i].offsetWidth ? this.data.rows[0].cells[i].offsetWidth : this.head.rows[0].cells[i].offsetWidth);
				if(w > 300) w = 300;
				if(w < 20)  w = 20;
				totalWidth += w;
				a[i] = w;
			}
			totalWidth = 0;
			var last = l - 1;
			for (i = 0; i < l; i++) {
				w = a[i];
				if(last == i && this.cfg.header[1] == 3) w = this.dDiv.clientWidth - totalWidth - 10;
				totalWidth += w + 10;
				this.head.rows[0].cells[i].style.width = this.data.rows[0].cells[i].style.width = w + 'px';
			}
			this.head.style.width = totalWidth + 'px';
			this.data.style.width = totalWidth + 'px';
			if(miniSQL.orderBy) {
				var col = this.head.rows[0].cells[miniSQL.orderPos];
				this.oDiv.style.left = (col.offsetLeft + Math.round(col.offsetWidth/2)-3) + 'px';
				this.oDiv.style.display = 'block';
				this.oDiv.className = miniSQL.orderAsc ? '' : 'desc';
			}
		},
		clear: function(){
			for(var i = 1, l = this.data.rows.length; i < l; i++){
				this.data.deleteRow(1);
			}
			this.oldtime = '';
		}
}
</script>
</head>

<body>
<div id="main">
	<div id="cont">
		<div id="head">Sypex Viewer for MySQL</div>
		<div id="toolbar"> 
			<div id="tab1">Host: <input type="text" id="host" value="localhost"> &nbsp;
				User: <input type="text" id="user" value="root"> &nbsp;
				Pass: <input type="password" id="pass"> &nbsp;
				DB: <input type="text" id="db"> &nbsp;
				<input type="button" id="connect" value="Connect" style="width:50px;margin-right:10px;" onclick="miniSQL.connect();">
			</div>
			<div id="tab2">
				Table: <select id="table"></select> &nbsp;
				Where: <input type="text" name="textfield" id="where" style="width:300px;">&nbsp;
				From: <input type="text" name="textfield" id="from" style="width:35px;">&nbsp;
				Limit: <input type="text" name="textfield" id="limit" style="width:35px;" value="50">
				<input type="button" name="button5" id="first" value="|&laquo;" title="First" onclick="miniSQL.select(1);">
				<input type="button" name="button3" id="prev" value="&laquo;" title="Prev" onclick="miniSQL.select(2);">
				<input type="button" name="button4" id="next" value="&raquo;" title="Next" onclick="miniSQL.select(3);">
				<input type="button" name="button6" id="last" value="&raquo;|" title="Last" onclick="miniSQL.select(4);"> &nbsp;Total: <span id="total">0</span>
				<input type="button" id="disconnect" value="Disconnect" style="float:right;width:70px;" onclick="miniSQL.disconnect();">
			</div>
		</div>
		<div id="zGrid"></div>
	</div>
</div>
<script>
miniSQL.init();
</script>
</body>
</html>
HTML;
}
?>
