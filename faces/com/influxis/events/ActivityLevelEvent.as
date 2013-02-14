﻿package com.influxis.events 
{
	import flash.events.Event;
	
		public var activityLevel:Number;
		public var source:*;
			super(p_type, p_bubbles, p_cancelable);
			this.activityLevel = p_activityLevel;
			this.source = p_source;
		public override function clone():Event
		{
			return new ActivityLevelEvent( type, activityLevel, source, bubbles, cancelable );
		};
		
		override public function toString():String
		{
			return formatToString( "ActivityLevelEvent", "type", "activityLevel", "source", "bubbles", "cancelable" );
		};