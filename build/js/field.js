"use strict";(function(a,b){'use strict';"undefined"!=typeof b&&b.addAction("new_field/type=google_fonts",function(c){var d=c.$el,e=d.find(".acf-google_fonts"),f=e.find(".acf-google_fonts-choice select"),g=e.find(".acf-google_fonts-preview");f.select2();var h=["variants","subsets"],i=null,j=f.data("js-name"),k=f.data("js-key"),l=f.data("js-action"),m=f.data("js-token"),n=function(a){return console.log(a)},o=function(c){if(g.length){var d=g.text();g.empty(),a("<link>",{rel:"stylesheet",type:"text/css",href:c.data.importUrl}).appendTo(g),a("<div>",{contenteditable:!0,text:d||b._e("google_fonts","preview_text"),style:"font-family: ".concat(c.data.family,";")}).appendTo(g)}for(var f=0;f<h.length;f++){var i=h[f],l=e.find(".acf-google_fonts-".concat(i," .acf-google_fonts-js_values"));if(l.empty(),i in c.data){var m=a("<ul>",{class:"acf-google_fonts-list"}).appendTo(l);for(var n in c.data[i]){var o=c.data[i][n],p="_".concat(k,"_").concat(i,"_").concat(n),q=a("<li>").appendTo(m);a("<input>",{type:"checkbox",name:"".concat(j,"[").concat(i,"][]"),value:n,id:p}).appendTo(q),a("<label>",{text:o,for:p}).appendTo(q)}}}};f.on("change load",function(){i&&"undefined"!=typeof i.abort&&i.abort(),f.val()&&(e.css({opacity:.4,"pointer-events":"none"}),i=a.ajax({url:b.data.ajaxurl,type:"post",dataType:"json",data:{action:l,csrf:m,family:f.val()}}).done(function(a,b,c){a.success?o(a):n(c)}).fail(function(a){n(a)}).always(function(){i=null,e.css({opacity:1,"pointer-events":""})}))}).trigger("load")})})(jQuery,window.acf);