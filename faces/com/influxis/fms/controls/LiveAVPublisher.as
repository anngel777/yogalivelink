/**
 * Copyright © 2010 Influxis All rights reserved.
 **/
package com.influxis.fms.controls
{
	import com.influxis.controls.MediaManager;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.NetStatusEvent;
	import flash.media.Camera;
	import flash.media.Microphone;
	import flash.net.NetConnection;
	import flash.net.NetStream;
	import flash.net.Responder;
	
	public class LiveAVPublisher extends EventDispatcher
	{
		public var nc:NetConnection;
		public var ns:NetStream;
		public var streamName:String;
		public var isPublishing:Boolean = 			false;
		public var localUserID:String;
		
		public var useH264:Boolean = true;
		
		protected var _media:MediaManager;
		
		public function LiveAVPublisher()
		{
		}
		
		protected function onStatusEvents( p_e:NetStatusEvent ):void
		{
			var code:String = p_e.info.code;
			//tracer("onStatusEvent.code "+code);
		}
		
		//creates stream for publishing
		protected function createStream():void
		{
			ns = new NetStream( nc );
			ns.addEventListener( NetStatusEvent.NET_STATUS, onStatusEvents );
			this.attachMedia();
		};
		
		//attches cam/mic for publishing media in stream.
		protected function attachMedia():void
		{
			//if there is no media or if the cam AND the mic are null, reuturn false.
			if( this._media == null || this._media.cam == null && this._media.mic == null ) {
				trace("Error:  No Media attached.");
				return;
			}
			var cam:Camera = this._media.cam;
			var mic:Microphone = this._media.mic;
			//tracer("attachMedia cam: "+cam+", mic: "+mic);
			if( mic != null && ns != null ) ns.attachAudio( mic );
			if( cam != null && ns != null ) ns.attachCamera( cam );
		}
		
		//handles when camera and mic are updated. so we can update stream.
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
		
		protected function doPublish( p_name:String ):void
		{
			if( ns == null ) {
				createStream();
			} else {
				this.attachMedia();
			}
			//tracer("doPublish "+p_name);
			if( isPublishing || !nc.connected ) return;
			this.streamName = useH264 ? "mp4:"+p_name+".f4v" : p_name;
			ns.publish( streamName );
			isPublishing = true;
		}
		
		//if we can stream, an object with the reserved stream name is sent.
		protected function onCanStream( p_o:Object ):void
		{
			var status:Boolean = p_o.status as Boolean;
			//tracer("onCanStream "+status);
			if( status ) {
				doPublish( p_o.name as String );
			} else {
				//dispatch no stream available.
			}
		};
		protected function onCanStreamFail( p_o:Object ):void
		{
			tracer("onCanStreamFail "+p_o);
		}
		
		//PUBLIC
		//handles connection link to service.
		public function connect( p_nc:NetConnection ):void
		{
			this.nc = p_nc;
		}
		
		public function close():void
		{
			
		}
		
		//handles local user data so we can stream a unique stream.
		public function userData( p_o:Object ):void
		{
			//tracer("userData "+p_o.id);
			this.localUserID = p_o.id;
		}
		
		//control to start pushing live stream to server.
		public function startPublish():void
		{
			//tracer( "onStartPublish connected: "+this.nc.connected+", publishing: "+this.isPublishing );
			if( isPublishing || !nc.connected ) return;
			this.nc.call( "canStream", new Responder( onCanStream, onCanStreamFail ) );
		};
		
		
		//control to stop publishing to server.
		public function stopPublish():void
		{
			if( !isPublishing ) return;
			//tracer( "onStopPublish "+streamName );
			isPublishing = false;
			if( ns != null ){
				ns.close();
			}
		};
		
		//saves media manger to listen to changes in camera and mic selections.
		public function set mediaManager( p_m:MediaManager ):void
		{
			if( _media != null ){
				this._media.addEventListener(MediaManager.CAM_CHANGE, onMediaChanged );
				this._media.addEventListener(MediaManager.MIC_CHANGE, onMediaChanged );
				_media = null;
			}
			//set the media manager that has the camera and mic data.
			_media = p_m;
			this._media.addEventListener(MediaManager.CAM_CHANGE, onMediaChanged, false, 0, true );
			this._media.addEventListener(MediaManager.MIC_CHANGE, onMediaChanged, false, 0, true );
			
			if( this.ns != null ) this.attachMedia();
		};
		
		public function get mediaManager():MediaManager
		{
			return _media;
		}
		
		private function tracer( p_msg:* ):void
		{
			//trace("#LiveAVPublisher#  "+p_msg);
		}
	}
}