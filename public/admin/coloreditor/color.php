<!--
	HTML Color Editor v1.2 (c) 2000 by Sebastian Weber <webersebastian@yahoo.de>

	This is a completely JavaScript- and HTML-based color editor
	for inclusion in Webpages.

	The Color Editor Window should be opened via a JavaScript Call
	like:
		window.open('ColorEditor.html','colorchoser',
		'height=250,width=390,dependent=yes,directories=no,location=no,
		menubar=no,resizable=yes,scrollbars=no,status=no,toolbar=no');

	The opening window MUST contain a JS-Function
		setColor(rgbcolor);
	This Function will be called whenever the user presses the "OK" or
	"Apply"-Button in the Color Editor Window.

	Furthermore the opening window MAY contain a variable called
	oldColor which must be set to the value of the old Color before
	opening the Color Editor window.

	Tested with Netscape Navigator v7.01 and MSIE v5.5
	The set of custom colors is stored in a Cookie which virtually
	never expires.

	Have Phun !
        
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2, or (at your option)
	any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
-->
<html>
<head>
<title>Color</title>
<script language="javascript">
<!--
// Old Color and Current Color
var ctrl=window.opener.document.getElementById('<?php echo $_GET["fld"]?>');
	if (window.opener && ctrl)
		oldrgb=ctrl.value.split(" ");
	else oldrgb=[255,0,255];
	//curcol=oldcol;
	currgb=oldrgb;
	
//curcol="FFFFFF";
	//
// Predefined Colors
	pcoc = getCookie("predefcolors");
	if (!pcoc) pcoc="FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,"+
					"FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF";
	precol= new Array(16);
	precol=pcoc.split(",",16);
// Predefined Colors Cursor
	cursorImg=new Image ();
	cursorImg.src="cursor.gif";
	blankImg=new Image ();
	blankImg.src="blank.gif";
	cursorPos=1;
// Other Stuff
	hexchars="0123456789ABCDEF";
	curcol=tohex(currgb[0])+tohex(currgb[1])+tohex(currgb[2]);
	oldcol=curcol;
	//currgb=[fromhex(curcol.substr(0,2)),fromhex(curcol.substr(2,2)),fromhex(curcol.substr(4,2))];
	curhsl=RGBtoHSL(currgb[0],currgb[1],currgb[2]);

// Funktions
	function setCookie(name, value, expire) {
	   document.cookie = name + "=" + escape(value)
	   + ((expire == null) ? "" : ("; expires=" + expire.toGMTString()))
	}

/*	function transferColor () {
		today= new Date();
		expires= new Date ()
		expires.setTime(today.getTime() + 1000*60*60*24*365)
		setCookie("predefcolors",precol.join(","),expires);
		if (window.opener)
			window.opener.setColor(currgb[0]);

	} */
	
	function transferColor () {
		today= new Date();
		expires= new Date ()
		expires.setTime(today.getTime() + 1000*60*60*24*365)
		setCookie("predefcolors",precol.join(","),expires);
		if (window.opener){
			/*window.opener.document.getElementsByName("color_r")[0].value = currgb[0];
			window.opener.document.getElementsByName("color_g")[0].value = currgb[1];
			window.opener.document.getElementsByName("color_b")[0].value = currgb[2];
			window.opener.document.getElementById("color_field").bgColor = curcol;*/
			window.opener.document.getElementById("<?php echo $_GET["fld"]?>").value=currgb[0] + " " + currgb[1] + " " + currgb[2];
			}
	}
		
	function getCookie(Name) {
	   var search = Name + "="
	   if (document.cookie.length > 0) { // if there are any cookies
	      offset = document.cookie.indexOf(search)
	      if (offset != -1) { // if cookie exists
	         offset += search.length
	         // set index of beginning of value
	         end = document.cookie.indexOf(";", offset)
	         // set index of end of cookie value
	         if (end == -1)
	            end = document.cookie.length
	         return unescape(document.cookie.substring(offset, end))
	      }
	   }
	}
	
	function fromhex(inval) {
		out=0;
		for (a=inval.length-1;a>=0;a--)
			out+=Math.pow(16,inval.length-a-1)*hexchars.indexOf(inval.charAt(a));	
		return out;
	}

	function tohex(inval) {
		out=hexchars.charAt(inval/16);
		out+=hexchars.charAt(inval%16);
		return out;
	}

	function setPreColors () {
		for (a=1;a<=16;a++) {
			document.getElementById("precell"+a).bgColor=precol[a-1];
		}
	}
	
	function definePreColor () {
		precol[cursorPos-1]=curcol;
		setPreColors();
		setCursor(cursorPos+1>16?1:cursorPos+1);
	}

	function preset (what) {
		setCol(precol[what-1]);
		setCursor(what);
	}

	function setCursor(what) {
		document.getElementById("preimg"+cursorPos).src=blankImg.src;
		cursorPos=what;
		document.getElementById("preimg"+cursorPos).src=cursorImg.src;
	}
	
	function update() {
		document.getElementById("thecell").bgColor=curcol;
		document.getElementById("rgb_r").value=currgb[0];
		document.getElementById("rgb_g").value=currgb[1];
		document.getElementById("rgb_b").value=currgb[2];
		document.getElementById("htmlcolor").value=curcol;
		document.getElementById("hsl_h").value=curhsl[0];
		document.getElementById("hsl_s").value=curhsl[1];
		document.getElementById("hsl_l").value=curhsl[2];
		setCursor(cursorPos);
		
		// set the cross on the colorpic
		var cross=document.getElementById("cross").style;
		var cp=document.getElementById("colorpic");
		xd=0;yd=0;lr=cp;
		while(lr!=null) {xd+=lr.offsetLeft; yd+=lr.offsetTop; lr=lr.offsetParent;}
		cross.top=(yd-9+191-191*curhsl[1]/255)+"px";
		cross.left=(xd-9+191*curhsl[0]/255)+"px";
		// update slider pointer
		var sa=document.getElementById("sliderarrow").style;
		var sp=document.getElementById("slider");
		xd=0;yd=0;lr=sp;
		while(lr!=null) {xd+=lr.offsetLeft; yd+=lr.offsetTop; lr=lr.offsetParent;}
		sa.top=(yd+187-191*curhsl[2]/255)+"px";
		sa.left=(xd+14)+"px"
		// update slider colors
		for (i=0;i<192;i++) {
			rgb=HSLtoRGB(curhsl[0],curhsl[1],255-255*i/191);
			document.getElementById("sc"+(i+1)).bgColor=tohex(rgb[0])+tohex(rgb[1])+tohex(rgb[2]);
		}
	}

	function HSLtoRGB (h,s,l) {
		if (s == 0) return [l,l,l] // achromatic
		h=h*360/255;s/=255;l/=255;
		if (l <= 0.5) rm2 = l + l * s;
		else rm2 = l + s - l * s;
		rm1 = 2.0 * l - rm2;
		return [ToRGB1(rm1, rm2, h + 120.0),ToRGB1(rm1, rm2, h),ToRGB1(rm1, rm2, h - 120.0)];
	}

	function ToRGB1(rm1,rm2,rh) {
		if      (rh > 360.0) rh -= 360.0;
		else if (rh <   0.0) rh += 360.0;
 		if      (rh <  60.0) rm1 = rm1 + (rm2 - rm1) * rh / 60.0;
		else if (rh < 180.0) rm1 = rm2;
		else if (rh < 240.0) rm1 = rm1 + (rm2 - rm1) * (240.0 - rh) / 60.0;
 		return Math.round(rm1 * 255);
	}

	function RGBtoHSL (r,g,b) {
		min = Math.min(r,Math.min(g,b));
		max = Math.max(r,Math.max(g,b));
		// l
		l = Math.round((max+min)/2);
		// achromatic ?
		if(max==min) {h=160;s=0;}
		else {
		// s
			if (l<128) s=Math.round(255*(max-min)/(max+min));
			else s=Math.round(255*(max-min)/(510-max-min));
		// h	
			if (r==max)	h=(g-b)/(max-min);
			else if (g==max) h=2+(b-r)/(max-min);
			else h=4+(r-g)/(max-min);
			h*=60;
			if (h<0) h+=360;
			h=Math.round(h*255/360);
		}
		return [h,s,l];
	}

	function setOldColor () {
		document.getElementById("theoldcell").bgColor=oldcol;
	}
	
	function setCol(value) {
		value=value.toUpperCase();
		if (value.length!=6) value=curcol;
		for (a=0;a<6;a++)
			if (hexchars.indexOf(value.charAt(a))==-1) {
				value=curcol;break;
			}
		curcol=value;
		currgb=[fromhex(curcol.substr(0,2)),fromhex(curcol.substr(2,2)),fromhex(curcol.substr(4,2))];
		curhsl=RGBtoHSL(currgb[0],currgb[1],currgb[2]);
		update();
	}

	function setRGB(r,g,b) {
		if (r>255||r<0||g>255||g<0||g>255||g<0) {r=currbg[0];g=currgb[1];b=currgb[2];}
		currgb=[r,g,b];
		curcol=tohex(r)+tohex(g)+tohex(b);
		curhsl=RGBtoHSL(r,g,b);
		update();
	}

	function setHSL(h,s,l) {
		if (h>255||h<0||s>255||s<0||l>255||l<0) {
			h=curhsl[0];s=curhsl[1];l=curhsl[2];
		}
		if(l==0 || l==255)l=100;
		curhsl=[h,s,l];
		currgb=HSLtoRGB(h,s,l);
		curcol=tohex(currgb[0])+tohex(currgb[1])+tohex(currgb[2]);
		update();
	}
	
	function setFromRGB () {
		r=document.getElementById("rgb_r").value;
		g=document.getElementById("rgb_g").value;
		b=document.getElementById("rgb_b").value;
		setRGB(r,g,b);
	}

	function setFromHTML () {
		inval=document.getElementById("htmlcolor").value.toUpperCase();
		if (inval.length!=6) {setCol(curcol);return;}
		for (a=0;a<6;a++)
			if (hexchars.indexOf(inval.charAt(a))==-1) {
				setCol(curcol);return;
			}
		setCol(inval);
	}

	function setFromHSL () {
		h=document.getElementById("hsl_h").value;
		s=document.getElementById("hsl_s").value;
		l=document.getElementById("hsl_l").value;
		if (h>255||h<0||s>255||s<0||l>255||l<0) {setHSL(curhsl[0],curhsl[1],curhsl[2]);return;}
		setHSL(h,s,l);
	}

	function setFromImage (event) {
		var x=event.offsetX;
		var y=event.offsetY;
		if (x == undefined) {
			xd=0;yd=0;lr=document.getElementById("colorpic");
			while(lr!=null) {xd+=lr.offsetLeft; yd+=lr.offsetTop; lr=lr.offsetParent;}
			x=event.pageX-xd;
			y=event.pageY-yd;
		}
		setHSL(Math.round(x*255/191),Math.round(255-y*255/191),curhsl[2]);
	}
	
	function setFromSlider (event) {
		yd=0;lr=document.getElementById("slider");
		while(lr!=null) {yd+=lr.offsetTop; lr=lr.offsetParent;}
		y=event.clientY-yd;
		setHSL(curhsl[0],curhsl[1],Math.round(255-y*255/191));
	}

// --> </script>
<style type="text/css">
td {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
input {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
</style>
</head>
<body bgcolor="FFFFFF" onLoad="setPreColors();setCursor(cursorPos);setCol(curcol);setOldColor();">
<table border=0 cellspacing=2 cellpadding=0><form>
	<tr><td align=center>Basic Colors</td>
	<!-- ************** COLORS PICTURE ******************** -->

		<td rowspan=6 width=10><img src=blank.gif width=10></td>
		<td align=center valign="middle" rowspan=6>
		<table border=0 cellspacing=0 cellpadding=0 width=192 height=192><tr>
			<td width=192 height=192><img id="colorpic" height=192 width=192 src="colors.jpg" border=0 onClick="setFromImage(event);"
			onMouseDown="setFromImage(event);"
			onDragOver="setFromImage(event);"
			></td></tr>
		</table>
		</td>
	<!-- ******************* SLIDER ************************ -->
		<td rowspan=6 width=10><img src=blank.gif width=10></td>
		<td rowspan=6 width=14
		><table border=0 cellspacing=0 cellpadding=0 width=24 heigth=192 id="slider"
			onClick="setFromSlider(event);"
			onMouseDown="setFromSlider(event);"
			onDragOver="setFromSlider(event);"
		><tr><td
		><table border=0 cellspacing=0 cellpadding=0 width=14 heigth=192
			><script>
			for (i=0;i<192;i++) document.write("<tr><td id=\"sc"+(i+1)+"\" height=1 width=14></td></tr>");
			</script></table></td>

		<td width=10 height=192><img src=blank.gif width=10></td></tr></table></td>
	</tr>
	<tr><td align=center>
	<!-- ************** BASIC COLORS PALETTE ******************** -->
	<table border=1 cellpadding=0 cellspacing=0>
		<tr height=14>
		<td height=14 width=14 bgcolor="#ff0000"><a
		href="javascript:setCol('ff0000')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#400000"><a
		href="javascript:setCol('400000')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#800000"><a
		href="javascript:setCol('800000')"><img src="blank.gif" width=14
		height=14 border=0></a></td>

		<td height=14 width=14 bgcolor="#c00000"><a
		href="javascript:setCol('c00000')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#ff4040"><a
		href="javascript:setCol('ff4040')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#ff8080"><a
		href="javascript:setCol('ff8080')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#ffc0c0"><a
		href="javascript:setCol('ffc0c0')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#000000"><a
		href="javascript:setCol('000000')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		</tr>
		<tr height=14>
		<td height=14 width=14 bgcolor="#ffff00"><a
		href="javascript:setCol('ffff00')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#404000"><a
		href="javascript:setCol('404000')"><img src="blank.gif" width=14
		height=14 border=0></a></td>

		<td height=14 width=14 bgcolor="#808000"><a
		href="javascript:setCol('808000')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#c0c000"><a
		href="javascript:setCol('c0c000')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#ffff40"><a
		href="javascript:setCol('ffff40')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#ffff80"><a
		href="javascript:setCol('ffff80')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#ffffc0"><a
		href="javascript:setCol('ffffc0')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#202020"><a
		href="javascript:setCol('202020')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		</tr>
		<tr height=14>
		<td height=14 width=14 bgcolor="#00ff00"><a
		href="javascript:setCol('00ff00')"><img src="blank.gif" width=14
		height=14 border=0></a></td>

		<td height=14 width=14 bgcolor="#004000"><a
		href="javascript:setCol('004000')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#008000"><a
		href="javascript:setCol('008000')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#00c000"><a
		href="javascript:setCol('00c000')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#40ff40"><a
		href="javascript:setCol('40ff40')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#80ff80"><a
		href="javascript:setCol('80ff80')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#c0ffc0"><a
		href="javascript:setCol('c0ffc0')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#404040"><a
		href="javascript:setCol('404040')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		</tr>
		<tr height=14>

		<td height=14 width=14 bgcolor="#00ffff"><a
		href="javascript:setCol('00ffff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#004040"><a
		href="javascript:setCol('004040')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#008080"><a
		href="javascript:setCol('008080')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#00c0c0"><a
		href="javascript:setCol('00c0c0')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#40ffff"><a
		href="javascript:setCol('40ffff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#80ffff"><a
		href="javascript:setCol('80ffff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#c0ffff"><a
		href="javascript:setCol('c0ffff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#808080"><a
		href="javascript:setCol('808080')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		</tr>

		<tr height=14>
		<td height=14 width=14 bgcolor="#0000ff"><a
		href="javascript:setCol('0000ff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#000040"><a
		href="javascript:setCol('000040')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#000080"><a
		href="javascript:setCol('000080')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#0000c0"><a
		href="javascript:setCol('0000c0')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#4040ff"><a
		href="javascript:setCol('4040ff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#8080ff"><a
		href="javascript:setCol('8080ff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#c0c0ff"><a
		href="javascript:setCol('c0c0ff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#c0c0c0"><a
		href="javascript:setCol('c0c0c0')"><img src="blank.gif" width=14
		height=14 border=0></a></td>

		</tr>
		<tr height=14>
		<td height=14 width=14 bgcolor="#ff00ff"><a
		href="javascript:setCol('ff00ff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#400040"><a
		href="javascript:setCol('400040')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#800080"><a
		href="javascript:setCol('800080')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#c000c0"><a
		href="javascript:setCol('c000c0')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#ff40ff"><a
		href="javascript:setCol('ff40ff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#ff80ff"><a
		href="javascript:setCol('ff80ff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		<td height=14 width=14 bgcolor="#ffc0ff"><a
		href="javascript:setCol('ffc0ff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>

		<td height=14 width=14 bgcolor="#ffffff"><a
		href="javascript:setCol('ffffff')"><img src="blank.gif" width=14
		height=14 border=0></a></td>
		</tr>
	</table>
	</td>
	</tr>
	<tr height=5><td height=5></td></tr>
	<tr><td align=center>Custom Colors</td></tr>
	<tr><td align="center">

<!-- ************** Custom Colors ******************** -->
	<table border=1 cellpadding=0 cellspacing=0>
		<tr height=14>
		<td width=14 height=14 id="precell1" bgcolor="#ffffff"
		><a
		href="javascript:preset(1)"><img src="blank.gif" width=14
		id="preimg1" height=14 border=0></a></td>
		<td width=14 height=14 id="precell2" bgcolor="#ffffff"
		><a
		href="javascript:preset(2)"><img src="blank.gif" width=14
		id="preimg2" height=14 border=0></a></td>
		<td width=14 height=14 id="precell3" bgcolor="#ffffff"
		><a
		href="javascript:preset(3)"><img src="blank.gif" width=14
		id="preimg3" height=14 border=0></a></td>
		<td width=14 height=14 id="precell4" bgcolor="#ffffff"
		><a
		href="javascript:preset(4)"><img src="blank.gif" width=14
		id="preimg4" height=14 border=0></a></td>
		<td width=14 height=14 id="precell5" bgcolor="#ffffff"
		><a
		href="javascript:preset(5)"><img src="blank.gif" width=14
		id="preimg5" height=14 border=0></a></td>
		<td width=14 height=14 id="precell6" bgcolor="#ffffff"
		><a
		href="javascript:preset(6)"><img src="blank.gif" width=14
		id="preimg6" height=14 border=0></a></td>

		<td width=14 height=14 id="precell7" bgcolor="#ffffff"
		><a
		href="javascript:preset(7)"><img src="blank.gif" width=14
		id="preimg7" height=14 border=0></a></td>
		<td width=14 height=14 id="precell8" bgcolor="#ffffff"
		><a
		href="javascript:preset(8)"><img src="blank.gif" width=14
		id="preimg8" height=14 border=0></a></td>
		</tr>
		<tr height=14>
		<td width=14 height=14 id="precell9" bgcolor="#ffffff"
		><a
		href="javascript:preset(9)"><img src="blank.gif" width=14
		id="preimg9" height=14 border=0></a></td>
		<td width=14 height=14 id="precell10" bgcolor="#ffffff"
		><a
		href="javascript:preset(10)"><img src="blank.gif" width=14
		id="preimg10" height=14 border=0></a></td>
		<td width=14 height=14 id="precell11" bgcolor="#ffffff"
		><a
		href="javascript:preset(11)"><img src="blank.gif" width=14
		id="preimg11" height=14 border=0></a></td>
		<td width=14 height=14 id="precell12" bgcolor="#ffffff"
		><a
		href="javascript:preset(12)"><img src="blank.gif" width=14
		id="preimg12" height=14 border=0></a></td>
		<td width=14 height=14 id="precell13" bgcolor="#ffffff"
		><a
		href="javascript:preset(13)"><img src="blank.gif" width=14
		id="preimg13" height=14 border=0></a></td>

		<td width=14 height=14 id="precell14" bgcolor="#ffffff"
		><a
		href="javascript:preset(14)"><img src="blank.gif" width=14
		id="preimg14" height=14 border=0></a></td>
		<td width=14 height=14 id="precell15" bgcolor="#ffffff"
		><a
		href="javascript:preset(15)"><img src="blank.gif" width=14
		id="preimg15" height=14 border=0></a></td>
		<td width=14 height=14 id="precell16" bgcolor="#ffffff"
		><a
		href="javascript:preset(16)"><img src="blank.gif" width=14
		id="preimg16" height=14 border=0></a></td>
		</tr>
	</table>
	</td>
	</tr>
	</tr>
	<tr><td align=center><input type="button"
		value="Add Custom" onClick="definePreColor()"></td></tr>

	<tr>
	<td valign=top colspan=5>
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr><td align=center>Current Color</td>
		<td valign=middle align=center rowspan=2>
<!-- ************** Display Old Color ******************** -->
		Old Color<br>
		<table border=0 cellspacing=0 cellpadding=0>

			<tr><td width=10 height=53></td>
			<td width=55 height=53 id="theoldcell" bgcolor="#ffffff"><table
			border=1 cellspacing=0 cellpadding=0><tr><td><img src="blank.gif"
			width=55 height=53 border=0></td></tr></table></td>
			<td width=10 height=53></td></tr>
		</table>
		</td></tr>
		<tr><td>
		<table border=1 cellpadding=0 cellspacing=0 width=100%><tr><td>
			<table border=0 cellpadding=0 cellspacing=2 width=100%>
<!-- ************** RGB INPUT ******************** -->

				<tr><td valign=top
				align=right>Red: <input id="rgb_r" type="text" size=3 maxlength=3 value=255
				onChange="setFromRGB()"><br>
				Green: <input id="rgb_g" type="text" size=3 maxlength=3 value=255
				onChange="setFromRGB()"><br>
				Blue: <input id="rgb_b" type="text" size=3 maxlength=3 value=255
				onChange="setFromRGB()"></td>
<!-- ************** HSV INPUT ******************** -->
				<td valign=top align=right>
				Hue: <input id="hsl_h" type="text" size=3 maxlength=3 value=0
				onChange="setFromHSL()"><br>
				Sat: <input id="hsl_s" type="text" size=3 maxlength=3 value=0
				onChange="setFromHSL()"><br>

				Light: <input id="hsl_l" type="text" size=3 maxlength=3 value=255
				onChange="setFromHSL()"></td>
				<td valign=middle align=center rowspan=2>
<!-- ************** Display New Color ******************** -->
				<table border=0 cellspacing=0 cellpadding=0>
					<tr height=10><td width=75 height=10 colspan=3></td></tr>
					<tr height=53>
					<td width=10 height=53> </td>
					<td width=55 height=53 id="thecell" bgcolor="#ffffff"><table
					border=1 cellspacing=0 cellpadding=0><tr><td><img src="blank.gif"
					width=55 height=53 border=0></td></tr></table></td>

					<td width=10 height=53> </td></tr>
					<tr height=10><td width=75 height=10 colspan=3></td></tr>
				</table>
				</td></tr>
				<tr>
				<td colspan=2 align=right>HTML Code: <input id=htmlcolor type=text size=8 maxlength=6 value="FFFFFF"
				onChange="setFromHTML()"></td>
				</tr>
			</table></td></tr>
		</table></td></tr>
	</table></td></tr><tr>
	<td colspan=5 align=center valign=bottom><input type="button"
	value="&nbsp;&nbsp;&nbsp;OK&nbsp;&nbsp;&nbsp;" onClick="transferColor();window.close()">
	<input type="button" value="Apply" onClick="transferColor()">
	<input type="button" value="Cancel" onClick="window.close()"></td>
	</tr>
</form></table>

<img id="cross" SRC="cross.gif" STYLE="position:absolute; left:0px; top:0px">
<img id="sliderarrow" SRC="arrow.gif" STYLE="position:absolute; left:0px; top:0px">
</body>
</html>

