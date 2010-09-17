<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:ceo="http://www.chamilo.org/xsd/ceo_v1p0" >
	<xsl:template match="/">
		<html><head>
			<title><xsl:value-of select="ceo:export/ceo:objects/ceo:object/ceo:general/ceo:title"/></title>
			<link href="resources/object_view.css" rel="stylesheet" type="text/css"/>
		</head><body>
		<xsl:for-each select="ceo:export/ceo:objects/ceo:object">
	    		<span class="title"><xsl:value-of select="ceo:general/ceo:title"/></span>
	    		<span class="id"><xsl:value-of select="@catalog"/> - <xsl:value-of select="@id"/></span>
	    		<span class="entry"><label>Id</label><span class="description"><xsl:value-of select="ceo:general/ceo:id"/></span></span>
	    		<span class="entry"><label>Catalog</label><span class="description"><xsl:value-of select="@catalog"/></span></span>
	    		<span class="entry"><label>Title</label><span class="description"><xsl:value-of select="ceo:general/ceo:title"/></span></span>
	    		<span class="entry"><label>Description</label><span class="description"><xsl:value-of select="ceo:general/ceo:description"/></span></span>
	    		<span class="entry"><label>Type</label><span class="description"><xsl:value-of select="ceo:general/ceo:type"/></span></span>
	    		<span class="entry"><label>Created</label><span class="description">
				<xsl:value-of select="substring(ceo:general/ceo:created, 1, 10)"/> : <xsl:value-of select="substring(ceo:general/ceo:created, 12, 10)"/>
			</span></span>
	    		<span class="entry"><label>Modified</label><span class="description">
				<xsl:value-of select="substring(ceo:general/ceo:modified, 1, 10)"/> : <xsl:value-of select="substring(ceo:general/ceo:modified, 12, 10)"/>
			</span></span>
	    		<span class="entry"><label>Comment</label><span class="comment"><xsl:value-of select="ceo:general/ceo:comment"/></span></span>

			<h3>Children</h3>
			<table>
				<tr><th>Id</th><th>Title</th><th>Description</th><th>Added</th></tr>
			<xsl:for-each select="ceo:sub_items/ceo:sub_item">
				<tr> 
	    				<td class="sub_id"><xsl:value-of select="ceo:general/ceo:ref_id"/></td>
	    				<td class="sub_title"><xsl:value-of select="ceo:general/ceo:title"/></td>
	    				<td class="sub_description"><xsl:copy-of select="ceo:general/ceo:description"/></td>
	    				<td class="sub_comment">
						<xsl:value-of select="substring(ceo:general/ceo:add_date, 1, 10)"/> : <xsl:value-of select="substring(ceo:general/ceo:add_date, 12, 10)"/>
					</td>
				</tr>
    			</xsl:for-each>
			</table>
    		</xsl:for-each>

    		</body></html>
	</xsl:template>
</xsl:stylesheet>