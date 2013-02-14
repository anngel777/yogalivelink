package com.influxis.events
{
	import flash.events.Event;

	public class StreamEvent extends Event
	{
		public var streamName:String;
		
		public function StreamEvent(type:String, p_streamName:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
			this.streamName = p_streamName;
		}
		
		public override function clone():Event
		{
			return new StreamEvent( type, streamName, bubbles, cancelable );
		};
		
		override public function toString():String
		{
			return formatToString( "StreamEvent", "type", "streamName", "bubbles", "cancelable" );
		};
	}
}