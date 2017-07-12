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
	<dd>{HTML_FILE_NAME}</dd>
	<dt>File size</dt>
	<dd>{HTML_FILE_SIZE} bytes</dd>
</dl>
{RCM_GEN_INFO}

<h2>Document header</h2>

<h3>Doctype</h3>
<dl>
	<dt>Doctype tag in source file</dt>
	<dd>{DOCTYPE_SRC}</dd>
	<dt>Auto-generated</dt>
	<dd>{DOCTYPE_GENERATED}</dd>
	<dt>HTML version according to doctype</dt>
	<dd>{DOCTYPE_HTML_VERSION}</dd>
</dl>
{RCM_DOCTYPE}

<h3>Document encoding</h3>
<dl>
	<dt>Initially set to</dt>
	<dd>{ENCODING_INITIAL}</dd>
	<dt>Meta charset tag in source file</dt>
	<dd>{ENCODING_TAG}</dd>
	<dt>Auto-generated</dt>
	<dd>{ENCODING_TAG_GENERATED}</dd>
	<dt>Encoding according to meta tag</dt>
	<dd>{ENCODING_CHARSET}</dd>
</dl>
{RCM_ENCODING}

<h3>Document title</h3>
<dl>
	<dt>Title tag in source file</dt>
	<dd>{TITLE_SRC}</dd>
</dl>
{RCM_TITLE}

<h3>CSS styles</h3>
<dl>
	<dt>Internal styles</dt>
	<dd>{CSS_INT}</dd>
	<dt>External style sheets</dt>
	<dd>{CSS_EXT}</dd>
</dl>
{RCM_CSS}

<h3>JavaScript</h3>
<dl>
	<dt>Internal scripts</dt>
	<dd>{JS_INT}</dd>
	<dt>External scripts</dt>
	<dd>{JS_EXT}</dd>
</dl>
{RCM_SCRIPTS}

<h2>Document structure</h2>

<h3>Headers</h3>
<dl>
	<dt>Headers hierarchy</dt>
	<dd>{DOC_HEADERS}</dd>
</dl>
{RCM_HEADERS}

<h3>Links</h3>
<dl>
	<dt>Links to htm pages</dt>
	<dd>{DOC_LINKS}</dd>
</dl>
{RCM_LINKS}

<h3>Images</h3>
<dl>
	<dt>Images in file</dt>
	<dd>{DOC_IMG_SRC}</dd>
	<dt>alt attributes</dt>
	<dd>{DOC_IMG_ALT}</dd>
	<dt>Figures</dt>
	<dd>{DOC_FIG}</dd>
	<dt>Figures transformation</dt>
	<dd>{DOC_FIG_TRANSFORM}</dd>
</dl>
{RCM_IMAGES}

<h3>Tags</h3>
<dl>
	<dt>&lt;b&gt; and &lt;i&gt;</dt>
	<dd>{DOC_TAG_B_I}</dd>
	<dt>&lt;font&gt;</dt>
	<dd>{DOC_TAG_FONT}</dd>
	<dt>&lt;center&gt;</dt>
	<dd>{DOC_TAG_CENTER}</dd>
	<dt>Empty tags</dt>
	<dd>{DOC_TAG_EMPTY}</dd>
	<dt>Inline styles in tags</dt>
	<dd>{DOC_TAG_INLINE_STYLES}</dd>
</dl>
{RCM_TAGS}

<h3>HTML 5 semantic markup</h3>
<dl>
	<dt>Semantic markup</dt>
	<dd>{DOC_SEM_MARKUP}</dd>
</dl>
{RCM_SEM_MARKUP}
<div class="ha-clear"></div>

</div>
</body>
</html>
