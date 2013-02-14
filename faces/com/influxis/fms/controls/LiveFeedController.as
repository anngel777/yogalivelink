/**
 * Copyright © 2010 Influxis All rights reserved.
 **/
package com.influxis.fms.controls
{
	import com.influxis.events.StreamEvent;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.NetStatusEvent;
	import flash.events.SyncEvent;
	import flash.net.NetConnection;
	import flash.net.SharedObject;
	
	public class LiveFeedController extends EventDispatcher
	{
		public static const READY:String = 					"ready";
		public static const REMOTE_STREAM_PLAY:String = 	"remoteStreamPlay";
		public static const REMOTE_STREAM_STOP:String = 	"remoteStreamStop";
		public static const LOCAL_STREAM_PLAY:String = 		"localStreamPlay";
		public static const LOCAL_STREAM_STOP:String = 		"localStreamStop";
		public static const STREAMING_AVAILABLE:String = 	"streamingAvailable";
		
		public var nc:NetConnection;
		public var so:SharedObject;
		public var localUserID:String;
		public var isReady:Boolean = 				false;
		
		public function LiveFeedController()
		{
		};
		
		protected function onStatusEvents( p_e:NetStatusEvent ):void
		{
			var code:String = p_e.info.code;
			//tracer("onStatusEvent.code "+code);
		}
		
		protected function createSharedObject():void
		{
			//tracer("createSharedObject ");
			so = SharedObject.getRemote( "LiveFeeds", this.nc.uri, false );
			so.addEventListener(SyncEvent.SYNC, onSyncEvent );
			var o:Object = new Object();
			o.onPublish = function( p_o:Object ):void {
				onPublish( p_o );
			}
			o.onUnpublish = function( p_o:Object ):void {
				onUnpublish( p_o );
			}
			o.StreamSlotsOpen = function( p_b:Boolean ):void {
				StreamSlotsOpen( p_b );
			}
			so.client = o;
			so.connect( this.nc );
		}
		
		protected function onSyncEvent( p_e:SyncEvent ):void
		{
			//tracer("onSyncEvent "+p_e);
			//check for other stream that is streaming. use sync 1 time to get up to speed, then we can just rely on messages.
			for( var i:String in this.so.data ){
				var item:Object = this.so.data[i];
				if( item != null && item.id != localUserID ){
					onPublish(item);
				}
				isReady = true;
				so.removeEventListener(SyncEvent.SYNC,onSyncEvent);
				dispatchEvent( new Event( LiveFeedController.READY) );
			}
		}
		
		protected function onPublish( p_o:Object ):void 
		{
			//tracer("onPublish "+p_o.id+", "+localUserID);
			if( p_o.id == localUserID ) {
				//dispatch local stream playing
				dispatchEvent( new StreamEvent( LiveFeedController.LOCAL_STREAM_PLAY, p_o.name as String ) );
			} else {
				dispatchEvent( new StreamEvent( LiveFeedController.REMOTE_STREAM_PLAY, p_o.name as String ) );
			}
		}
		protected function onUnpublish( p_o:Object ):void 
		{
			//tracer("onUnpublish "+p_o.id+", "+localUserID);
			if( p_o.id == this.localUserID ) {
				//dispatch local stream stop
				dispatchEvent( new StreamEvent( LiveFeedController.LOCAL_STREAM_STOP, p_o.name as String ) );
			} else {
				dispatchEvent( new StreamEvent( LiveFeedController.REMOTE_STREAM_STOP, p_o.name as String ) );
			}
		}
		
		public function StreamSlotsOpen( p_b:Boolean ):void
		{
			this.dispatchEvent( new StreamEvent(LiveFeedController.STREAMING_AVAILABLE, "") );
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
			this.localUserID = p_o.id;
		}
		
		private function tracer( p_msg:* ):void
		{
			//trace("#LiveFeedController#  "+p_msg);
		}
	}
}