/**
 * Copyright © 2010 Influxis All rights reserved.
 **/
package  
{
	import com.influxis.controls.MediaManager;
	import com.influxis.events.StreamEvent;
	import com.influxis.fms.controls.LiveAVPublisher;
	import com.influxis.fms.controls.LiveFeedController;
	import com.influxis.fms.controls.NetController;
	import com.influxis.views.MediaControlView;
	import com.influxis.views.MediaPresetView;
	import com.influxis.views.MenuView;
	import com.influxis.views.VideoView;
	import flash.system.System;
	
	
	import fl.controls.Button;
	
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.display.StageDisplayState;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.SyncEvent;
	import flash.events.TimerEvent;
	import flash.net.NetConnection;
	import flash.net.SharedObject;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.text.TextField;
	import flash.utils.Timer;
	import flash.display.LoaderInfo;
	
	
	public class FacesMain extends Sprite
	{
		public static var version:String = "0.1.0.0";
		//handles camera and mic for the entire application.
		public var media:MediaManager;
		//handles connection and disconnection for the entire application.
		public var con:NetController;
		//handles the videos feeds starting and stoping on the server.
		public var feed:LiveFeedController;
		//handles publighins UP to the server.
		public var publisher:LiveAVPublisher;
		// keeps window from sizing too small
		public var minWidth:int = 670;
		public var minHeight:int = 550;
		//tracks a simple use count.
		public var so:SharedObject;
		//setting for sounds
		public var isMuted:Boolean = 		false;
		//setting for popup panels 
		public var mediaUIMode:String = 	"Presets";
		//rtmp connection to influxis service.
		public var connectPath:String;
		//path to the settings.xml file that the application uses for some dynamic settings.
		public var appSettingsPath:String = "settings.xml";
		
		//UI
		public var mediaControls:MediaControlView;
		public var mediaPresents:MediaPresetView;
		public var videoView1:VideoView;
		public var videoView2:VideoView;
		public var streamButton:Button;
		public var fullScreenButton:Button;
		public var menu:MenuView;
		public var bkgImage:MovieClip;
		public var conAlert:MovieClip;
		public var streamButtonText:TextField;
		public var hitSpace:MovieClip;
		public var mainView:VideoView;
		public var pipView:VideoView;
		public var pipWidth:int;
		public var pipHeight:int;
		public var url:String
		public var roomid:String
	
		
		
		//radio button pre-sets for camera
		public var camSizeSelections:Object = {
			r0:{width:256,height:192},
			r1:{width:320,height:240},
			r2:{width:448,height:336},
			r3:{width:640,height:480}
		}
		//preset settings for camera and mic
		public var presetSelections:Object = {
			low:{width:320,height:240,fps:10,quality:80,rate:11, selected:true},
			medium:{width:320,height:240,fps:20,quality:90,rate:22},
			high:{width:640,height:480,fps:15,quality:80,rate:22}
		}
		
		//buttonURL.enabled = false;
		public function FacesMain()
		{
			//handle Stage adjusting
			stage.align = StageAlign.TOP_LEFT;
			stage.scaleMode = StageScaleMode.NO_SCALE;
			stage.addEventListener( Event.RESIZE, onResizeStage );
			//var l:String = this.loaderInfo.parameters.roomid;
			
				try {
					var paramObj:Object=LoaderInfo(this.root.loaderInfo).parameters//{roomid:"test"}
					roomid = paramObj.roomid
					//textURL.text= paramObj.roomid;
					} catch (error:Error) {
					//trace(error);
				}
			

			//media default settings - sets defaults of camera and mic
			var o:Object = camSizeSelections.r1;
			MediaManager.DEFAULT_CAM_HEIGHT = o.height;
			MediaManager.DEFAULT_CAM_WIDTH = o.width;
			MediaManager.DEFAULT_CAM_FPS = 15;
			MediaManager.DEFAULT_CAM_QUALITY = 80;
			MediaManager.DEFAULT_CAM_BANDWIDTH = 0;
			MediaManager.DEFAULT_MIC_GAIN = 70;
			
			//handles mic cam controlls, switching quality etc..
			media = new MediaManager();
			media.addEventListener( MediaManager.CAM_CHANGE, onMediaUpdate );
			media.addEventListener( MediaManager.READY, onMediaUpdate );
			
			//handles streaming of audio / video to the service
			publisher = new LiveAVPublisher();
			publisher.mediaManager = media;
			
			//handles streaming of audio / video from the service
			feed = new LiveFeedController();
			feed.addEventListener( LiveFeedController.REMOTE_STREAM_PLAY, onStreamStatus );
			feed.addEventListener( LiveFeedController.REMOTE_STREAM_STOP, onStreamStatus );
			feed.addEventListener( LiveFeedController.LOCAL_STREAM_PLAY, onStreamStatus );
			feed.addEventListener( LiveFeedController.LOCAL_STREAM_STOP, onStreamStatus );
			
			//handles connection to service.
			con = new NetController();
			con.addEventListener( NetController.CONNECTED, onConnection );
			con.addEventListener( NetController.DISCONNECTED, onConnection );
			con.connectToNetwork( [feed, publisher, videoView1, videoView2] );
			/*con.path = connectPath;
			con.connect();*/
			
			//UI
			mediaControls.addEventListener( MediaControlView.PRESET_PRESSED, onMenuEvents );
			mediaControls.addEventListener( MediaControlView.ON_CLOSE, onMenuEvents );
			mediaControls.mediaManager = this.media;
			mediaControls.camSizeSelections = this.camSizeSelections;//selections for radio buttons in view.
			mediaControls.visible = false;
			mediaControls.alpha = 1;
			
			mediaPresents.addEventListener( MediaPresetView.CUSTOM_PRESSED, onMenuEvents );
			mediaPresents.addEventListener( MediaPresetView.ON_CLOSE, onMenuEvents );
			mediaPresents.mediaManager = this.media;
			mediaPresents.presetSelections = this.presetSelections;//selections for radio buttons in view.
			mediaPresents.visible = false;
			mediaPresents.alpha = 1;
			
			//video windows 1 and 2
			videoView1.mediaManager = media;
			videoView2.mediaManager = media;
			videoView1.smoothing = true;
			videoView2.smoothing = true;
			videoView1.mouseEnabled = false;
			videoView2.mouseEnabled = false;
			
			//the video which is set to be the main one.
			mainView = videoView1;
			
			//the video which is set to be the small one.
			pipView = videoView2;
			pipView.maintainAspectRatio = false;
			if( pipWidth == 0 ) pipWidth = pipView.width;
			if( pipHeight == 0 ) pipHeight = pipView.height;
			//make sure that the pip window is not scaled.
			pipView.scaleX = 1;
			pipView.scaleY = 1;
			
			menu.addEventListener( MenuView.ON_SETTINGS, onMenuEvents );
			menu.addEventListener( MenuView.ON_FULLSCREEN, onMenuEvents );
			menu.addEventListener( MenuView.ON_PUBLISH, onMenuEvents );
			menu.addEventListener( MenuView.ON_MUTED, onMenuEvents );
			menu.enabled = false;
			
			resize();
			//if the connection path exists just connect. otherwise load our settings xml
			if( this.connectPath != null ){
				this.doConnect( this.connectPath );
			} else {
				loadSettings();
			}
			
			conAlert.infoText.text = "Connecting...";
		};
		
		protected function onConnection( p_e:Event ):void
		{
			var type:String = p_e.type;
			tracer( "connection: "+type );
			switch( type ){
				case NetController.CONNECTED:
					//hide alert window after a few seconds
					conAlert.infoText.text = "Connected.";
					var t:Timer = new Timer(800, 1);
					t.addEventListener( TimerEvent.TIMER, hideAlert );
					t.start();
					
					//detect when mouse is not on menu to close it.
					hitSpace.addEventListener(MouseEvent.CLICK, onHitSpace );
					hitSpace.useHandCursor = true;
					hitSpace.buttonMode = true;
					
					resize();
					media.init();
					mediaControls.init();
					mediaPresents.init();
					menu.enabled = true;
					
					createSharedObject();
					break;
				case NetController.DISCONNECTED:
					conAlert.infoText.text = "Disconnected";
					conAlert.visible = true;
					videoView1.close();
					videoView2.close();
					this.resize();
					break;
			}
		}
		
		protected function createSharedObject():void
		{
			var nc:NetConnection = this.con.nc;
			so = SharedObject.getRemote( "Users", nc.uri );
			so.addEventListener(SyncEvent.SYNC, onSyncEvent );
			so.connect( nc );
		}
		
		protected function onSyncEvent( p_e:SyncEvent ):void
		{
			var data:Object = so.data;
			var userCount:Number = Number(data.userCount);
			//tracer("test "+userCount);
			if( !isNaN(data.userCount) ){
				menu.userCount = userCount;
			}
		}
		
		protected function onMediaUpdate( p_e:Event ):void
		{
			var type:String = p_e.type;
			//tracer("onMediaUpdate "+type);
			switch( type ){
				case MediaManager.CAM_CHANGE:
					//tracer("cam "+this.media.selectedCamIndex);
					break;
				case MediaManager.READY:
					media.camBandwidth = 0;
					publisher.startPublish();
					break;
			}
		};
		
		//handles events dispatched by control menus that interface with end users.
		protected function onMenuEvents( p_e:Event ):void
		{
			var type:String = p_e.type;
			//tracer("onMenuEvent "+type);
			switch( type ){
				case MediaPresetView.CUSTOM_PRESSED:
					showMenu( this.mediaPresents, false );
					showMenu( this.mediaControls, true );
					mediaUIMode = "Custom";
					break;
				case MediaControlView.PRESET_PRESSED:
					showMenu( this.mediaControls, false );
					showMenu( this.mediaPresents, true );
					mediaUIMode = "Presets";
					break;
				case MenuView.ON_PUBLISH:
					if( publisher.isPublishing ){
						publisher.stopPublish();
					} else {
						publisher.startPublish();
					}
					break;
				case MenuView.ON_FULLSCREEN:
					if( stage.displayState == StageDisplayState.NORMAL ){
						stage.displayState = StageDisplayState.FULL_SCREEN;
					} else {
						stage.displayState = StageDisplayState.NORMAL;
					}
					break;
				case MenuView.ON_SETTINGS:
					if( this.mediaUIMode == "Custom" ){
						showMenu( this.mediaControls, !this.mediaControls.visible );
					} else {
						showMenu( this.mediaPresents, !this.mediaPresents.visible );
					}
					break;
				case MenuView.ON_MUTED:
					isMuted = !isMuted;
					
					menu.isMuted = isMuted;
					videoView1.mute = isMuted;
					videoView2.mute = isMuted;
					
					break;
				case MediaPresetView.ON_CLOSE:
				case MediaControlView.ON_CLOSE:
					mediaControls.visible = false;
					mediaPresents.visible = false;
					break;
			}
		}
		
		//close menu's when users cursor clicks on main video area.
		protected function onHitSpace( p_e:MouseEvent ):void
		{
			var type:String = p_e.type;
			switch( type ){
				case MouseEvent.CLICK:
					if( this.isMenuVisible ){
						//if menu open, close menu otherwise swap video windows.
						mediaPresents.visible = false;
						this.mediaControls.visible = false;
					} else {
						this.switchVideoWindows();
					}
					break;
				case MouseEvent.MOUSE_OVER:
					
					break;
				case MouseEvent.MOUSE_OUT:
					
					break;
			}
		}
		
		protected function showMenu( p_menu:DisplayObject, p_b:Boolean ):void
		{
			//tracer("showMenu "+p_menu)
			p_menu.visible = p_b;
		}
		
		protected function onResizeStage( p_e:Event ):void
		{
			//tracer( this.stage.stageHeight );
			this.resize();
		}
		
		//catches stream events coming from server.
		protected function onStreamStatus( p_e:StreamEvent ):void
		{
			var type:String = p_e.type;
			var name:String = p_e.streamName;
			//tracer("onStreamStatus type: "+type+": "+name);
			var view:VideoView;
			switch( type ){
				case LiveFeedController.REMOTE_STREAM_PLAY:
					if( !this.videoView1.isPlaying && !this.videoView1.hasCamera ){
						view = this.videoView1;
					} else if( !this.videoView2.isPlaying && !this.videoView2.hasCamera ){
						view = this.videoView2;
					}
					if( view != null ){
						view.playStreamNamed(name);
					}
					break;
				case LiveFeedController.REMOTE_STREAM_STOP:
					//todo - clear video that has closed.
					if( this.videoView1.streamName == name ){
						this.videoView1.stopPlayingStream();
					}
					if( this.videoView2.streamName == name ){
						this.videoView2.stopPlayingStream();
					}
					break;
				case LiveFeedController.LOCAL_STREAM_PLAY:
					if( !this.videoView1.isPlaying && !this.videoView1.hasCamera ){
						view = this.videoView1;
					} else if( !this.videoView2.isPlaying && !this.videoView2.hasCamera ){
						view = this.videoView2;
					}
					if( view != null ){
						view.showLocalVideo = true;
					}
					break;
				case LiveFeedController.LOCAL_STREAM_STOP:
					if( this.videoView1.hasCamera ){
						view = this.videoView1;
					} else if( this.videoView2.hasCamera ){
						view = this.videoView2;
					}
					if( view != null ){
						view.showLocalVideo = false;
					}
					break;
			}
			
			var v1InUse:Boolean = this.videoView1.isPlaying;
			var v2InUse:Boolean = this.videoView2.isPlaying;
			
			if( v1InUse && v2InUse ){
				//if there is no available stream.
				menu.streamStatus = MenuView.STREAM_IN_USE;
			} else {
				//if we are streaming then set state, otherwise set state to allow a stream.
				menu.streamStatus = this.videoView1.hasCamera || this.videoView2.hasCamera ? MenuView.STREAM_LOCAL_USER : MenuView.STREAM_AVAILABLE;
			}
		}
		
		protected function hideAlert(p_e:TimerEvent=null):void
		{
			this.conAlert.visible = false;
		}
		
		protected function onSettings( p_e:Event ):void
		{
			var ldr:URLLoader = p_e.target as URLLoader;
			var xml:XML = new XML( ldr.data );
			var s:String = (xml.rtmp.@path).toString();
			s = s + "/" + roomid
			url = (xml.rtmp.@url).toString();
			
			try{
				this.doConnect( s );
				trace(s)
			} catch( e:Error ){
				this.conAlert.infoText.text = "Error!!!";
			}
		}
		
		//loads external settings xml file.
		protected function loadSettings():void
		{
			var req:URLRequest = new URLRequest( appSettingsPath );
			var ldr:URLLoader = new URLLoader();
			ldr.addEventListener(Event.COMPLETE, onSettings );
			ldr.load( req );
		}

		//PUBLIC 
		public function doConnect( p_s:String ):void
		{
			if( connectPath != p_s ){
				connectPath = p_s;
				textURL.text = url + "?roomid=" + roomid;
				//textURL.textColor = #000000;
				buttonURL.enabled = true;
				function onClick(event:MouseEvent):void{
					textURL.setSelection(0, textURL.text.length)
  					System.setClipboard(textURL.text);
					textURL.visible = false;
					buttonURL.visible = false;

					}
					
				buttonURL.addEventListener(MouseEvent.CLICK,onClick);
			}
			
			con.path = connectPath;
			con.connect();
		}
		public function switchVideoWindows():void
		{
			pipView = pipView == this.videoView1 ? this.videoView2 : this.videoView1;
			mainView = pipView == this.videoView1 ? this.videoView2 : this.videoView1;
			//tracer("pip "+pipView.name+"  main "+mainView.name);
			pipView.maintainAspectRatio = false;
			mainView.maintainAspectRatio = true;
			
			this.swapChildren( pipView, mainView );
			
			this.resize();
		}
		
		public function get isMenuVisible():Boolean
		{
			return this.mediaControls.visible || this.mediaPresents.visible;
		}
		
		public function resize():void
		{
			var w:int = stage.stageWidth;
			var h:int = stage.stageHeight;
			var viewableHeight:int = Math.round(h - menu.height);
			if( w < minWidth ) w = minWidth;
			if( h < minHeight ) h = minHeight;
			
			menu.x = 0;
			menu.y = viewableHeight;
			menu.width = w;
			
			bkgImage.width = w;
			bkgImage.height = h;
			hitSpace.width = w;
			hitSpace.height = h;
			
			pipView.setSize( pipWidth, pipHeight );
			mainView.setSize( w, viewableHeight );
			
			var absCenter:Number = Math.round( w - mainView.width )/2;
			mainView.x = absCenter < 0 ? 0 : absCenter;
			mainView.y = 0;
			pipView.x = mainView.x + 10;
			pipView.y = mainView.height - (pipHeight + 10);
			if( conAlert.visible ){
				conAlert.x = w/2;
				conAlert.y = h/2 - menu.height;
			}
		}
		
		private function tracer( p_msg:* ):void
		{
			trace( "#TwoCamMain#  "+p_msg );
		}
	}
	
}