/**
 * Copyright © 2010 Influxis All rights reserved.
 **/
package com.influxis.views
{
	import com.influxis.controls.MediaManager;
	import com.influxis.events.StreamEvent;
	
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.NetStatusEvent;
	import flash.media.Camera;
	import flash.media.SoundTransform;
	import flash.media.Video;
	import flash.net.NetConnection;
	import flash.net.NetStream;

	public class VideoView extends Sprite
	{
		public static const STREAM_PLAY_START:String = 	"streamPlayStart";
		public static const STREAM_PLAY_STOP:String = 	"streamPlayStop";
		
		public var bkgImage:MovieClip;
		public var videoObject:Video;
		public var ns:NetStream;
		public var nc:NetConnection;
		public var localUserID:String;
		public var streamName:String;
		public var isPlaying:Boolean = false;
		public var hasCamera:Boolean = false;
		public var maintainAspectRatio:Boolean = true;
		
		protected var _media:MediaManager;
		protected var _width:Number;
		protected var _height:Number;
		protected var _muted:Boolean = false;
		
		protected var _snd:SoundTransform;
		
		public function VideoView()
		{
			
		}
		
		//camera methods
		
		//attches cam for publishing media in stream when camera is changed AND we are showing our camera.
		protected function attachMedia():void
		{
			if( !this.hasCamera ) return;
			//if there is no media or if the cam AND the mic are null, reuturn false.
			if( this._media == null || this._media.cam == null && this._media.mic == null ) {
				trace("Error:  No Media attached.");
				return;
			}
			
			var cam:Camera = this._media.cam;
			//tracer("attachMedia cam: "+cam.name);
			this.videoObject.attachCamera( cam );
		};
		
		//handles when camera and mic are updated. so we can update stream.
		protected function onMediaChanged( p_e:Event ):void
		{
			var type:String = p_e.type;
			//tracer("onMediaChanged "+type);
			switch( type ){
				case MediaManager.CAM_CHANGE:
					attachMedia();
					break;
			}
		}
		
		protected function playCamera():Boolean
		{
			//tracer("playCamera");
			if( this.isPlaying ){
				this.stopStream();
			}
			
			this.videoObject.attachCamera( this.mediaManager.cam );
			this.videoObject.visible = true;
			
			return true;
		}
		
		protected function stopCamera():void
		{
			//tracer("stopCamera");
			this.videoObject.attachCamera(null);
			this.videoObject.visible = false;
		}
		
		//remote stream methods
		
		protected function onStatusEvents( p_e:NetStatusEvent ):void
		{
			var code:String = p_e.info.code;
			//tracer("onStatusEvent.code "+code);
			/*if( code == "NetStream.Play.Start" ){
				tracer("cam o "+this.videoObject);
			}*/
		}
		
		protected function playStream( p_s:String ):void
		{
			//tracer("playStream "+p_s);
			streamName = p_s;
			if( ns == null ){
				this.createStream();
				_snd.volume = _muted ? 0 : 1;
				ns.receiveAudio( !_muted );
			}
			ns.play( streamName );
			
			isPlaying = true;
			dispatchEvent( new StreamEvent( VideoView.STREAM_PLAY_START, streamName ) );
			
			videoObject.attachNetStream( ns );
			videoObject.visible = true;
		}
		
		protected function stopStream():void
		{
			//tracer("stopStream ");
			if( ns != null ) ns.close();
			isPlaying = false;
			streamName = null;
			videoObject.attachNetStream( null );
			videoObject.visible = false;
			dispatchEvent( new StreamEvent( VideoView.STREAM_PLAY_STOP, streamName ) );
		}
		
		protected function createStream():void
		{
			//tracer("createStream");
			ns = new NetStream( nc );
			ns.client = this;
			ns.addEventListener( NetStatusEvent.NET_STATUS, this.onStatusEvents );
			
			_snd = ns.soundTransform;
		};
		
		protected function onMetaData( p_o:Object ):void
		{
			//tracer("onMetaData "+p_o);
		}
		
		protected function onPlayStatus ( p_o:Object ):void
		{
			//tracer("onPlayStatus "+p_o);
		}
		
		//public
		
		public function setSize( p_width:int, p_height:int ):void
		{
			//tracer("- "+p_width+", "+p_height);
			if( p_width == this._width && p_height == this._height ) return;
			
			this._height = p_height;
			this._width = p_width;
			
			if( maintainAspectRatio ){
				var r:Number = bkgImage.height/bkgImage.width;//ratio
				//width takes priority
				if( this._width < this._height ){
					bkgImage.width = this._width;
					bkgImage.height = Math.round(bkgImage.width*r);
				}//height takes priority
				else {
					bkgImage.height = this._height;
					bkgImage.width = Math.round(bkgImage.height/r);
				}
			} else {
				bkgImage.width = this._width;
				bkgImage.height = this._height;
			}
			this.videoObject.width = this.bkgImage.width;
			this.videoObject.height = this.bkgImage.height;
		}
		
		public function playStreamNamed( p_s:String ):void
		{
			if( this.nc == null || !this.nc.connected ) return;
			
			if( this.isPlaying ){
				this.stopStream();
			} else if( this.hasCamera ){
				stopCamera();
			}
			
			var str:String = p_s;
			this.playStream( str );
		}
		
		public function stopPlayingStream():void
		{
			if( this.nc == null || !this.nc.connected ) return;
			if( this.isPlaying ){
				this.stopStream();
			}
		}
		
		public function connect( p_nc:NetConnection ):void
		{
			//tracer("connect "+p_nc);
			this.nc = p_nc;
		}
		
		public function close():void
		{
			if( this.isPlaying ){
				this.stopStream();
			}
			if( this.hasCamera ){
				stopCamera();
			}
		}
		
		public function userData( p_o:Object ):void
		{
			//tracer("userData "+p_o.id);
			this.localUserID = p_o.id;
		}
		
		public function set mute( p_b:Boolean ):void
		{
			this._muted = p_b;
			if( this.ns != null ) {
				//this.tracer( "muted "+_muted );
				_snd.volume = _muted ? 0 : 1;
				ns.receiveAudio( !_muted );
			}
		}
		public function get mute():Boolean
		{
			return this._muted;
		}
		
		public function set smoothing( p_b:Boolean ):void
		{
			this.videoObject.smoothing = p_b;
		}
		public function get smoothing():Boolean
		{
			return this.videoObject.smoothing;
		}
		
		public function set showLocalVideo( p_b:Boolean ):void
		{
			
			if( p_b ){
				playCamera();
			} else if( !p_b ){
				stopCamera();
			}
			this.hasCamera = p_b;
		}
		public function get showLocalVideo():Boolean
		{
			return hasCamera;
		}
		
		//saves media manger to listen to changes in camera and mic selections.
		public function set mediaManager( p_m:MediaManager ):void
		{
			if( _media != null ){
				this._media.addEventListener(MediaManager.CAM_CHANGE, onMediaChanged );
				_media = null;
			}
			//set the media manager that has the camera and mic data.
			_media = p_m;
			this._media.addEventListener(MediaManager.CAM_CHANGE, onMediaChanged, false, 0, true );
			
		};
		
		public function get mediaManager():MediaManager
		{
			return _media;
		}
		
		//override public function get width():Number
		public function get vidWidth():Number
		{
			return isNaN(this._width) ? this.width : this._width;
		}
		
		//override public function get height():Number
		public function get vidHeight():Number
		{
			return isNaN(this._height) ? this.height : this._height;
		}
		
		private function tracer( p_msg:* ):void
		{
			//trace("#VideoView."+this.name+"#  "+p_msg);
		}
	}
}