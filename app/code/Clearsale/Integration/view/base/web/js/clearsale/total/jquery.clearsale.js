/*Extensão para verificar existência de um controle com o selector informado*/
jQuery.exists = function (selector) { return ($(selector).length > 0); }
;
/*Plugin metadata*/
(function($){$.extend({metadata:{defaults:{type:'class',name:'metadata',cre:/({.*})/,single:'metadata'},setType:function(type,name){this.defaults.type=type;this.defaults.name=name},get:function(elem,opts){var settings=$.extend({},this.defaults,opts);if(!settings.single.length)settings.single='metadata';var data=$.data(elem,settings.single);if(data)return data;data="{}";if(settings.type=="class"){var m=settings.cre.exec(elem.className);if(m)data=m[1]}else if(settings.type=="elem"){if(!elem.getElementsByTagName)return undefined;var e=elem.getElementsByTagName(settings.name);if(e.length)data=$.trim(e[0].innerHTML)}else if(elem.getAttribute!=undefined){var attr=elem.getAttribute(settings.name);if(attr)data=attr}if(data.indexOf('{')<0)data="{"+data+"}";data=eval("("+data+")");$.data(elem,settings.single,data);return data}}});$.fn.metadata=function(opts){return $.metadata.get(this[0],opts)}})(jQuery);
;
/*Plugin tablesorter*/
(function($){$.extend({tablesorter:new function(){var parsers=[],widgets=[];this.defaults={cssHeader:"header",cssAsc:"headerSortUp",cssDesc:"headerSortDown",sortInitialOrder:"asc",sortMultiSortKey:"shiftKey",sortForce:null,sortAppend:null,textExtraction:"simple",parsers:{},widgets:[],widgetZebra:{css:["even","odd"]},headers:{},widthFixed:false,cancelSelection:true,sortList:[],headerList:[],dateFormat:"uk",decimal:'.',debug:false};function benchmark(s,d){log(s+","+(new Date().getTime()-d.getTime())+"ms");}this.benchmark=benchmark;function log(s){if(typeof console!="undefined"&&typeof console.debug!="undefined"){console.log(s);}else{alert(s);}}function buildParserCache(table,$headers){if(table.config.debug){var parsersDebug="";}var rows=table.tBodies[0].rows;if(table.tBodies[0].rows[0]){var list=[],cells=rows[0].cells,l=cells.length;for(var i=0;i<l;i++){var p=false;if($.metadata&&($($headers[i]).metadata()&&$($headers[i]).metadata().sorter)){p=getParserById($($headers[i]).metadata().sorter);}else if((table.config.headers[i]&&table.config.headers[i].sorter)){p=getParserById(table.config.headers[i].sorter);}if(!p){p=detectParserForColumn(table,cells[i]);}if(table.config.debug){parsersDebug+="column:"+i+" parser:"+p.id+"\n";}list.push(p);}}if(table.config.debug){log(parsersDebug);}return list;};function detectParserForColumn(table,node){var l=parsers.length;for(var i=1;i<l;i++){if(parsers[i].is($.trim(getElementText(table.config,node)),table,node)){return parsers[i];}}return parsers[0];}function getParserById(name){var l=parsers.length;for(var i=0;i<l;i++){if(parsers[i].id.toLowerCase()==name.toLowerCase()){return parsers[i];}}return false;}function buildCache(table){if(table.config.debug){var cacheTime=new Date();}var totalRows=(table.tBodies[0]&&table.tBodies[0].rows.length)||0,totalCells=(table.tBodies[0].rows[0]&&table.tBodies[0].rows[0].cells.length)||0,parsers=table.config.parsers,cache={row:[],normalized:[]};for(var i=0;i<totalRows;++i){var c=table.tBodies[0].rows[i],cols=[];cache.row.push($(c));for(var j=0;j<totalCells;++j){cols.push(parsers[j].format(getElementText(table.config,c.cells[j]),table,c.cells[j]));}cols.push(i);cache.normalized.push(cols);cols=null;};if(table.config.debug){benchmark("Building cache for "+totalRows+" rows:",cacheTime);}return cache;};function getElementText(config,node){if(!node)return"";var t="";if(config.textExtraction=="simple"){if(node.childNodes[0]&&node.childNodes[0].hasChildNodes()){t=node.childNodes[0].innerHTML;}else{t=node.innerHTML;}}else{if(typeof(config.textExtraction)=="function"){t=config.textExtraction(node);}else{t=$(node).text();}}return t;}function appendToTable(table,cache){if(table.config.debug){var appendTime=new Date()}var c=cache,r=c.row,n=c.normalized,totalRows=n.length,checkCell=(n[0].length-1),tableBody=$(table.tBodies[0]),rows=[];for(var i=0;i<totalRows;i++){rows.push(r[n[i][checkCell]]);if(!table.config.appender){var o=r[n[i][checkCell]];var l=o.length;for(var j=0;j<l;j++){tableBody[0].appendChild(o[j]);}}}if(table.config.appender){table.config.appender(table,rows);}rows=null;if(table.config.debug){benchmark("Rebuilt table:",appendTime);}applyWidget(table);setTimeout(function(){$(table).trigger("sortEnd");},0);};function buildHeaders(table){if(table.config.debug){var time=new Date();}var meta=($.metadata)?true:false,tableHeadersRows=[];for(var i=0;i<table.tHead.rows.length;i++){tableHeadersRows[i]=0;};$tableHeaders=$("thead th",table);$tableHeaders.each(function(index){this.count=0;this.column=index;this.order=formatSortingOrder(table.config.sortInitialOrder);if(checkHeaderMetadata(this)||checkHeaderOptions(table,index))this.sortDisabled=true;if(!this.sortDisabled){$(this).addClass(table.config.cssHeader);}table.config.headerList[index]=this;});if(table.config.debug){benchmark("Built headers:",time);log($tableHeaders);}return $tableHeaders;};function checkCellColSpan(table,rows,row){var arr=[],r=table.tHead.rows,c=r[row].cells;for(var i=0;i<c.length;i++){var cell=c[i];if(cell.colSpan>1){arr=arr.concat(checkCellColSpan(table,headerArr,row++));}else{if(table.tHead.length==1||(cell.rowSpan>1||!r[row+1])){arr.push(cell);}}}return arr;};function checkHeaderMetadata(cell){if(($.metadata)&&($(cell).metadata().sorter===false)){return true;};return false;}function checkHeaderOptions(table,i){if((table.config.headers[i])&&(table.config.headers[i].sorter===false)){return true;};return false;}function applyWidget(table){var c=table.config.widgets;var l=c.length;for(var i=0;i<l;i++){getWidgetById(c[i]).format(table);}}function getWidgetById(name){var l=widgets.length;for(var i=0;i<l;i++){if(widgets[i].id.toLowerCase()==name.toLowerCase()){return widgets[i];}}};function formatSortingOrder(v){if(typeof(v)!="Number"){i=(v.toLowerCase()=="desc")?1:0;}else{i=(v==(0||1))?v:0;}return i;}function isValueInArray(v,a){var l=a.length;for(var i=0;i<l;i++){if(a[i][0]==v){return true;}}return false;}function setHeadersCss(table,$headers,list,css){$headers.removeClass(css[0]).removeClass(css[1]);var h=[];$headers.each(function(offset){if(!this.sortDisabled){h[this.column]=$(this);}});var l=list.length;for(var i=0;i<l;i++){h[list[i][0]].addClass(css[list[i][1]]);}}function fixColumnWidth(table,$headers){var c=table.config;if(c.widthFixed){var colgroup=$('<colgroup>');$("tr:first td",table.tBodies[0]).each(function(){colgroup.append($('<col>').css('width',$(this).width()));});$(table).prepend(colgroup);};}function updateHeaderSortCount(table,sortList){var c=table.config,l=sortList.length;for(var i=0;i<l;i++){var s=sortList[i],o=c.headerList[s[0]];o.count=s[1];o.count++;}}function multisort(table,sortList,cache){if(table.config.debug){var sortTime=new Date();}var dynamicExp="var sortWrapper = function(a,b) {",l=sortList.length;for(var i=0;i<l;i++){var c=sortList[i][0];var order=sortList[i][1];var s=(getCachedSortType(table.config.parsers,c)=="text")?((order==0)?"sortText":"sortTextDesc"):((order==0)?"sortNumeric":"sortNumericDesc");var e="e"+i;dynamicExp+="var "+e+" = "+s+"(a["+c+"],b["+c+"]); ";dynamicExp+="if("+e+") { return "+e+"; } ";dynamicExp+="else { ";}var orgOrderCol=cache.normalized[0].length-1;dynamicExp+="return a["+orgOrderCol+"]-b["+orgOrderCol+"];";for(var i=0;i<l;i++){dynamicExp+="}; ";}dynamicExp+="return 0; ";dynamicExp+="}; ";eval(dynamicExp);cache.normalized.sort(sortWrapper);if(table.config.debug){benchmark("Sorting on "+sortList.toString()+" and dir "+order+" time:",sortTime);}return cache;};function sortText(a,b){return((a<b)?-1:((a>b)?1:0));};function sortTextDesc(a,b){return((b<a)?-1:((b>a)?1:0));};function sortNumeric(a,b){return a-b;};function sortNumericDesc(a,b){return b-a;};function getCachedSortType(parsers,i){return parsers[i].type;};this.construct=function(settings){return this.each(function(){if(!this.tHead||!this.tBodies)return;var $this,$document,$headers,cache,config,shiftDown=0,sortOrder;this.config={};config=$.extend(this.config,$.tablesorter.defaults,settings);$this=$(this);$headers=buildHeaders(this);this.config.parsers=buildParserCache(this,$headers);cache=buildCache(this);var sortCSS=[config.cssDesc,config.cssAsc];fixColumnWidth(this);$headers.click(function(e){$this.trigger("sortStart");var totalRows=($this[0].tBodies[0]&&$this[0].tBodies[0].rows.length)||0;if(!this.sortDisabled&&totalRows>0){var $cell=$(this);var i=this.column;this.order=this.count++%2;if(!e[config.sortMultiSortKey]){config.sortList=[];if(config.sortForce!=null){var a=config.sortForce;for(var j=0;j<a.length;j++){if(a[j][0]!=i){config.sortList.push(a[j]);}}}config.sortList.push([i,this.order]);}else{if(isValueInArray(i,config.sortList)){for(var j=0;j<config.sortList.length;j++){var s=config.sortList[j],o=config.headerList[s[0]];if(s[0]==i){o.count=s[1];o.count++;s[1]=o.count%2;}}}else{config.sortList.push([i,this.order]);}};setTimeout(function(){setHeadersCss($this[0],$headers,config.sortList,sortCSS);appendToTable($this[0],multisort($this[0],config.sortList,cache));},1);return false;}}).mousedown(function(){if(config.cancelSelection){this.onselectstart=function(){return false};return false;}});$this.bind("update",function(){this.config.parsers=buildParserCache(this,$headers);cache=buildCache(this);}).bind("sorton",function(e,list){$(this).trigger("sortStart");config.sortList=list;var sortList=config.sortList;updateHeaderSortCount(this,sortList);setHeadersCss(this,$headers,sortList,sortCSS);appendToTable(this,multisort(this,sortList,cache));}).bind("appendCache",function(){appendToTable(this,cache);}).bind("applyWidgetId",function(e,id){getWidgetById(id).format(this);}).bind("applyWidgets",function(){applyWidget(this);});if($.metadata&&($(this).metadata()&&$(this).metadata().sortlist)){config.sortList=$(this).metadata().sortlist;}if(config.sortList.length>0){$this.trigger("sorton",[config.sortList]);}applyWidget(this);});};this.addParser=function(parser){var l=parsers.length,a=true;for(var i=0;i<l;i++){if(parsers[i].id.toLowerCase()==parser.id.toLowerCase()){a=false;}}if(a){parsers.push(parser);};};this.addWidget=function(widget){widgets.push(widget);};this.formatFloat=function(s){var i=parseFloat(s);return(isNaN(i))?0:i;};this.formatInt=function(s){var i=parseInt(s);return(isNaN(i))?0:i;};this.isDigit=function(s,config){var DECIMAL='\\'+config.decimal;var exp='/(^[+]?0('+DECIMAL+'0+)?$)|(^([-+]?[1-9][0-9]*)$)|(^([-+]?((0?|[1-9][0-9]*)'+DECIMAL+'(0*[1-9][0-9]*)))$)|(^[-+]?[1-9]+[0-9]*'+DECIMAL+'0+$)/';return RegExp(exp).test($.trim(s));};this.clearTableBody=function(table){if($.browser.msie){function empty(){while(this.firstChild)this.removeChild(this.firstChild);}empty.apply(table.tBodies[0]);}else{table.tBodies[0].innerHTML="";}};}});$.fn.extend({tablesorter:$.tablesorter.construct});var ts=$.tablesorter;ts.addParser({id:"text",is:function(s){return true;},format:function(s){return $.trim(s.toLowerCase());},type:"text"});ts.addParser({id:"digit",is:function(s,table){var c=table.config;return $.tablesorter.isDigit(s,c);},format:function(s){return $.tablesorter.formatFloat(s);},type:"numeric"});ts.addParser({id:"currency",is:function(s){return/^[Â£$â‚¬?.]/.test(s);},format:function(s){return $.tablesorter.formatFloat(s.replace(new RegExp(/[^0-9.]/g),""));},type:"numeric"});ts.addParser({id:"ipAddress",is:function(s){return/^\d{2,3}[\.]\d{2,3}[\.]\d{2,3}[\.]\d{2,3}$/.test(s);},format:function(s){var a=s.split("."),r="",l=a.length;for(var i=0;i<l;i++){var item=a[i];if(item.length==2){r+="0"+item;}else{r+=item;}}return $.tablesorter.formatFloat(r);},type:"numeric"});ts.addParser({id:"url",is:function(s){return/^(https?|ftp|file):\/\/$/.test(s);},format:function(s){return jQuery.trim(s.replace(new RegExp(/(https?|ftp|file):\/\//),''));},type:"text"});ts.addParser({id:"isoDate",is:function(s){return/^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/.test(s);},format:function(s){return $.tablesorter.formatFloat((s!="")?new Date(s.replace(new RegExp(/-/g),"/")).getTime():"0");},type:"numeric"});ts.addParser({id:"percent",is:function(s){return/\%$/.test($.trim(s));},format:function(s){return $.tablesorter.formatFloat(s.replace(new RegExp(/%/g),""));},type:"numeric"});ts.addParser({id:"usLongDate",is:function(s){return s.match(new RegExp(/^[A-Za-z]{3,10}\.? [0-9]{1,2}, ([0-9]{4}|'?[0-9]{2}) (([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(AM|PM)))$/));},format:function(s){return $.tablesorter.formatFloat(new Date(s).getTime());},type:"numeric"});ts.addParser({id:"shortDate",is:function(s){return/\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}/.test(s);},format:function(s,table){var c=table.config;s=s.replace(/\-/g,"/");if(c.dateFormat=="us"){s=s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/,"$3/$1/$2");}else if(c.dateFormat=="uk"){s=s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/,"$3/$2/$1");}else if(c.dateFormat=="dd/mm/yy"||c.dateFormat=="dd-mm-yy"){s=s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2})/,"$1/$2/$3");}return $.tablesorter.formatFloat(new Date(s).getTime());},type:"numeric"});ts.addParser({id:"time",is:function(s){return/^(([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(am|pm)))$/.test(s);},format:function(s){return $.tablesorter.formatFloat(new Date("2000/01/01 "+s).getTime());},type:"numeric"});ts.addParser({id:"metadata",is:function(s){return false;},format:function(s,table,cell){var c=table.config,p=(!c.parserMetadataName)?'sortValue':c.parserMetadataName;return $(cell).metadata()[p];},type:"numeric"});ts.addWidget({id:"zebra",format:function(table){if(table.config.debug){var time=new Date();}$("tr:visible",table.tBodies[0]).filter(':even').removeClass(table.config.widgetZebra.css[1]).addClass(table.config.widgetZebra.css[0]).end().filter(':odd').removeClass(table.config.widgetZebra.css[0]).addClass(table.config.widgetZebra.css[1]);if(table.config.debug){$.tablesorter.benchmark("Applying Zebra widget",time);}}});})(jQuery);
;
/*Plugin scrollTo*/
(function(d){var k=d.scrollTo=function(a,i,e){d(window).scrollTo(a,i,e)};k.defaults={axis:'xy',duration:parseFloat(d.fn.jquery)>=1.3?0:1};k.window=function(a){return d(window)._scrollable()};d.fn._scrollable=function(){return this.map(function(){var a=this,i=!a.nodeName||d.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!i)return a;var e=(a.contentWindow||a).document||a.ownerDocument||a;return d.browser.safari||e.compatMode=='BackCompat'?e.body:e.documentElement})};d.fn.scrollTo=function(n,j,b){if(typeof j=='object'){b=j;j=0}if(typeof b=='function')b={onAfter:b};if(n=='max')n=9e9;b=d.extend({},k.defaults,b);j=j||b.speed||b.duration;b.queue=b.queue&&b.axis.length>1;if(b.queue)j/=2;b.offset=p(b.offset);b.over=p(b.over);return this._scrollable().each(function(){var q=this,r=d(q),f=n,s,g={},u=r.is('html,body');switch(typeof f){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(f)){f=p(f);break}f=d(f,this);case'object':if(f.is||f.style)s=(f=d(f)).offset()}d.each(b.axis.split(''),function(a,i){var e=i=='x'?'Left':'Top',h=e.toLowerCase(),c='scroll'+e,l=q[c],m=k.max(q,i);if(s){g[c]=s[h]+(u?0:l-r.offset()[h]);if(b.margin){g[c]-=parseInt(f.css('margin'+e))||0;g[c]-=parseInt(f.css('border'+e+'Width'))||0}g[c]+=b.offset[h]||0;if(b.over[h])g[c]+=f[i=='x'?'width':'height']()*b.over[h]}else{var o=f[h];g[c]=o.slice&&o.slice(-1)=='%'?parseFloat(o)/100*m:o}if(/^\d+$/.test(g[c]))g[c]=g[c]<=0?0:Math.min(g[c],m);if(!a&&b.queue){if(l!=g[c])t(b.onAfterFirst);delete g[c]}});t(b.onAfter);function t(a){r.animate(g,j,b.easing,a&&function(){a.call(this,n,b)})}}).end()};k.max=function(a,i){var e=i=='x'?'Width':'Height',h='scroll'+e;if(!d(a).is('html,body'))return a[h]-d(a)[e.toLowerCase()]();var c='client'+e,l=a.ownerDocument.documentElement,m=a.ownerDocument.body;return Math.max(l[h],m[h])-Math.min(l[c],m[c])};function p(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);
;
/*Plugin abas*/
(function ($) {
    $.extend({
        abas: new function () {
            function mostrar(o)
            {
                $(o).addClass('ativa');
                $($(o).attr('href')).show();
            }
            function esconder(o)
            {
                $(o).removeClass('ativa');
                $($(o).attr('href')).hide();
            }
            this.iniciar = function()
            {
                $(this.selector).each(function(i) {
                    $(this).children('li').children('a').each(function(j) {
                        esconder(this);
                    });
                    mostrar($(this).children('li').children('a').first());
                });

                $(this.selector+' li a').click(function(){
                    $(this).parent().parent().children('li').each(function(i) {
                        esconder($(this).children('a'));
                    });
                    mostrar(this);
                    return false;
                });
            };
        }
    });
    $.fn.extend({
        abas: $.abas.iniciar
    });
})(jQuery);

/*Plugin mascara*/
(function ($) {

    var isIphone = (window.orientation != null);

    // the timeout is set because we can't get the value from the input without it
    var pasteHandler = function (e) {
        e = $.event.fix(e || window.event);
        e.type = 'paste';
        var el = e.target;

        setTimeout(function () {
            $.event.dispatch.call(el, e);
        }, 1);
    };

    $.event.special.paste = {
        setup: function () {
            if (this.addEventListener)
                this.addEventListener('paste', pasteHandler, false);
            else if (this.attachEvent)
                this.attachEvent('onpaste', pasteHandler);
        },

        teardown: function () {
            if (this.removeEventListener)
                this.removeEventListener('paste', pasteHandler, false);
            else if (this.detachEvent)
                this.detachEvent('onpaste', pasteHandler);
        }
    };

    $.extend({
        mask: {

            // the mask rules. You may add yours!
            // number rules will be overwritten
            rules: {
                'z': /[a-z]/,
                'Z': /[A-Z]/,
                'a': /[a-zA-Z]/,
                '*': /[0-9a-zA-Z]/,
                '@': /[0-9a-zA-ZçÇáàãâéèêíìóòôõúùü]/
            },

            // these keys will be ignored by the mask.
            // all these numbers where obtained on the keydown event
            keyRepresentation: {
                8: 'backspace',
                9: 'tab',
                13: 'enter',
                16: 'shift',
                17: 'control',
                18: 'alt',
                27: 'esc',
                33: 'page up',
                34: 'page down',
                35: 'end',
                36: 'home',
                37: 'left',
                38: 'up',
                39: 'right',
                40: 'down',
                45: 'insert',
                46: 'delete',
                116: 'f5',
                123: 'f12',
                224: 'command'
            },

            iphoneKeyRepresentation: {
                10: 'go',
                127: 'delete'
            },

            signals: {
                '+': '',
                '-': '-'
            },

            // default settings for the plugin
            options: {
                attr: 'alt', // an attr to look for the mask name or the mask itself
                mask: null, // the mask to be used on the input
                type: 'fixed', // the mask of this mask
                maxLength: -1, // the maxLength of the mask
                defaultValue: '', // the default value for this input
                signal: false, // this should not be set, to use signal at masks put the signal you want ('-' or '+') at the default value of this mask.
                // See the defined masks for a better understanding.

                textAlign: true, // use false to not use text-align on any mask (at least not by the plugin, you may apply it using css)
                selectCharsOnFocus: true, // select all chars from input on its focus
                autoTab: true, // auto focus the next form element when you type the mask completely
                setSize: false, // sets the input size based on the length of the mask (work with fixed and reverse masks only)
                fixedChars: '[(),.:/ -]', // fixed chars to be used on the masks. You may change it for your needs!

                onInvalid: function () { },
                onValid: function () { },
                onOverflow: function () { }
            },

            // masks. You may add yours!
            // Ex: $.fn.setMask.masks.msk = {mask: '999'}
            // and then if the 'attr' options value is 'alt', your input should look like:
            // <input type="text" name="some_name" id="some_name" alt="msk" />
            masks: {
                'phone': { mask: '(99) 9999-9999' },
                'phone-us': { mask: '(999) 999-9999' },
                'cpf': { mask: '999.999.999-99' }, // cadastro nacional de pessoa fisica (kind of a brazillian ssn)
                'cnpj': { mask: '99.999.999/9999-99' },
                'date': { mask: '39/19/9999' },     // uk date
                'date-us': { mask: '19/39/9999' },
                'cep': { mask: '99999-999' },
                'time': { mask: '29:59' },
                'cc': { mask: '9999 9999 9999 9999' }, // credit card
                'integer': { mask: '999.999.999.999', type: 'reverse' },
                'decimal': { mask: '99,999.999.999.999', type: 'reverse' },
                'decimal-us': { mask: '99.999,999,999,999', type: 'reverse', defaultValue: '000' },
                'signed-decimal': { mask: '99,999.999.999.999', type: 'reverse', defaultValue: '+000' },
                'signed-decimal-us': { mask: '99,999.999.999.999', type: 'reverse', defaultValue: '+000' },
                "score": { mask: "99,999", type: "reverse" },
                "4ultimos": { mask: "9999" },
                "bin": { mask: "999999" },
                "voo-data": { mask: "999999" },
                "qtd": { mask: "9999" },
                "date-expiracao": { mask: "19/9999" },
                "tel_nono": { mask: "9999999999" },
                "ddi_ddd": { mask: "999" }
            },

            init: function () {
                // if has not inited...
                if (!this.hasInit) {

                    var self = this, i,
                        keyRep = (isIphone) ? this.iphoneKeyRepresentation : this.keyRepresentation;

                    this.ignore = false;

                    // constructs number rules
                    for (i = 0; i <= 9; i++) this.rules[i] = new RegExp('[0-' + i + ']');

                    this.keyRep = keyRep;
                    // ignore keys array creation for iphone or the normal ones
                    this.ignoreKeys = [];
                    $.each(keyRep, function (key) {
                        self.ignoreKeys.push(parseInt(key, 10));
                    });

                    this.hasInit = true;
                }
            },

            set: function (el, options) {

                var maskObj = this,
                    $el = $(el),
                    mlStr = 'maxLength';

                options = options || {};
                this.init();

                return $el.each(function () {

                    if (options.attr) maskObj.options.attr = options.attr;

                    var $this = $(this),
                        o = $.extend({}, maskObj.options),
                        attrValue = $this.attr(o.attr),
                        tmpMask = '';

                    // then we look for the 'attr' option
                    tmpMask = (typeof options == 'string') ? options : (attrValue !== '') ? attrValue : null;
                    if (tmpMask) o.mask = tmpMask;

                    // then we see if it's a defined mask
                    if (maskObj.masks[tmpMask]) o = $.extend(o, maskObj.masks[tmpMask]);

                    // then it looks if the options is an object, if it is we will overwrite the actual options
                    if (typeof options == 'object' && options.constructor != Array) o = $.extend(o, options);

                    //then we look for some metadata on the input
                    if ($.metadata) o = $.extend(o, $this.metadata());

                    if (o.mask != null) {

                        // prevents javascript automatic type convertion
                        o.mask += '';

                        if ($this.data('mask')) maskObj.unset($this);

                        var defaultValue = o.defaultValue,
                            reverse = (o.type === 'reverse'),
                            fixedCharsRegG = new RegExp(o.fixedChars, 'g');

                        if (o.maxLength === -1) o.maxLength = $this.attr(mlStr);

                        o = $.extend({}, o, {
                            fixedCharsReg: new RegExp(o.fixedChars),
                            fixedCharsRegG: fixedCharsRegG,
                            maskArray: o.mask.split(''),
                            maskNonFixedCharsArray: o.mask.replace(fixedCharsRegG, '').split('')
                        });

                        // setSize option (this is kept when the mask is removed)
                        if ((o.type == 'fixed' || reverse) && o.setSize && !$this.attr('size')) $this.attr('size', o.mask.length);

                        // sets text-align right for reverse masks
                        if (reverse && o.textAlign) $this.css('text-align', 'right');

                        if (this.value !== '' || defaultValue !== '') {
                            // apply mask to the current value of the input or to the default value
                            var val = maskObj.string((this.value !== '') ? this.value : defaultValue, o);
                            //setting defaultValue fixes the reset button from the form
                            this.defaultValue = val;
                            $this.val(val);
                        }

                        // compatibility patch for infinite mask, that is now repeat
                        if (o.type == 'infinite') o.type = 'repeat';

                        $this.data('mask', o);

                        // removes the maxLength attribute (it will be set again if you use the unset method)
                        $this.removeAttr(mlStr);

                        // setting the input events
                        $this.bind('keydown.mask', { func: maskObj._onKeyDown, thisObj: maskObj }, maskObj._onMask)
                            .bind('keypress.mask', { func: maskObj._onKeyPress, thisObj: maskObj }, maskObj._onMask)
                            .bind('keyup.mask', { func: maskObj._onKeyUp, thisObj: maskObj }, maskObj._onMask)
                            .bind('paste.mask', { func: maskObj._onPaste, thisObj: maskObj }, maskObj._onMask)
                            .bind('focus.mask', maskObj._onFocus)
                            .bind('blur.mask', maskObj._onBlur)
                            .bind('change.mask', maskObj._onChange);
                    }
                });
            },

            //unsets the mask from el
            unset: function (el) {
                var $el = $(el);
                
                return $el.each(function () {
                    var $this = $(this);
                    if ($this.data('mask')) {
                        
                        var maxLength = $this.data('mask').maxLength;
                        if (maxLength != -1) $this.attr('maxLength', maxLength);
                        $this.unbind('.mask')
                            .removeData('mask');
                    }
                    
                });
            },

            //masks a string
            string: function (str, options) {
                this.init();
                var o = {};
                if (typeof str != 'string') str = String(str);
                switch (typeof options) {
                    case 'string':
                        // then we see if it's a defined mask
                        if (this.masks[options]) o = $.extend(o, this.masks[options]);
                        else o.mask = options;
                        break;
                    case 'object':
                        o = options;
                }
                if (!o.fixedChars) o.fixedChars = this.options.fixedChars;

                var fixedCharsReg = new RegExp(o.fixedChars),
                    fixedCharsRegG = new RegExp(o.fixedChars, 'g');

                // insert signal if any
                if ((o.type === 'reverse') && o.defaultValue) {
                    if (typeof this.signals[o.defaultValue.charAt(0)] != 'undefined') {
                        var maybeASignal = str.charAt(0);
                        o.signal = (typeof this.signals[maybeASignal] != 'undefined') ? this.signals[maybeASignal] : this.signals[o.defaultValue.charAt(0)];
                        o.defaultValue = o.defaultValue.substring(1);
                    }
                }

                return this.__maskArray(str.split(''),
                            o.mask.replace(fixedCharsRegG, '').split(''),
                            o.mask.split(''),
                            o.type,
                            o.maxLength,
                            o.defaultValue,
                            fixedCharsReg,
                            o.signal);
            },

            // all the 3 events below are here just to fix the change event on reversed masks.
            // It isn't fired in cases that the keypress event returns false (needed).
            _onFocus: function (e) {
                var $this = $(this), dataObj = $this.data('mask');
                dataObj.inputFocusValue = $this.val();
                dataObj.changed = false;
                if (dataObj.selectCharsOnFocus) $this.select();
            },

            _onBlur: function (e) {
                var $this = $(this), dataObj = $this.data('mask');
                if (dataObj.inputFocusValue != $this.val() && !dataObj.changed)
                    $this.trigger('change');
            },

            _onChange: function (e) {
                $(this).data('mask').changed = true;
            },

            _onMask: function (e) {
                var thisObj = e.data.thisObj,
                    o = {};

                o._this = e.target;
                o.$this = $(o._this);
                o.data = o.$this.data('mask');

                if (o.$this.attr('readonly') || !o.data) {
                    return true;
                }

                o[o.data.type] = true;
                o.value = o.$this.val();
                o.nKey = thisObj.__getKeyNumber(e);
                o.range = thisObj.__getRange(o._this);
                o.valueArray = o.value.split('');
                return e.data.func.call(thisObj, e, o);
            },

            _onKeyDown: function (e, o) {
                // lets say keypress at desktop == keydown at iphone (theres no keypress at iphone)
                this.ignore = $.inArray(o.nKey, this.ignoreKeys) > -1 || e.ctrlKey || e.metaKey || e.altKey;
                if (this.ignore) {
                    var rep = this.keyRep[o.nKey];
                    o.data.onValid.call(o._this, rep || '', o.nKey);
                }
                return isIphone ? this._onKeyPress(e, o) : true;
            },

            _onKeyUp: function (e, o) {
                //9=TAB_KEY 16=SHIFT_KEY
                //this is a little bug, when you go to an input with tab key
                //it would remove the range selected by default, and that's not a desired behavior
                if (o.nKey === 9 || o.nKey === 16) return true;

                if (o.repeat) {
                    this.__autoTab(o);
                    return true;
                }

                return this._onPaste(e, o);
            },

            _onPaste: function (e, o) {
                // changes the signal at the data obj from the input
                if (o.reverse) this.__changeSignal(e.type, o);

                var $thisVal = this.__maskArray(
                    o.valueArray,
                    o.data.maskNonFixedCharsArray,
                    o.data.maskArray,
                    o.data.type,
                    o.data.maxLength,
                    o.data.defaultValue,
                    o.data.fixedCharsReg,
                    o.data.signal
                );

                o.$this.val($thisVal);
                // this makes the caret stay at first position when
                // the user removes all values in an input and the plugin adds the default value to it (if it haves one).
                if (!o.reverse && o.data.defaultValue.length && (o.range.start === o.range.end))
                    this.__setRange(o._this, o.range.start, o.range.end);

                //fix so ie's and safari's caret won't go to the end of the input value.
                if (($.browser.msie || $.browser.safari) && !o.reverse)
                    this.__setRange(o._this, o.range.start, o.range.end);

                if (this.ignore) return true;

                this.__autoTab(o);
                return true;
            },

            _onKeyPress: function (e, o) {

                if (this.ignore) return true;

                // changes the signal at the data obj from the input
                if (o.reverse) this.__changeSignal(e.type, o);

                var c = String.fromCharCode(o.nKey),
                    rangeStart = o.range.start,
                    rawValue = o.value,
                    maskArray = o.data.maskArray;

                if (o.reverse) {
                    // the input value from the range start to the value start
                    var valueStart = rawValue.substr(0, rangeStart),
                    // the input value from the range end to the value end
                        valueEnd = rawValue.substr(o.range.end, rawValue.length);

                    rawValue = valueStart + c + valueEnd;
                    //necessary, if not decremented you will be able to input just the mask.length-1 if signal!=''
                    //ex: mask:99,999.999.999 you will be able to input 99,999.999.99
                    if (o.data.signal && (rangeStart - o.data.signal.length > 0)) {
                        rangeStart -= o.data.signal.length;
                    }
                }

                var valueArray = rawValue.replace(o.data.fixedCharsRegG, '').split(''),
                // searches for fixed chars begining from the range start position, till it finds a non fixed
                    extraPos = this.__extraPositionsTill(rangeStart, maskArray, o.data.fixedCharsReg);

                o.rsEp = rangeStart + extraPos;

                if (o.repeat) {
                    o.rsEp = 0;
                }

                // if the rule for this character doesnt exist (value.length is bigger than mask.length)
                // added a verification for maxLength in the case of the repeat type mask
                if (!this.rules[maskArray[o.rsEp]] || (o.data.maxLength != -1 && valueArray.length >= o.data.maxLength && o.repeat)) {
                    // auto focus on the next input of the current form
                    o.data.onOverflow.call(o._this, c, o.nKey);
                    return false;
                }

                // if the new character is not obeying the law...
                else if (!this.rules[maskArray[o.rsEp]].test(c)) {
                    o.data.onInvalid.call(o._this, c, o.nKey);
                    return false;
                }

                else {
                    o.data.onValid.call(o._this, c, o.nKey);
                }

                var $thisVal = this.__maskArray(
                    valueArray,
                    o.data.maskNonFixedCharsArray,
                    maskArray,
                    o.data.type,
                    o.data.maxLength,
                    o.data.defaultValue,
                    o.data.fixedCharsReg,
                    o.data.signal,
                    extraPos
                );

                if (!o.repeat) {
                    o.$this.val($thisVal);
                }

                return (o.reverse) ? this._keyPressReverse(e, o) : (o.fixed) ? this._keyPressFixed(e, o) : true;
            },

            _keyPressFixed: function (e, o) {

                if (o.range.start == o.range.end) {
                    // the 0 thing is because theres an unwanted behavior when you put a default
                    // value on a fixed mask and you select the value from the input the range would go to the
                    // end of the string when you enter a char. with this it will overwrite the first char wich is a better behavior.
                    // opera fix, cant have range value bigger than value length, i think it loops thought the input value...
                    if ((o.rsEp === 0 && o.value.length === 0) || o.rsEp < o.value.length)
                        this.__setRange(o._this, o.rsEp, o.rsEp + 1);
                }
                else
                    this.__setRange(o._this, o.range.start, o.range.end);

                return true;
            },

            _keyPressReverse: function (e, o) {
                // fix for ie
                // this bug was pointed by Pedro Martins
                // it fixes a strange behavior that ie was having after a char was inputted in a text input that
                // had its content selected by any range
                if ($.browser.msie && ((o.range.start === 0 && o.range.end === 0) || o.range.start != o.range.end))
                    this.__setRange(o._this, o.value.length);
                return false;
            },

            __autoTab: function (o) {
                if (o.data.autoTab && (
                        (
                            o.$this.val().length >= o.data.maskArray.length &&
                            !o.repeat
                        ) || (
                            o.data.maxLength != -1 &&
                            o.valueArray.length >= o.data.maxLength &&
                            o.repeat
                        )
                    )
                ) {
                    var nextEl = this.__getNextInput(o._this, o.data.autoTab);
                    if (nextEl) {
                        o.$this.trigger('blur');
                        nextEl.focus().select();
                    }
                }
            },

            // changes the signal at the data obj from the input
            __changeSignal: function (eventType, o) {
                if (o.data.signal !== false) {
                    var inputChar = (eventType === 'paste') ? o.value.charAt(0) : String.fromCharCode(o.nKey);
                    if (this.signals && (typeof this.signals[inputChar] !== 'undefined')) {
                        o.data.signal = this.signals[inputChar];
                    }
                }
            },

            __getKeyNumber: function (e) {
                return (e.charCode || e.keyCode || e.which);
            },

            // this function is totaly specific to be used with this plugin, youll never need it
            // it gets the array representing an unmasked string and masks it depending on the type of the mask
            __maskArray: function (valueArray, maskNonFixedCharsArray, maskArray, type, maxlength, defaultValue, fixedCharsReg, signal, extraPos) {
                if (type === 'reverse') valueArray.reverse();
                valueArray = this.__removeInvalidChars(valueArray, maskNonFixedCharsArray, type === 'repeat' || type === 'infinite');
                if (defaultValue) valueArray = this.__applyDefaultValue.call(valueArray, defaultValue);
                valueArray = this.__applyMask(valueArray, maskArray, extraPos, fixedCharsReg);
                switch (type) {
                    case 'reverse':
                        valueArray.reverse();
                        return (signal || '') + valueArray.join('').substring(valueArray.length - maskArray.length);
                    case 'infinite': case 'repeat':
                        var joinedValue = valueArray.join('');
                        return (maxlength !== -1 && valueArray.length >= maxlength) ? joinedValue.substring(0, maxlength) : joinedValue;
                    default:
                        return valueArray.join('').substring(0, maskArray.length);
                }
                return '';
            },

            // applyes the default value to the result string
            __applyDefaultValue: function (defaultValue) {
                var defLen = defaultValue.length,
                    thisLen = this.length,
                    i;
                //removes the leading chars
                for (i = thisLen - 1; i >= 0; i--) {
                    if (this[i] == defaultValue.charAt(0)) {
                        this.pop();
                    }
                    else break;
                }
                // apply the default value
                for (i = 0; i < defLen; i++) if (!this[i]) {
                    this[i] = defaultValue.charAt(i);
                }

                return this;
            },

            // Removes values that doesnt match the mask from the valueArray
            // Returns the array without the invalid chars.
            __removeInvalidChars: function (valueArray, maskNonFixedCharsArray, repeatType) {
                // removes invalid chars
                for (var i = 0, y = 0; i < valueArray.length; i++) {
                    if (maskNonFixedCharsArray[y] &&
                        this.rules[maskNonFixedCharsArray[y]] &&
                        !this.rules[maskNonFixedCharsArray[y]].test(valueArray[i])) {
                        valueArray.splice(i, 1);
                        if (!repeatType) y--;
                        i--;
                    }
                    if (!repeatType) y++;
                }
                return valueArray;
            },

            // Apply the current input mask to the valueArray and returns it.
            __applyMask: function (valueArray, maskArray, plus, fixedCharsReg) {
                if (typeof plus == 'undefined') plus = 0;
                // apply the current mask to the array of chars
                for (var i = 0; i < valueArray.length + plus; i++) {
                    if (maskArray[i] && fixedCharsReg.test(maskArray[i]))
                        valueArray.splice(i, 0, maskArray[i]);
                }
                return valueArray;
            },

            // searches for fixed chars begining from the range start position, till it finds a non fixed
            __extraPositionsTill: function (rangeStart, maskArray, fixedCharsReg) {
                var extraPos = 0;
                while (fixedCharsReg.test(maskArray[rangeStart++])) {
                    extraPos++;
                }
                return extraPos;
            },

            __getNextInput: function (input, selector) {
                var form = input.form;

                if (form == null) {
                    return null;
                }

                var formEls = form.elements,
                    initialInputIndex = $.inArray(input, formEls) + 1,
                    len = formEls.length,
                    $input = null,
                    i;

                // look for next input on the form of the pased input
                for (i = initialInputIndex; i < len; i++) {
                    $input = $(formEls[i]);
                    if (this.__isNextInput($input, selector)) {
                        return $input;
                    }
                }

                var forms = document.forms,
                    initialFormIndex = $.inArray(input.form, forms) + 1,
                    y, tmpFormEls, _len = forms.length;
                // look for the next forms for the next input
                for (y = initialFormIndex; y < _len; y++) {
                    tmpFormEls = forms[y].elements;
                    len = tmpFormEls.length;
                    for (i = 0; i < len; i++) {
                        $input = $(tmpFormEls[i]);
                        if (this.__isNextInput($input, selector)) {
                            return $input;
                        }
                    }
                }
                return null;
            },

            __isNextInput: function ($formEl, selector) {
                var formEl = $formEl.get(0);
                return formEl &&
                    (formEl.offsetWidth > 0 || formEl.offsetHeight > 0) &&
                    formEl.nodeName != 'FIELDSET' &&
                    (selector === true || (typeof selector == 'string' && $formEl.is(selector)));
            },

            // http://www.bazon.net/mishoo/articles.epl?art_id=1292
            __setRange: function (input, start, end) {
                if (typeof end == 'undefined') {
                    end = start;
                }
                if (input.setSelectionRange) {
                    input.setSelectionRange(start, end);
                } else {
                    // assumed IE
                    var range = input.createTextRange();
                    range.collapse();
                    range.moveStart('character', start);
                    range.moveEnd('character', end - start);
                    range.select();
                }
            },

            // adaptation from http://digitarald.de/project/autocompleter/
            __getRange: function (input) {
                if (!$.browser.msie) return { start: input.selectionStart, end: input.selectionEnd };
                var pos = { start: 0, end: 0 },
                    range = document.selection.createRange();
                pos.start = 0 - range.duplicate().moveStart('character', -100000);
                pos.end = pos.start + range.text.length;
                return pos;
            },

            //deprecated
            unmaskedVal: function (el) {
                return $(el).val().replace($.mask.fixedCharsRegG, '');
            }

        }
    });

    $.fn.extend({
        setMask: function (options) {
            return $.mask.set(this, options);
        },
        unsetMask: function () {
            return $.mask.unset(this);
        },
        //deprecated
        unmaskedVal: function () {
            return $.mask.unmaskedVal(this[0]);
        }
    });
})(jQuery);

// Função para montar mensagem
function ExibirMensagem(painel, mensagemTipo, mensagem) {
    if (painel) {
        mensagemSpan = $('<div></div>');
        mensagemSpan.html(mensagem);
        switch (mensagemTipo) {
            case "Alerta":
                mensagemSpan.addClass("mensagem-alerta");
                break;
            case "Erro":
                mensagemSpan.addClass("mensagem-erro");
                break;
            case "Ok":
                mensagemSpan.addClass("mensagem-ok");
                break;
            default:
                mensagemSpan.addClass("mensagem-info");
                break;
        }
        painel.empty();
        painel.append(mensagemSpan);
    }
}