<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<style>
div.metadata_label
{
 float: left;
 width: 100px;
 text-align: right;
 font-style: italic;
}
div.metadata_value
{
 margin-left: 105px;
}
</style>
	<strong>General</strong><br />
    <xsl:for-each select="/lom/general/title/string">
      	<div class="metadata_label">Title</div>
      	<div class="metadata_value">
   			<xsl:value-of select="."/>
   			<xsl:if test="./@language">
	   			(<xsl:value-of select="./@language"/>)
    		</xsl:if>
      	</div>
    </xsl:for-each>
    <xsl:if test="/lom/general/language">
	    <div class="metadata_label">Language</div>
	    <xsl:for-each select="/lom/general/language">
    	  	<div class="metadata_value">
    	  		<xsl:value-of select="."/>
    	  	</div>
    	</xsl:for-each>
    </xsl:if>
    <div class="metadata_label">Description</div>
    <xsl:for-each select="/lom/general/description/string">
      	<div class="metadata_value">
      		<xsl:value-of select="."/>
      		<xsl:if test="./@language">
	   			(<xsl:value-of select="./@language"/>)
    		</xsl:if>
      	</div>
    </xsl:for-each>
	<strong>Life Cycle</strong><br />
    <strong>Technical</strong><br />
   	<xsl:if test="/lom/technical/format">
   	 	<div class="metadata_label">File Format</div>
   	 	<div class="metadata_value"><xsl:value-of select="/lom/technical/format"/></div>
   	</xsl:if>
   	<xsl:if test="/lom/technical/size">
   	 	<div class="metadata_label">File Size</div>
   	 	<div class="metadata_value"><xsl:value-of select="/lom/technical/size"/>b</div>
   	</xsl:if>
</xsl:template>
</xsl:stylesheet>