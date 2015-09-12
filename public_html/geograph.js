//	-	-	-	-	-	-	-	-

var IE = document.all?true:false;


function popupOSMap(gridref,gridref2)
{
	if (!gridref && gridref2.length)
		gridref = gridref2;
        var wWidth = 740;
        var wHeight = 520;
        var wLeft = Math.round(0.5 * (screen.availWidth - wWidth));
        var wTop = Math.round(0.5 * (screen.availHeight - wHeight)) - 20;
        if (gridref.length > 0) {
        	if (gridref.length < 7) {
			gridref = gridref.substr(0,gridref.length-2)+'5'+gridref.substr(gridref.length-2,2)+'5';
		}
	var newWin = window.open('http://getamap.ordnancesurvey.co.uk/getamap/frames.htm?mapAction=gaz&gazName=g&gazString='+gridref, 
		'gam',
		'left='+wLeft+',screenX='+wLeft+',top='+wTop+',screenY='+wTop+',width='+wWidth+',height='+wHeight+',status,scrolling=no');
	} else {
	var newWin = window.open('http://getamap.ordnancesurvey.co.uk/getamap/frames.htm', 
		'gam',
		'left='+wLeft+',screenX='+wLeft+',top='+wTop+',screenY='+wTop+',width='+wWidth+',height='+wHeight+',status,scrolling=no');
	}
}

//	-	-	-	-	-	-	-	-

function setCaretTo(obj, pos) { 
    if(obj.createTextRange) { 
        /* Create a TextRange, set the internal pointer to
           a specified position and show the cursor at this
           position
        */ 
        var range = obj.createTextRange(); 
        range.move("character", pos); 
        range.select(); 
    } else if(obj.selectionStart) { 
        /* Gecko is a little bit shorter on that. Simply
           focus the element and set the selection to a
           specified position
        */ 
        obj.focus(); 
        obj.setSelectionRange(pos, pos); 
    } 
}

function tabClick(tabname,divname,num,count) {
	for (var q=1;q<=count;q++) {
		document.getElementById(tabname+q).className = (num==q)?'tabSelected':'tab';
		if (divname != '') {
			document.getElementById(divname+q).style.display = (num==q)?'':'none';
		}
	}
}

//	-	-	-	-	-	-	-	-


function autoDisable(that) {
 	that.value = "Submitting... Please wait...";
 	name = "document."+that.form.name+"."+that.name;
  
 	setTimeout(name+".disabled = true",100); //if we disable in the function then form wont submit
 	setTimeout(name+".value="+name+".defaultValue; "+name+".disabled = false",30000);
 	return true;
}

//	-	-	-	-	-	-	-	-

/* FIXME remove other instances of this */
function geoGetXMLRequestObject() // stolen from admin/moderation.js
{
	var xmlhttp=false;
		
	/*@cc_on @*/
	/*@if (@_jscript_version >= 5)
	// JScript gives us Conditional compilation, we can cope with old IE versions.
	// and security blocked creation of the objects.
	 try {
	  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	 } catch (e) {
	  try {
	   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  } catch (E) {
	   xmlhttp = false;
	  }
	 }
	@end @*/
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
	  xmlhttp = new XMLHttpRequest();
	}

	return xmlhttp;
}

function imgvote(imageid, type, vote) {
	var classbase = [ '', 'voteneg', 'voteneg', 'voteneu', 'votepos', 'votepos' ];
	var postdata = 'imageid=' + imageid;
	if (vote) {
		postdata += '&type=' + type;
		postdata += '&vote=' + vote;
	}
	var url="/imgvote.php";
	var req=geoGetXMLRequestObject();
	var reqTimer = setTimeout(function() {
	       req.abort();
	}, 30000);
	req.onreadystatechange = function() {
		if (req.readyState != 4) {
			return;
		}
		clearTimeout(reqTimer);
		req.onreadystatechange = function() {};
		commiterrors = true;

		if (req.status != 200) {
			if (vote) {
				alert("Cannot communicate with server, status " + req.status);
			}
		} else {
			var responseText = req.responseText;
			//alert(responseText);// FIXME remove
			if (/^-[1-9][0-9]*:[^:]+$/.test(responseText)) { /* general error */
				var parts = responseText.split(':');
				var rcode = parseInt(parts[0]);
				if (vote) {
					alert("Error: Server returned error " + -rcode + " (" + parts[1] + ")");
				}
			} else if (! /^0(:[^:]+:[0-5])*$/.test(responseText)) {
				if (vote) {
					alert("Unexpected response from server: "+responseText);
				}
			} else {
				var parts = responseText.split(':');
				for (var i = 1; i < parts.length; i+=2) {
					var curtype = parts[i];
					var curvote = parts[i+1];
					for (var j = 1; j <= 5; ++j) {
						var ele = document.getElementById('vote'+imageid+curtype+j);
						if (ele) {
							if (j != curvote) {
								ele.className = classbase[j];
							} else {
								ele.className = classbase[j] + 'active';
							}
						}
					}
				}
			}
		}
	}
	req.open("POST", url, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	//req.setRequestHeader("Connection", "close");
	req.send(postdata);
}

//	-	-	-	-	-	-	-	-

function record_vote(type,id,vote) {
	var i=new Image();
	i.src= "/stuff/record_vote.php?t="+type+"&id="+id+"&v="+vote;
	document.getElementById("votediv"+id).innerHTML = "Thank you!";
}

function star_hover(id,vote,num) {
	for (var i=1;i<=vote;i++) {
		document.images['star'+i+id].src = document.images['star'+i+id].src.replace(/light/,'on');
	}
}
function star_out(id,num) {
	for (var i=1;i<=num;i++) {
		document.images['star'+i+id].src = document.images['star'+i+id].src.replace(/-on/,'-light');
	}
}

//	-	-	-	-	-	-	-	-


function di20(id, newSrc) {
    var theImage = FWFindImage(document, id, 0);
    if (theImage) {
        theImage.src = newSrc;
    }
}

function FWFindImage(doc, name, j) {
    var theImage = false;
    if (doc.getElementById) {
    	theImage = doc.getElementById(name);
    }
    if (theImage) {
	    return theImage;
	}
   
    
    if (doc.images) {
        theImage = doc.images[name];
    }
    if (theImage) {
        return theImage;
    }
   
   if (doc.layers) {
        for (j = 0; j < doc.layers.length; j++) {
            theImage = FWFindImage(doc.layers[j].document, name, 0);
            if (theImage) {
                return (theImage);
            }
        }
    }
    return (false);
}

//	-	-	-	-	-	-	-	-

function setdate(name,date,form) {
	parts = date.split('-');
	parts[2] = parseInt(parts[2],10);
	parts[1] = parseInt(parts[1],10);
	ele = form.elements[name+'Year'].options;
	for(i=0;i<ele.length;i++) 
		if (ele[i].value == parts[0]) 
			ele[i].selected = true;
	ele = form.elements[name+'Month'].options;
	for(i=0;i<ele.length;i++) 
		if (parseInt(ele[i].value,10) == parts[1]) 
			ele[i].selected = true;
	ele = form.elements[name+'Day'].options;
	for(i=0;i<ele.length;i++) 
		if (parseInt(ele[i].value,10) == parts[2]) 
			ele[i].selected = true;
}

//	-	-	-	-	-	-	-	-

function onChangeImageclass()
{
	if (document.getElementById('otherblock')) {
		var sel=document.getElementById('imageclass');
		var idx=sel.selectedIndex;

		var isOther=idx==sel.options.length-1;

		var otherblock=document.getElementById('otherblock');
		otherblock.style.display=isOther?'':'none';
	}
}

//	-	-	-	-	-	-	-	-

function unescapeHTML_function() {
	var div = document.createElement('div');
	div.innerHTML = this;
	return div.childNodes[0] ? div.childNodes[0].nodeValue : '';
}
function fakeUnescapeHTML_function() {
	return this;
}

if (document.getElementById) {
	String.prototype.unescapeHTML = unescapeHTML_function;
} else {
	String.prototype.unescapeHTML = fakeUnescapeHTML_function;
}

//	-	-	-	-	-	-	-	-

function populateImageclass() 
{
	var sel=document.getElementById('imageclass');
	var opt=sel.options;
	var idx=sel.selectedIndex;
	var idx_value = null;
	if (idx > 0)
		idx_value = opt[idx].value;
	var first_opt = new Option(opt[0].text,opt[0].value);
	var last_opt = new Option(opt[opt.length-1].text,opt[opt.length-1].value);

	//clear out the options
	for(q=opt.length;q>=0;q=q-1) {
		opt[q] = null;
	}
	opt.length = 0; //just to confirm!

	//re-add the first
	opt[0] = first_opt;

	newselected = -1;
	//add the recent list
	if (typeof catListUser != "undefined" && catListUser.length > 1) {
		for(i=0; i < catListUser.length; i++) {
			if (catListUser[i].length > 0) {
				act = catListUser[i].unescapeHTML();
				var newoption = new Option(act,act);
				if (idx_value == act) {
					newoption.selected = true;
					newselected = opt.length;
				}
				opt[opt.length] = newoption;
			}
		}
		var newoption = new Option('-----','-----');
		opt[opt.length] = newoption;
	}
	//add the whole list
	for(i=0; i < catList.length; i++) {
		if (catList[i].length > 0) {
			act = catList[i].unescapeHTML();
			var newoption = new Option(act,act);
			if (idx_value == act) {
				newoption.selected = true;
				newselected = opt.length;
			}
			opt[opt.length] = newoption;
		}
	}

	//if our value is not found then use other textbox!
	if (newselected < 1 && idx_value != null) {
		var selother=document.getElementById('imageclassother');
		selother.value = idx_value;
		idx_value = 'Other';
	} else {
		sel.selectedIndex = newselected;
	}

	//re add the other option
	opt[opt.length] = last_opt;
	if (idx_value != null && idx_value == 'Other')
		sel.selectedIndex=opt.length-1;

	onChangeImageclass();
}

var hasloaded = false;
function prePopulateImageclass() {
	if (!hasloaded) {
		var sel=document.getElementById('imageclass');
		sel.disabled = false;
		var oldText = sel.options[0].text;
		sel.options[0].text = "please wait...";
		
		populateImageclass();
		
		hasloaded = true;
		sel.options[0].text = oldText;
		if (document.getElementById('imageclass_enable_button'))
			document.getElementById('imageclass_enable_button').disabled = true;
	}
}

//	-	-	-	-	-	-	-	-

function checkstyle(that,name,finalize) {
	var valid = true;
	var type = null;
	var v = that.value;
	if (v.length > 1) {
		var f = v.substr(0,1);
		if (f.match(/[a-z]/)) {
			valid = false;
			type = 'missing initial capital';
		}
		
		if (v.toUpperCase() == v || v.toLowerCase() == v) {
			valid = false;
			type = 'single case';
		}
		
		var l = v.substr(-1);
		var l3 = v.substr(-3);
		if (name == 'title' && l == '.' && l3 != '...') {
			valid = false;
			type = 'full stop';
		}

		if (name == 'title2' && l == '.' && l3 != '...') {
			valid = false;
			type = 'full stop';
		}

		if (finalize && !v.match(/ /)) {
			valid = false;
			type = 'very short';
		}
		
		if (name == 'comment' && that.form.title.value == v) {
			valid = false;
			type = 'duplicate of title';
		}

		if (name == 'comment2' && that.form.title2.value == v) {
			valid = false;
			type = 'duplicate of title';
		}
	}
	
	document.getElementById(name+'style').style.display = valid?'none':'';
	document.getElementById(name+'stylet').innerHTML = type?("("+type+")"):'';
	document.getElementById('styleguidelink').style.backgroundColor = valid?'':'yellow';
}

//	-	-	-	-	-	-	-	-

function markImage(image) {
	current = readCookie('markedImages');
	newtext = 'marked';
	if (current) {
		re = new RegExp("\\b"+image+"\\b");
		if (current == image || current.search(re) > -1) {
			newCookie = current.replace(re,',').commatrim();
			newtext = 'Mark';
		} else {
			newCookie = current + ',' + image;
		}
	} else {
		newCookie = image.toString();
	}

	createCookie('markedImages',newCookie,10);

	if (document.getElementById('marked_number')) {
		if (!newCookie) {//chrome needs this... 
			document.getElementById('marked_number').innerHTML = '[0]';
		} else {
			splited = newCookie.commatrim().split(',');
			document.getElementById('marked_number').innerHTML = '['+(splited.length+0)+']';
		}
	}

	ele = document.getElementById('mark'+image);
	if(ele.innerText != undefined) {
		ele.innerText = newtext;
	} else {
		ele.textContent = newtext;
	}
}

function markAllImages(str) {
	for(var q=0;q<document.links.length;q++) {
		if (document.links[q].text == str) {
			markImage(document.links[q].id.substr(4));
		}
	}
}

String.prototype.commatrim = function () {
	return this.replace(/^,+|,+$/g,"").replace(/,,/g,',');
}

function importToMarkedImages() {
	newCookie = readCookie('markedImages');
	if (!newCookie)
		newCookie = new String();
	list = prompt('Paste your current list, either comma or space separated\n or just surrounded with [[[ ]]] ','');
	if (list && list != '') {
		splited = list.split(/[^\d]+/);
		count=0;	
		for(i=0; i < splited.length; i++) {
			image = splited[i];
			if (image != '')
				if (newCookie.search(new RegExp("\\b"+image+"\\b")) == -1) {
					newCookie = newCookie + ',' + image;
					count=count+1;
				}
		}
		createCookie('markedImages',newCookie,10);
		showMarkedImages();
		leng = newCookie.commatrim().split(',').length;
		alert("Added "+count+" image(s) to your list, now contains "+leng+" images in total.");
	} else {
		alert("Nothing to add");
	}
}

function displayMarkedImages() {
	current = readCookie('markedImages');
	if (current) {
		splited = current.commatrim().split(',');
		newstring = '[[['+splited.join(']]] [[[')+']]]';
		prompt("Copy and Paste the following into the forum",newstring);
	} else {
		alert("You haven't marked any images yet. Or cookies are disabled");
	}
}

function returnMarkedImages() {
	current = readCookie('markedImages');
	if (current) {
		splited = current.commatrim().split(',');
		return '[[['+splited.join(']]] [[[')+']]]';
	} else {
		alert("You haven't marked any images yet. Or cookies are disabled");
		return '';
	}
}

function showMarkedImages() {
	current = readCookie('markedImages');
	if (current) {
		splited = current.commatrim().split(',');
		
		var hasInnerText = (document.getElementsByTagName('body')[0].innerText != undefined)?true:false;
		
		for(i=0; i < splited.length; i++) 
			if (document.getElementById('mark'+splited[i])) {
				ele = document.getElementById('mark'+splited[i])
				if(hasInnerText) {
				    ele.innerText = 'marked';
				} else {
				    ele.textContent = 'marked';
				}
			}
		if (document.getElementById('marked_number')) {
			document.getElementById('marked_number').innerHTML = '['+(splited.length+0)+']';
		}
	} 
}


function clearMarkedImages() {
	current = readCookie('markedImages');
	if (current && confirm('Are you sure?')) {
		splited = current.commatrim().split(',');

		var hasInnerText = (document.getElementsByTagName('body')[0].innerText != undefined)?true:false;
		
		for(i=0; i < splited.length; i++) 
			if (document.getElementById('mark'+splited[i])) {
				ele = document.getElementById('mark'+splited[i])
				if(hasInnerText) {
				    ele.innerText = 'Mark';
				} else {
				    ele.textContent = 'Mark';
				}
			}
		eraseCookie('markedImages');
		alert('All images removed from your list');
		if (document.getElementById('marked_number')) {
			document.getElementById('marked_number').innerHTML = '[0]';
		}
	} 
}

//	-	-	-	-	-	-	-	-

function handleCSRFError(msg)
{
	if (typeof geograph_CSRF_token === 'undefined') {
		return;
	}
	if (typeof msg === 'undefined') { msg = "An error occurred and was corrected. Please try again."; }
	var url="/session.php";
	var postdata="action=CSRF_token";

	var req=getXMLRequestObject();
	var reqTimer = setTimeout(function() {
	       req.abort();
	}, 30000);
	req.onreadystatechange = function() {
		if (req.readyState != 4) {
			return;
		}
		clearTimeout(reqTimer);
		req.onreadystatechange = function() {};
		if (req.status != 200) {
			alert("CSRF recovery: Cannot communicate with server, status " + req.status);
			return;
		}
		var responseText = req.responseText;
		//alert(responseText);// FIXME remove
		if (/^-[1-9][0-9]*:[0-9]*:.*$/.test(responseText)) { /* error */
			var parts = responseText.split(':');
			var rcode = parseInt(parts[0]);
			var rinfo = parseInt(parts[1]);
			alert("CSRF recovery: Server returned error " + -rcode + " (" + parts[2] + ")");
		} else if (/^0:.*$/.test(responseText)) { /* success */
			geograph_CSRF_token = responseText.substring(2);
			alert(msg);
		} else {
			alert("CSRF recovery: Unexpected response from server");
		}
	}
	req.open("POST", url, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	//req.setRequestHeader("Connection", "close");
	req.send(postdata);
}

function handleAuthError()
{
	if (typeof geograph_user_id === 'undefined' || typeof geograph_CSRF_token === 'undefined') {
		return;
	}
	var pass = prompt("Authentication error! Please enter your password and try again.", "");
	if (pass === null) {
		return;
	}
	var url="/session.php";
	var postdata="action=login&u="+geograph_user_id+"&CSRF_token="+encodeURIComponent(geograph_CSRF_token)+"&password="+encodeURIComponent(pass);

	var req=getXMLRequestObject();
	var reqTimer = setTimeout(function() {
	       req.abort();
	}, 30000);
	req.onreadystatechange = function() {
		if (req.readyState != 4) {
			return;
		}
		clearTimeout(reqTimer);
		req.onreadystatechange = function() {};
		if (req.status != 200) {
			alert("Cannot communicate with server, status " + req.status);
			return;
		}
		var responseText = req.responseText;
		//alert(responseText);// FIXME remove
		if (/^-[1-9][0-9]*:[0-9]*:.*$/.test(responseText)) { /* error */
			var parts = responseText.split(':');
			var rcode = parseInt(parts[0]);
			var rinfo = parseInt(parts[1]);
			if (rcode == -5) {
				handleCSRFError("Login denied for security reasons, please try again");
			} else if (rcode == -4) {
				var timestr = rinfo < 120 ? rinfo + ' seconds' : Math.ceil(rinfo/60) + ' minutes';
				alert("Authentication error: Access blocked for " + timestr);
			} else if (rcode == -3) {
				alert("Invalid password");
			} else {
				alert("Error: Server returned error " + -rcode + " (" + parts[2] + ")");
			}
		} else if (/^0:.*$/.test(responseText)) {
			/* success */
		} else {
			alert("Unexpected response from server");
		}
	}
	req.open("POST", url, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	//req.setRequestHeader("Connection", "close");
	req.send(postdata);
}

function timestr(t)
{
	var tseconds = t % 60;
	t = Math.floor(t / 60);
	var tminutes = t % 60;
	t = Math.floor(t / 60);
	var thours = t;
	var s = ('0'+tseconds).slice(-2);
	if (thours || tminutes) {
		s = ('0'+tminutes).slice(-2) + ':' + s;
	}
	if (thours) {
		s = thours + ':' + s;
	}
	return s;
}

function buttontimer(id, seconds)
{
	if (seconds <= 0) {
		return;
	}
	var button = document.getElementById(id);
	if (!button) {
		return;
	}
	button.disabled = true;
	var buttontext = button.value;
	var secondsleft = seconds;
	button.value = buttontext + ' (' + timestr(secondsleft) + ')';
	var intv = setInterval(function () {
		secondsleft--;
		if (secondsleft <= 0) {
			clearInterval(intv);
			button.disabled = false;
			button.value = buttontext;
		} else {
			button.value = buttontext + ' (' + timestr(secondsleft) + ')';
		}
	}, 1000);
}

//	-	-	-	-	-	-	-	-

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+encodeURIComponent(value)+expires+"; path=/";
}

function readCookie(name) {
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var pos = ca[i].indexOf("=");
		var argname = ca[i].substring(0,pos);

		while (argname.charAt(0)==' ') argname = argname.substring(1,argname.length);
		if (argname == name) return decodeURIComponent(ca[i].substring(pos+1));
	}
	return false;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}


//	-	-	-	-	-	-	-	-

	function show_tree(id, display) {
		if (typeof display === "undefined") {
			var display = '';
		}
		document.getElementById("show"+id).style.display=display;
		document.getElementById("hide"+id).style.display='none';
		if (typeof resizeContainer != 'undefined') {
			setTimeout(resizeContainer,100);
		}
	}
	function hide_tree(id) {
		document.getElementById("show"+id).style.display='none';
		document.getElementById("hide"+id).style.display='';
		if (typeof resizeContainer != 'undefined') {
			setTimeout(resizeContainer,100);
		}
	}

//	-	-	-	-	-	-	-	-

var marker1left = 14;
var marker1top = 14;

var marker2left = 14;
var marker2top = 14;

function overlayHideMarkers(e) {
	if (IE || e.layerX == null) {
		tempX = event.offsetX;
		tempY = event.offsetY;
	} else {
		tempX = e.layerX
		tempY = e.layerY
	}
	
	var m1 = document.getElementById('marker1');
	
	m1left = parseInt(m1.style.left)+marker1left;
	m1top = parseInt(m1.style.top)+marker1top;
	found = false;
	if (Math.abs(tempX - m1left) < marker1left && Math.abs(tempY - m1top) < marker1top) {
		m1.style.display = 'none';
	} else {
		m1.style.display = displayMarker1?'':'none';
	}
	
	var m2 = document.getElementById('marker2');

	m2left = parseInt(m2.style.left)+marker2left;
	m2top = parseInt(m2.style.top)+marker2top;

	if (Math.abs(tempX - m2left) < marker2left && Math.abs(tempY - m2top) < marker2top) {
		m2.style.display = 'none';
	} else {
		m2.style.display = displayMarker2?'':'none';
	}
	
	return false;
}

//	-	-	-	-	-	-	-	-

//*** This code is copyright 2003 by Gavin Kistner, gavin@refinery.com
//*** It is covered under the license viewable at http://phrogz.net/JS/_ReuseLicense.txt
//*** Reuse or modification is free provided you abide by the terms of that license.
//*** (Including the first two lines above in your source code satisfies the conditions.)

//***Cross browser attach event function. For 'evt' pass a string value with the leading "on" omitted
//***e.g. AttachEvent(window,'load',MyFunctionNameWithoutParenthesis,false);

function AttachEvent(obj,evt,fnc,useCapture){
	if (!useCapture) useCapture=false;
	if (obj.addEventListener){
		obj.addEventListener(evt,fnc,useCapture);
		return true;
	} else if (obj.attachEvent) return obj.attachEvent("on"+evt,fnc);
	else{
		MyAttachEvent(obj,evt,fnc);
		obj['on'+evt]=function(){ MyFireEvent(obj,evt) };
	}
} 

//The following are for browsers like NS4 or IE5Mac which don't support either
//attachEvent or addEventListener
function MyAttachEvent(obj,evt,fnc){
	if (!obj.myEvents) obj.myEvents={};
	if (!obj.myEvents[evt]) obj.myEvents[evt]=[];
	var evts = obj.myEvents[evt];
	evts[evts.length]=fnc;
}
function MyFireEvent(obj,evt){
	if (!obj || !obj.myEvents || !obj.myEvents[evt]) return;
	var evts = obj.myEvents[evt];
	for (var i=0,len=evts.length;i<len;i++) evts[i]();
}

