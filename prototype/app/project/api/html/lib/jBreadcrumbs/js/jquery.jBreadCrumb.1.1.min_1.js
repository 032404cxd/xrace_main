/**
 * @author Jason Roy for CompareNetworks Inc.
 * Thanks to mikejbond for suggested udaptes
 *
 * Version 1.1
 * Copyright (c) 2009 CompareNetworks Inc.
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */

;(function(e){function j(){var c=jQuery(a[a.length-1]);jQuery(c).width()>b.maxFinalElementLength&&(0<b.beginingElementsToLeaveOpen&&b.beginingElementsToLeaveOpen--,0<b.endElementsToLeaveOpen&&b.endElementsToLeaveOpen--);jQuery(c).width()<b.maxFinalElementLength&&jQuery(c).width()>b.minFinalElementLength&&0<b.beginingElementsToLeaveOpen&&b.beginingElementsToLeaveOpen--;var f=a.length-1-b.endElementsToLeaveOpen;jQuery(a[a.length-1]).css({background:"none"});e(a).each(function(c,a){if(c>b.beginingElementsToLeaveOpen&& c<f){jQuery(a).find("a").wrap("<span></span>").width(jQuery(a).find("a").width()+10);jQuery(a).append(jQuery('<div class="'+b.overlayClass+'"></div>').css({display:"block"})).css({background:"none"});e.browser.msie&&/MSIE\s(5\.5|6\.)/.test(navigator.userAgent)&&k(jQuery(a).find("."+b.overlayClass).css({width:"20px",right:"-1px"}));var d={id:c,width:jQuery(a).width(),listElement:jQuery(a).find("span"),isAnimating:false,element:jQuery(a).find("span")};jQuery(a).bind("mouseover",d,g).bind("mouseout", d,h);jQuery(a).find("a").unbind("mouseover",g).unbind("mouseout",h);a.autoInterval=setInterval(function(){clearInterval(a.autoInterval);jQuery(a).find("span").animate({width:b.previewWidth},b.timeInitialCollapse,b.easing)},150*(c-2))}})}function g(a){var f=a.data.width;jQuery(a.data.element).stop();jQuery(a.data.element).animate({width:f},{duration:b.timeExpansionAnimation,easing:b.easing,queue:!1});return!1}function h(a){jQuery(a.data.element).stop();jQuery(a.data.element).animate({width:b.previewWidth}, {duration:b.timeCompressionAnimation,easing:b.easing,queue:!1});return!1}function k(a){var b;jQuery(a).is("img")?b=jQuery(a).attr("src"):(b=e(a).css("backgroundImage"),b.match(/^url\(["']?(.*\.png)["']?\)$/i),b=RegExp.$1);e(a).css({backgroundImage:"none",filter:"progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=scale, src='"+b+"')"})}var b={},d={},a={},i;jQuery.fn.jBreadCrumb=function(c){b=e.extend({},e.fn.jBreadCrumb.defaults,c);return this.each(function(){d=e(this);i= "object"==typeof jQuery.easing?"easeOutQuad":"swing";a=jQuery(d).find("li");jQuery(d).find("ul").wrap('<div style="overflow:hidden; position:relative; width: '+jQuery(d).css("width")+';"><div>');jQuery(d).find("ul").width(5E3);0<a.length&&(jQuery(a[a.length-1]).addClass("last"),jQuery(a[0]).addClass("first"),a.length>b.minimumCompressionElements&&j())})};jQuery.fn.jBreadCrumb.defaults={maxFinalElementLength:400,minFinalElementLength:200,minimumCompressionElements:4,endElementsToLeaveOpen:1,beginingElementsToLeaveOpen:1, timeExpansionAnimation:800,timeCompressionAnimation:500,timeInitialCollapse:600,easing:i,overlayClass:"chevronOverlay",previewWidth:5}})(jQuery);