<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title></title>
<style>
#html-analysis { width: 950px; background-color: #f6f6f6; padding-left: 10px; }
body { margin: 0; padding: 0; min-width: 800px; font-family: Calibri, Arial, sans-serif; font-size: medium; }
h1 { font-size: 2.5em; margin: 0; padding: 6px 0; }
h2 { font-size: 2em; margin: 0; padding: 12px 0 0 0; }
h3 { font-size: 1.4em; margin: 0; padding: 6px 0 0 0; }
table { width: 100%; vertical-align: top; }
ol { margin: 6px 0 0 0; }
dl { float: left; width: 100%; margin: 6px 0; }
dt { float: left; width: 30%; }
dd { float: left; width: 69%; margin: 0; padding: 4px 0 2px 0; font-family: monospace; vertical-align: baseline; }
.ha-clear { clear: both; }
.ha-recommendation { clear: both; font-size: 1.2em; font-weight: bold; color: red; }
.ha-value { font-family: monospace; }
.ha-hidden { color: #bbb; }
</style>
</head>

<body>
<div id="html-analysis">

<h1>Document analysis</h1>

<h2>General info</h2>
<dl>
	<dt>HTML file has been loaded</dt>
	<dd>Contents.htm</dd>
	<dt>File size</dt>
	<dd>9652 bytes</dd>
</dl>
<div class="ha-recommendation">Recommendations:</div>
<ol>
	<li><span class="ha-value">htm</span> - old style of HTML file extension detected. Change it to <span class="ha-value">html</span></li>
</ol>

<h2>Document header</h2>

<h3>Doctype</h3>
<dl>
	<dt>Doctype tag in source file</dt>
	<dd>not found <span class="ha-hidden">/ &lt;!DOCTYPE html&gt;</span></dd>
	<dt>Auto-generated</dt>
	<dd>&lt;!DOCTYPE html PUBLIC &quot;-//W3C//DTD HTML 4.0 Transitional//EN&quot; &quot;http://www.w3.org/TR/REC-html40/loose.dtd&quot;&gt;</dd>
	<dt>HTML version according to doctype</dt>
	<dd>4 <span class="ha-hidden">/ 5</span></dd>
</dl>
<div class="ha-recommendation">Recommendations:</div>
<ol>
	<li>There is no doctype in the document. Set it to <span class="ha-value">&lt;!DOCTYPE html&gt;</span>
	<li>HTML 4 is supposed.</li>
</ol>

<h3>Document encoding</h3>
<dl>
	<dt>Initially set to</dt>
	<dd>Windows-1251</dd>
	<dt>Meta charset tag in source file</dt>
	<dd>not found <span class="ha-hidden">/ &lt;meta charset="UTF-8"&gt;</span></dd>
	<dt>Auto-generated</dt>
	<dd>&lt;meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=Windows-1251&quot;&gt;</dd>
	<dt>Encoding according to meta tag</dt>
	<dd>Windows-1251</dd>
</dl>
<div class="ha-recommendation">Recommendations:</div>
<ol>
	<li><span class="ha-value">Windows-1251</span> is an obsoleted charset. Convert document encoding to <span class="ha-value">UTF-8</span>.</li>
	<li>There is no &lt;meta charset&gt; tag in the document. Set it to HTML 5 format: <span class="ha-value">&lt;meta charset="UTF-8"&gt;</span></li>
</ol>

<h3>Document title</h3>
<dl>
	<dt>Title tag in source file</dt>
	<dd>not found <span class="ha-hidden">/ found</span></dd>
	<dt>Tag value</dt>
	<dd>Высокоскоростные технологии ЛВС</dd>
</dl>

<h3>CSS styles</h3>
<dl>
	<dt>Embedded styles</dt>
	<dd>not found</dd>
	<dt>External style sheets</dt>
	<dd>not found</dd>
</dl>

<h3>JavaScript</h3>
<dl>
	<dt>Embedded scripts</dt>
	<dd>not found</dd>
	<dt>External scripts</dt>
	<dd>not found</dd>
</dl>

<h2>Document structure</h2>

<h3>Headers</h3>
<dl>
	<dt>Headers hierarchy</dt>
	<dd>not found / broken / ok</dd>
</dl>

<h3>Links</h3>
<dl>
	<dt>Links to htm pages</dt>
	<dd>found / not found</dd>
</dl>

<h3>Images</h3>
<dl>
	<dt>Images in file</dt>
	<dd>not found / found</dd>
	<dt>alt attributes</dt>
	<dd>not found / ok</dd>
	<dt>Figures</dt>
	<dd>not found / found</dd>
	<dt>Figures transformation</dt>
	<dd>recommended / not required</dd>
</dl>

<h3>Tags</h3>
<dl>
	<dt>&lt;b&gt; and &lt;i&gt;</dt>
	<dd>not found / found</dd>
	<dt>&lt;font&gt;</dt>
	<dd>not found / found</dd>
	<dt>&lt;center&gt;</dt>
	<dd>not found / found</dd>
	<dt>Empty tags</dt>
	<dd>not found / found</dd>
</dl>

<h3>HTML 5 semantic markup</h3>
<dl>
	<dt>Semantic markup</dt>
	<dd>not found</dd>
	<dt></dt>
	<dd></dd>
</dl>
<div class="ha-clear"></div>

</div>
</body>
</html>
