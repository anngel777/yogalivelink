<div id="page">
		<!-- this A tag is where your Flowplayer will be placed. it can be anywhere -->
		<a  
			 href="http://e1h13.simplecdn.net/flowplayer/flowplayer.flv"  
			 style="display:block;width:520px;height:330px"  
			 id="player"> 
		</a> 
        
        
		<!-- this will install flowplayer inside previous A- tag. -->
		<script>            
			flowplayer("player", "../flowplayer/flowplayer-3.2.2.swf");
            
		</script>
        
	</div>

    
    
    
    
<?php


# NOTES
# ============================================================
/*
<p>If you are running these examples <strong>locally</strong> and not on some webserver you must edit your     <a href="http://www.macromedia.com/support/documentation/en/flashplayer/help/settings_manager04.html">        Flash security settings</a>. </p>

<p class="less">    Select "Edit locations" &gt; "Add location" &gt; "Browse for files" and select    flowplayer-x.x.x.swf you just downloaded.</p>

<h2>Documentation</h2>
<p>			<a href="http://flowplayer.org/documentation/installation/index.html">Flowplayer installation</a>		</p>
<p>			<a href="http://flowplayer.org/documentation/configuration/index.html">Flowplayer configuration</a>		</p>
<p>			See this identical page on <a href="http://flowplayer.org/demos/example/index.htm">Flowplayer website</a> 		</p>
*/




# SCRIPT
# ============================================================
$script = '/jslib/flowplayer-3.2.2.min.js';
AddScriptInclude($script);



$script = <<<SCRIPT
$("live", "http://releases.flowplayer.org/swf/flowplayer-3.2.2.swf", {

	clip: {
		url: 'my_lifecast',
		live: true,
		// configure clip to use influxis as our provider, it uses our rtmp plugin
		provider: 'influxis'
	},

	// streaming plugins are configured under the plugins node
	plugins: {

		// here is our rtpm plugin configuration
		influxis: {
			url: 'flowplayer.rtmp-3.2.1.swf',

			// netConnectionUrl defines where the streams are found
			netConnectionUrl: 'rtmp://cyzy7r959.rtmphost.com/flowplayer'
		}
	}
});
SCRIPT;
#addScript($script);
#addScriptOnReady($script);



# CSS STYLE
# ============================================================

$style = <<<STYLE
body {
	background-color:#fff;	
	font-family:"Lucida Grande","bitstream vera sans","trebuchet ms",verdana,arial;
	text-align:center;
}

#page {
	background-color:#efefef;
	/*width:600px;*/
	/*margin:50px auto;*/
	/*padding:20px 150px 20px 50px;*/
	/*min-height:600px;*/
	border:2px solid #fff;
	outline:1px solid #ccc;
	text-align:left;
}

a {
	color:#295c72;		
}
STYLE;
AddStyle($style);