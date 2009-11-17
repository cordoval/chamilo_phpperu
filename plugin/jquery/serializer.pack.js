/* 
 * More info at: http://phpjs.org
 * 
 * This is version: 2.71
 * php.js is copyright 2009 Kevin van Zonneveld.
 * 
 * Portions copyright Brett Zamir (http://brett-zamir.me), Kevin van Zonneveld
 * (http://kevin.vanzonneveld.net), Onno Marsman, Michael White
 * (http://getsprink.com), Waldo Malqui Silva, Paulo Ricardo F. Santos, Jack,
 * Philip Peterson, Jonas Raoni Soares Silva (http://www.jsfromhell.com),
 * Legaev Andrey, Ates Goral (http://magnetiq.com), Martijn Wieringa, Nate,
 * Enrique Gonzalez, Philippe Baumann, Webtoolkit.info
 * (http://www.webtoolkit.info/), Theriault, Jani Hartikainen, Ash Searle
 * (http://hexmen.com/blog/), Carlos R. L. Rodrigues
 * (http://www.jsfromhell.com), Ole Vrijenhoek, travc, Michael Grier, GeekFG
 * (http://geekfg.blogspot.com), Johnny Mast (http://www.phpvrouwen.nl), Alex,
 * d3x, stag019, Andrea Giammarchi (http://webreflection.blogspot.com),
 * marrtins, Erkekjetter, Steve Hilder, Oleg Eremeev, David, Marc Palau,
 * Steven Levithan (http://blog.stevenlevithan.com), gorthaur, Arpad Ray
 * (mailto:arpad@php.net), Public Domain (http://www.json.org/json2.js),
 * gettimeofday, mdsjack (http://www.mdsjack.bo.it), Thunder.m, Alfonso
 * Jimenez (http://www.alfonsojimenez.com), KELAN, Tyler Akins
 * (http://rumkin.com), Mirek Slugen, Kankrelune
 * (http://www.webfaktory.info/), Karol Kowalski, AJ, Lars Fischer, Caio
 * Ariede (http://caioariede.com), Aman Gupta, Sakimori, Pellentesque
 * Malesuada, Breaking Par Consulting Inc
 * (http://www.breakingpar.com/bkp/home.nsf/0/87256B280015193F87256CFB006C45F7),
 * Josh Fraser
 * (http://onlineaspect.com/2007/06/08/auto-detect-a-time-zone-with-javascript/),
 * Paul, madipta, Douglas Crockford (http://javascript.crockford.com), Ole
 * Vrijenhoek (http://www.nervous.nl/), Hyam Singer
 * (http://www.impact-computing.com/), Raphael (Ao RUDLER), kenneth, T. Wild,
 * nobbler, class_exists, noname, Marco, David James, Steve Clay, marc andreu,
 * ger, john (http://www.jd-tech.net), mktime, Brad Touesnard, J A R, djmix,
 * Lincoln Ramsay, Linuxworld, Thiago Mata (http://thiagomata.blog.com),
 * Pyerre, Jon Hohle, Bayron Guevara, duncan, Sanjoy Roy, sankai, 0m3r, Felix
 * Geisendoerfer (http://www.debuggable.com/felix), Gilbert, Subhasis Deb,
 * Soren Hansen, T0bsn, Eugene Bulkin (http://doubleaw.com/), Der Simon
 * (http://innerdom.sourceforge.net/), JB, LH, Marc Jansen, Francesco, echo is
 * bad, XoraX (http://www.xorax.info), MeEtc (http://yass.meetcweb.com),
 * Peter-Paul Koch (http://www.quirksmode.org/js/beat.html), Nathan, Tim Wiel,
 * Ozh, David Randall, Bryan Elliott, Jalal Berrami, Arno, Rick Waldron,
 * Mick@el, rezna, Kirk Strobeck, Martin Pool, Daniel Esteban, Saulo Vallory,
 * Kristof Coomans (SCK-CEN Belgian Nucleair Research Centre), Pierre-Luc
 * Paour, Eric Nagel, Bobby Drake, penutbutterjelly, Christian Doebler,
 * setcookie, Gabriel Paderni, Simon Willison (http://simonwillison.net), Pul,
 * Luke Godfrey, Blues (http://tech.bluesmoon.info/), Anton Ongson, Jason Wong
 * (http://carrot.org/), Valentina De Rosa, sowberry, hitwork, Norman "zEh"
 * Fuchs, Yves Sucaet, johnrembo, Nick Callen, ejsanders, Aidan Lister
 * (http://aidanlister.com/), Philippe Jausions
 * (http://pear.php.net/user/jausions), dptr1988, Pedro Tainha
 * (http://www.pedrotainha.com), Alan C, uestla, Wagner B. Soares, T.Wild,
 * strcasecmp, strcmp, DxGx, Alexander Ermolaev
 * (http://snippets.dzone.com/user/AlexanderErmolaev), ChaosNo1, metjay, YUI
 * Library: http://developer.yahoo.com/yui/docs/YAHOO.util.DateLocale.html,
 * Blues at http://hacks.bluesmoon.info/strftime/strftime.js, taith, Robin,
 * Matt Bradley, Tim de Koning, Luis Salazar (http://www.freaky-media.com/),
 * FGFEmperor, baris ozdil, Tod Gentille, FremyCompany, Manish, Cord, Slawomir
 * Kaniecki, ReverseSyntax, Mateusz "loonquawl" Zalega, Scott Cariss,
 * Francois, Victor, stensi, vlado houba, date, gabriel paderni, Yannoo,
 * mk.keck, Leslie Hoare, Ben Bryan, Dino, Andrej Pavlovic, Andreas, DtTvB
 * (http://dt.in.th/2008-09-16.string-length-in-bytes.html), Russell Walker,
 * Garagoth, booeyOH, Cagri Ekin, Benjamin Lupton, davook, Atli Þór, jakes,
 * Allan Jensen (http://www.winternet.no), Howard Yeend, Kheang Hok Chin
 * (http://www.distantia.ca/), Luke Smith (http://lucassmith.name), Rival,
 * Diogo Resende
 * 
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL KEVIN VAN ZONNEVELD BE LIABLE FOR ANY CLAIM, DAMAGES
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */ 


function serialize( mixed_value ) {
    // Returns a string representation of variable (which can later be unserialized)  
    // 
    // version: 905.3120
    // discuss at: http://phpjs.org/functions/serialize
    // +   original by: Arpad Ray (mailto:arpad@php.net)
    // +   improved by: Dino
    // +   bugfixed by: Andrej Pavlovic
    // +   bugfixed by: Garagoth
    // +      input by: DtTvB (http://dt.in.th/2008-09-16.string-length-in-bytes.html)
    // +   bugfixed by: Russell Walker
    // %          note: We feel the main purpose of this function should be to ease the transport of data between php & js
    // %          note: Aiming for PHP-compatibility, we have to translate objects to arrays
    // *     example 1: serialize(['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
    // *     example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
    // *     returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'
    var _getType = function( inp ) {
        var type = typeof inp, match;
        var key;
        if (type == 'object' && !inp) {
            return 'null';
        }
        if (type == "object") {
            if (!inp.constructor) {
                return 'object';
            }
            var cons = inp.constructor.toString();
            match = cons.match(/(\w+)\(/);
            if (match) {
                cons = match[1].toLowerCase();
            }
            var types = ["boolean", "number", "string", "array"];
            for (key in types) {
                if (cons == types[key]) {
                    type = types[key];
                    break;
                }
            }
        }
        return type;
    };
    var type = _getType(mixed_value);
    var val, ktype = '';
    
    switch (type) {
        case "function": 
            val = ""; 
            break;
        case "undefined":
            val = "N";
            break;
        case "boolean":
            val = "b:" + (mixed_value ? "1" : "0");
            break;
        case "number":
            val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
            break;
        case "string":
            val = "s:" + encodeURIComponent(mixed_value).replace(/%../g, 'x').length + ":\"" + mixed_value + "\"";
            break;
        case "array":
        case "object":
            val = "a";
            /*
            if (type == "object") {
                var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);
                if (objname == undefined) {
                    return;
                }
                objname[1] = serialize(objname[1]);
                val = "O" + objname[1].substring(1, objname[1].length - 1);
            }
            */
            var count = 0;
            var vals = "";
            var okey;
            var key;
            for (key in mixed_value) {
                ktype = _getType(mixed_value[key]);
                if (ktype == "function") { 
                    continue; 
                }
                
                okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                vals += serialize(okey) +
                        serialize(mixed_value[key]);
                count++;
            }
            val += ":" + count + ":{" + vals + "}";
            break;
    }
    if (type != "object" && type != "array") {
        val += ";";
    }
    return val;
}

function unserialize(data){
    // Takes a string representation of variable and recreates it
    //
    // version: 903.3016
    // discuss at: http://phpjs.org/functions/unserialize
    // +     original by: Arpad Ray (mailto:arpad@php.net)
    // +     improved by: Pedro Tainha (http://www.pedrotainha.com)
    // +     bugfixed by: dptr1988
    // +      revised by: d3x
    // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // %            note: We feel the main purpose of this function should be to ease the transport of data between php & js
    // %            note: Aiming for PHP-compatibility, we have to translate objects to arrays
    // *       example 1: unserialize('a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}');
    // *       returns 1: ['Kevin', 'van', 'Zonneveld']
    // *       example 2: unserialize('a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}');
    // *       returns 2: {firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'}
    var error = function (type, msg, filename, line){throw new this.window[type](msg, filename, line);};
    var read_until = function (data, offset, stopchr){
        var buf = [];
        var chr = data.slice(offset, offset + 1);
        var i = 2;
        while (chr != stopchr) {
            if ((i+offset) > data.length) {
                error('Error', 'Invalid');
            }
            buf.push(chr);
            chr = data.slice(offset + (i - 1),offset + i);
            i += 1;
        }
        return [buf.length, buf.join('')];
    };
    var read_chrs = function (data, offset, length){
        var buf;

        buf = [];
        for(var i = 0;i < length;i++){
            var chr = data.slice(offset + (i - 1),offset + i);
            buf.push(chr);
        }
        return [buf.length, buf.join('')];
    };
    var _unserialize = function (data, offset){
        var readdata;
        var readData;
        var chrs = 0;
        var ccount;
        var stringlength;
        var keyandchrs;
        var keys;

        if(!offset) {offset = 0;}
        var dtype = (data.slice(offset, offset + 1)).toLowerCase();

        var dataoffset = offset + 2;
        var typeconvert = new Function('x', 'return x');

        switch(dtype){
            case 'i':
                typeconvert = function (x) {return parseInt(x, 10);};
                readData = read_until(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
            break;
            case 'b':
                typeconvert = function (x) {return parseInt(x, 10) == 1;};
                readData = read_until(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
            break;
            case 'd':
                typeconvert = function (x) {return parseFloat(x);};
                readData = read_until(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
            break;
            case 'n':
                readdata = null;
            break;
            case 's':
                ccount = read_until(data, dataoffset, ':');
                chrs = ccount[0];
                stringlength = ccount[1];
                dataoffset += chrs + 2;

                readData = read_chrs(data, dataoffset+1, parseInt(stringlength, 10));
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 2;
                if(chrs != parseInt(stringlength, 10) && chrs != readdata.length){
                    error('SyntaxError', 'String length mismatch');
                }
            break;
            case 'a':
                readdata = {};

                keyandchrs = read_until(data, dataoffset, ':');
                chrs = keyandchrs[0];
                keys = keyandchrs[1];
                dataoffset += chrs + 2;

                for(var i = 0;i < parseInt(keys, 10);i++){
                    var kprops = _unserialize(data, dataoffset);
                    var kchrs = kprops[1];
                    var key = kprops[2];
                    dataoffset += kchrs;

                    var vprops = _unserialize(data, dataoffset);
                    var vchrs = vprops[1];
                    var value = vprops[2];
                    dataoffset += vchrs;

                    readdata[key] = value;
                }

                dataoffset += 1;
            break;
            default:
                error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);
            break;
        }
        return [dtype, dataoffset - offset, typeconvert(readdata)];
    };
    return _unserialize(data, 0)[2];
}
