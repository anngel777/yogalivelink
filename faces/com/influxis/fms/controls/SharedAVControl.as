/**
 * Copyright © 2010 Influxis All rights reserved.
 **/
package com.influxis.fms.controls
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.NetStatusEvent;
	import flash.events.SyncEvent;
	import flash.media.Camera;
	import flash.media.Microphone;
	import flash.net.NetConnection;
	import flash.net.NetStream;
	import flash.net.SharedObject;

	public class SharedAVControl extends EventDispatcher
	{
		public static const REMOTE_STREAM_PLAY:String = "remoteStreamPlay";
		public static const REMOTE_STREAM_STOP:String = "remoteStreamStop";
		
		public var nc:NetConnection;
		public var ns:NetStream;
		public var nsRemote:NetStream;
		public var so:SharedObject;
		public var streamName:String;
		public var remoteStreamName:String;
		public var isPublishing:Boolean = 			false;
		public var isPlaying:Boolean = 				false;
		public var isSyncronized:Boolean = 			false;
		
		protected var _media:MediaManager;
		protected var _uniqueID:String;
		protected var _localUserId:String;
		
		
		public function SharedAVControl()
		{
		}
		
		protected function onStatusEvents( p_e:NetStatusEvent ):void
		{
			var code:String = p_e.info.code;
			//tracer("onStatusEvent.code "+code);
		}
		
		protected function createSharedObject():void
		{
			//tracer("createSharedObject "+this._uniqueID);
			if( this._uniqueID == null){
				trace("ERROR: This SharedAVControl needs a uniqueIdentifier to connect.");
			}
			if( so != null) {
				so.removeEventListener(SyncEvent.SYNC, onSyncEvent );
				so.close();
			}
			so = SharedObject.getRemote( this._uniqueID, this.nc.uri, false );
			so.addEventListener(SyncEvent.SYNC, onSyncEvent, false, 0, true );
			var o:Object = new Object();
				o.onPublish = function( p_client:Object, p_stream:Object ):void{
					//trace("onPublish "+p_client.id+", "+p_stream.name);
					if( p_stream.name != streamName ) {
						playRemoteStream( p_stream.name );
					}
				}
				o.onUnpublish = function( p_client:Object, p_stream:Object ):void{
					//trace("onUnpublish "+p_client.id+", "+p_stream.name);
					if( remoteStreamName == p_stream.name ){
						stopRemoteStream();
					}
				}
			so.client = o;
			so.connect( this.nc );
		}
		
		protected function onSyncEvent( p_e:SyncEvent ):void
		{
			//tracer("onSyncEvent "+p_e);
			var data:Object = so.data;
			//check for other stream that is streaming.
			for( var i:String in data ){
				var item:Object = data[i];
				if( item.stream != this.streamName && item.stream != null){
					playRemoteStream( item.stream );
					break;
				}
				so.removeEventListener(SyncEvent.SYNC,onSyncEvent);
			}
		}
		
		protected function playRemoteStream( p_s:String ):void
		{
			//tracer("playRemoteStream "+p_s);
			remoteStreamName = p_s;
			nsRemote.play( remoteStreamName );
			this.dispatchEvent( new Event( SharedAVControl.REMOTE_STREAM_PLAY ) );
		}
		
		protected function stopRemoteStream():void
		{
			//tracer("stopRemoteStream ");
			nsRemote.close();
			this.dispatchEvent( new Event( SharedAVControl.REMOTE_STREAM_STOP ) );
		}
		
		protected function createStream():void
		{
			nsRemote = new NetStream( nc );
			nsRemote.client = {};
			
			ns = new NetStream( nc );
			ns.addEventListener( NetStatusEvent.NET_STATUS, onStatusEvents );
			this.attachMedia();
		};
		
		protected function attachMedia():void
		{
			if( this._media == null || this._media.cam == null && this._media.mic == null ) {
				trace("Error:  No Media attached.");
				return;
			}
			var cam:Camera = this._media.cam;
			var mic:Microphone = this._media.mic;
			//tracer("attachMedia cam: "+cam+", mic: "+mic);
			if( mic != null ) ns.attachAudio( mic );
			if( cam != null ) ns.attachCamera( cam );
		};
		
		protected function onMediaChanged( p_e:Event ):void
		{
			var type:String = p_e.type;
			//tracer("onMediaChanged "+type);
			switch( type ){
				case MediaManager.CAM_CHANGE:
				case MediaManager.MIC_CHANGE:
					attachMedia();
					break;
			}
		}
		
		//PUBLIC
		
		public function connect( p_nc:NetConnection ):void
		{
			this.nc = p_nc;
			this.createSharedObject();
		}
		
		public function userData( p_o:Object ):void
		{
			//tracer("userData "+p_o.id);
			this._localUserId = p_o.id;
			streamName = this._localUserId;
		}
		
		public function startPublish():void
		{
			//tracer( "onStartPublish name: "+streamName+", connected: "+this.nc.connected+", publishing: "+this.isPublishing );
			if( this._uniqueID == null ) {
				trace("ERROR:  This SharedAVControl needs a uniqueIdentifier to connect.");
				return;
			}
			//reject if not connected, or is already publishing, or is already playing.
			if( !nc.connected || isPublishing || isPlaying ) return;
			if( ns == null ) {
				this.createStream();
			} else {
				this.attachMedia();
			}
			ns.publish( streamName );
			isPublishing = true;
		};
		
		public function stopPublish():void
		{
			if( !isPublishing ) return;
			//tracer( "onStopPublish "+streamName );
			ns.close();
			isPublishing = false;
		};
		
		public function set mediaManager( p_m:MediaManager ):void
		{
			//set the media manager that has the camera and mic data.
			_media = p_m;
			this._media.addEventListener(MediaManager.CAM_CHANGE, onMediaChanged );
			this._media.addEventListener(MediaManager.MIC_CHANGE, onMediaChanged );
			
			if( this.ns != null ) this.attachMedia();
		};
		
		public function get mediaManager():MediaManager
		{
			return _media;
		}
		
		public function set uniqueIdentifier( p_s:String ):void
		{
			this._uniqueID = p_s;
			if( this.nc.connected ){
				this.createSharedObject();
			}
		}
		public function get uniqueIdentifier():String
		{
			return this._uniqueID;
		}
		
		private function tracer( p_msg:* ):void
		{
			//trace("#SharedAVControl#  "+p_msg);
		}
	}
}