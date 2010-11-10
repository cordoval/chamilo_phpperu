
function giveFocus()
    {
      document.visorFreeMind.focus();
    }
// <![CDATA[
// for allowing using http://.....?mindmap.mm mode
function getMap(map){
  var result=map;
  var loc=document.location+'';
  if(loc.indexOf(".mm")>0 && loc.indexOf("?")>0){
        result=loc.substring(loc.indexOf("?")+1);
  }
  return result;
}
var fo = new FlashObject("visorFreemind.swf", "visorFreeMind", "100%", "50%", 6, "#9999ff");
fo.addParam("quality", "high");
fo.addParam("bgcolor", "#a0a0f0");
fo.addVariable("openUrl", "_blank");
fo.addVariable("startCollapsedToLevel","3");
fo.addVariable("maxNodeWidth","200");
//
fo.addVariable("mainNodeShape","elipse");
fo.addVariable("justMap","false");
fo.addVariable("initLoadFile",getMap("lerenOndernemenVzw.mm"));
fo.addVariable("defaultToolTipWordWrap",200);
fo.addVariable("offsetX","left");
fo.addVariable("offsetY","top");
fo.addVariable("buttonsPos","top");
fo.addVariable("min_alpha_buttons",20);
fo.addVariable("max_alpha_buttons",100);
fo.addVariable("scaleTooltips","false");



fo.write("flashcontent");
// ]]>
	


