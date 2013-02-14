/**
 * Copyright © 2010 Influxis All rights reserved.
 **/
package com.influxis.views
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.text.TextField;

	public class MenuView extends MovieClip
	{
		//events
		public static const ON_FULLSCREEN:String = 	"onFullScreen";
		public static const ON_PUBLISH:String =		"onPublish";
		public static const ON_SETTINGS:String = 	"onSettings";
		public static const ON_MUTED:String =		"onMuted";
		
		//states
		public static const STREAM_AVAILABLE:String = 	"streamAvailable";
		public static const STREAM_IN_USE:String = 		"streamInUse";
		public static const STREAM_LOCAL_USER:String = 	"streamLocalUser";
		
		protected var _selected:MovieClip;
		protected var _enabled:Boolean = true;
		protected var _width:Number;
		protected var _muted:Boolean = false;
		
		//UI
		public var menuBar:MovieClip;
		public var usersText:TextField;
		public var mediaButton:MovieClip;
		public var streamButton:MovieClip;
		public var fullscreenButton:MovieClip;
		public var muteButton:MovieClip;
		
		
		public function MenuView()
		{
			_width = this.width;
			
			mediaButton.useHandCursor = true;
			mediaButton.buttonMode = true;
			mediaButton.mouseChildren = false;
			mediaButton.addEventListener( MouseEvent.CLICK, itemClick );
			
			streamButton.useHandCursor = true;
			streamButton.buttonMode = true;
			streamButton.mouseChildren = false;
			streamButton.addEventListener( MouseEvent.CLICK, itemClick );
			
			fullscreenButton.useHandCursor = true;
			fullscreenButton.buttonMode = true;
			fullscreenButton.mouseChildren = false;
			fullscreenButton.addEventListener( MouseEvent.CLICK, itemClick );
			
			muteButton.useHandCursor = true;
			muteButton.buttonMode = true;
			muteButton.mouseChildren = false;
			muteButton.addEventListener( MouseEvent.CLICK, itemClick );
			
			this.resize();
		}
		
		protected function itemClick( p_e:MouseEvent ):void
		{
			if( !this.enabled ) return;
			var target:MovieClip = p_e.target as MovieClip;
			//tracer("itemClick "+p_e.target.name);
			switch( target ) {
				case this.mediaButton :
					this.dispatchEvent( new Event( MenuView.ON_SETTINGS ) );
					break;
				case this.streamButton:
					this.dispatchEvent( new Event( MenuView.ON_PUBLISH ) );
					break;
				case this.fullscreenButton:
					this.dispatchEvent( new Event( MenuView.ON_FULLSCREEN ) );
					break;
				case muteButton:
					this.dispatchEvent( new Event( MenuView.ON_MUTED ) );
					break;
			}
		}
		
		//PUBLIC
		public function resize():void
		{
			var w:int = this._width;
			menuBar.width = w;
			this.fullscreenButton.x = w - (this.fullscreenButton.width+10);
			streamButton.x = Math.round( (w - streamButton.width)/2 );
			muteButton.x = Math.round( fullscreenButton.x - ( muteButton.width+20 ) );
		}
		
		//stream
		public function set streamStatus( p_s:String ):void
		{
			//tracer("streamStatus "+p_s);
			//changes the stream button state based on what is happening
			switch( p_s ){
				case MenuView.STREAM_AVAILABLE:
					streamButton.gotoAndStop( "startstream" );
					break;
				case MenuView.STREAM_IN_USE:
					streamButton.gotoAndStop( "nostream" );
					break;
				case MenuView.STREAM_LOCAL_USER:
					streamButton.gotoAndStop( "stopstream" );
					break;
			}
		}
		public function get streamStatus():String
		{
			return "";
		}
		
		public function set isMuted( p_b:Boolean ):void
		{
			_muted = p_b;
			this.muteButton.icon.gotoAndStop( p_b ? "off" : "on" );
		}
		
		public function get isMuted():Boolean
		{
			return _muted;
		}
		
		public function set userCount( p_n:int ):void
		{
			usersText.text = p_n.toString();
		}
		
		public function get userCount():int
		{
			return Number(usersText.text);
		}
		
		public override function set enabled( p_b:Boolean ):void
		{
			_enabled = p_b;
			super.enabled = p_b;
		}
		
		public override function get enabled():Boolean
		{
			return super.enabled;
		}
		
		override public function set height(p_n:Number):void
		{
			return;
		}
		
		override public function get height():Number
		{
			return this.menuBar.height;
		}
		
		override public function set width(p_n:Number):void
		{
			if( p_n == this._width ) return;
			this._width = p_n;
			this.resize();
		}
		
		override public function get width():Number
		{
			return menuBar.width;
		}
		
		private function tracer( p_msg:* ):void
		{
			//trace( "#MenuView#  "+p_msg );
		}
		

	}
}