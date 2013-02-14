/**
 * Copyright © 2010 Influxis All rights reserved.
 **/
package com.influxis.views
{
	import com.influxis.controls.MediaManager;
	import com.influxis.events.ActivityLevelEvent;
	
	import fl.controls.Button;
	import fl.controls.ComboBox;
	import fl.controls.RadioButton;
	import fl.controls.RadioButtonGroup;
	import fl.controls.Slider;
	import fl.data.DataProvider;
	import fl.events.SliderEvent;
	
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.text.TextField;
	
	public class MediaPresetView extends MovieClip
	{
		public static const CUSTOM_PRESSED:String = 		"customPressed";
		public static const ON_CLOSE:String = 				"onClose";
		
		protected var _media:MediaManager;
		protected var _rbGroup:RadioButtonGroup;
		
		//UI
		public var camsList:ComboBox;
		public var micList:ComboBox;
		public var volumeMeter:MovieClip;
		public var gainSlider:Slider;
		public var infoText:TextField;
		public var volText:TextField;
		public var low:RadioButton;
		public var medium:RadioButton;
		public var high:RadioButton;
		public var customButton:Button;
		public var closeButton:Button;
		
		public var isReady:Boolean = 					false;
		//cannot deslect radio buttons, override with dummy rb
		protected var _dummyRB:RadioButton;
		
		public var presetSelections:Object = {
			low:{width:320,height:240,fps:10,quality:80,rate:8},
			medium:{width:320,height:240,fps:20,quality:90,rate:11, selected:true},
			high:{width:640,height:480,fps:15,quality:80,rate:22}
		}
		
		public function MediaPresetView()
		{
		}
		
		public function init():void
		{
			camsList.addEventListener( Event.CHANGE, onMediaListUpdate );
			micList.addEventListener( Event.CHANGE, onMediaListUpdate );
			
			gainSlider.liveDragging = true;
			gainSlider.addEventListener( SliderEvent.CHANGE, onSliderChange );
			gainSlider.addEventListener( MouseEvent.CLICK, onSliderClick );
			
			low.addEventListener(MouseEvent.CLICK, onRadioButtonEvent);
			medium.addEventListener(MouseEvent.CLICK, onRadioButtonEvent);
			high.addEventListener(MouseEvent.CLICK, onRadioButtonEvent);
			_rbGroup = low.group;
			if( _rbGroup == null ){
				_rbGroup = new RadioButtonGroup("PresetGroup");
			}
			
			//set dummy radio button. do not display.
			_dummyRB = new RadioButton();
			_dummyRB.name = "dummy";
			_dummyRB.group = _rbGroup;
			
			customButton.addEventListener(MouseEvent.CLICK, onCustom );
			closeButton.addEventListener(MouseEvent.CLICK, onClose );
			
			this.updateControls();
			isReady = true;
		}
		
		protected function onRadioButtonEvent( p_e:MouseEvent ):void
		{
			//tracer("onRadioButtonEvent "+p_e);
			var rad:RadioButton = p_e.target as RadioButton;
			if( rad == null ) return;
			var data:Object = this.presetSelections[rad.name] as Object;
			if( data == null) {
				trace("ERROR: No data for this radio button.");
				return;
			}
			var name:String = rad.name;
			this.infoText.text = name;
			this._media.setCamSizeProps(data.width,data.height,data.fps);
			this._media.setCamQuality(data.quality,0);
			this._media.micRate = data.rate;
		}
		
		protected function onCustom( p_e:MouseEvent ):void
		{
			this.infoText.text = "Custom";
			this._rbGroup.selection = this._dummyRB;
			this.dispatchEvent( new Event( MediaPresetView.CUSTOM_PRESSED ) );
		}
		
		protected function onClose( p_e:MouseEvent ):void
		{
			this.dispatchEvent( new Event( MediaPresetView.ON_CLOSE ) );
		}
		
		protected function onMediaUpdate( p_e:Event ):void
		{
			var type:String = p_e.type;
			//tracer("onMediaUpdate "+type);
			switch( type ){
				case MediaManager.MIC_CHANGE:
					//tracer("mic "+_media.mic);
					if( micList.selectedIndex != _media.selectedMicIndex ) micList.selectedIndex = _media.selectedMicIndex;
					if( this._media.isReady ) updateControls();//update only after we are setup.
					break;
				case MediaManager.CAM_CHANGE:
					//tracer("cam "+this._media.selectedCamIndex);
					if( this.camsList.selectedIndex != this._media.selectedCamIndex ) this.camsList.selectedIndex = this._media.selectedCamIndex;
					if( this._media.isReady ) updateControls();//update only after we are setup.
					break;
				case MediaManager.READY:
					//CAM
					var a:Array = _media.camList;
					if( a.length > 0 ){
						var dp:DataProvider = new DataProvider(a);
						camsList.dataProvider = dp;
					}
					
					//MIC
					a = _media.micList;
					if( a.length > 0 ){
						dp = new DataProvider(a);
						micList.dataProvider = dp;
					}
					
					//set the default selected quality settings.
					var rb:RadioButton;
					var item:Object;
					for( var i:String in this.presetSelections ){
						item = this.presetSelections[i];
						if( item.selected ){
							try{
								rb = this[i] as RadioButton;
								rb.selected = true;
							}catch(e:Error){};
							
							break;
						}
					}
					//if there is something selected, we need to set the default to what is selected.
					if( rb != null ){
						this._media.setCamSizeProps(item.width,item.height,item.fps);
						this._media.setCamQuality(item.quality,0);
						this._media.micRate = item.rate;
					}
					
					if( isReady ) {
						updateControls();
					}
					
					break;
			}
		};
		
		//update the sliders, buttons, text fields with the correct cam and mic settings.
		protected function updateControls():void
		{
			if( this._media == null ) return;
			//tracer("updateControls "+this._media.cam+"  "+this._media.mic);
			
			//mic:
			if( micList.selectedIndex != _media.selectedMicIndex ) micList.selectedIndex = _media.selectedMicIndex;
			gainSlider.value = this._media.micGain;
			this.volText.text = this._media.micGain.toString();
			if( !gainSlider.enabled ) gainSlider.enabled = true;
			
			//cam:
			if( this.camsList.selectedIndex != this._media.selectedCamIndex ) this.camsList.selectedIndex = this._media.selectedCamIndex;
			
		}
		
		//clear radio buttons user used costom settings instead of presets.
		public function showAsCustom():void
		{
			//workaround - fl radio button group controls will not clear selected, so we select a dummy control to clear the real ones.
			var rbSel:RadioButton = this._rbGroup.selection;
			if( rbSel != null ){
				this._rbGroup.selection = this._dummyRB;
				_dummyRB.selected = true;
			}
		}
		
		protected function onMicVolume( p_e:ActivityLevelEvent ):void
		{
			var level:Number = p_e.activityLevel;
			//tracer("onMicVolume "+level);
			this.volumeMeter.scaleY = level;
		}
		
		protected function onSliderChange( p_e:SliderEvent ):void
		{
			//tracer("onSliderChange "+p_e);
			var sl:Slider = p_e.target as Slider;
			var val:String = p_e.value.toString();
			switch( sl ){
				case this.gainSlider:
					this.volText.text = val;
					break;
			}
			
		}
		
		protected function onSliderClick( p_e:MouseEvent ):void
		{
			//tracer("onSliderClick "+p_e);
			var sl:Slider = p_e.currentTarget as Slider;
			var val:int = sl.value;
			switch( sl ){
				case this.gainSlider:
					this._media.micGain = val;
					this.volText.text = val.toString();
					break;
			}
			
		}
		
		protected function onMediaListUpdate( p_e:Event ):void
		{
			var target:ComboBox = p_e.target as ComboBox;
			//tracer("onMediaListUpdate "+target);
			switch( target ) {
				case this.camsList:
					_media.selectedCamIndex = this.camsList.selectedIndex;
					break;
				case this.micList:
					_media.selectedMicIndex = this.micList.selectedIndex;
					break;
			}
		}
		
		public function set mediaManager( p_m:MediaManager ):void
		{
			//set the media manager that has the camera and mic data.
			_media = p_m;
			_media.addEventListener( MediaManager.MIC_CHANGE, onMediaUpdate );
			_media.addEventListener( MediaManager.CAM_CHANGE, onMediaUpdate );
			_media.addEventListener( MediaManager.READY, onMediaUpdate );
			_media.addEventListener( MediaManager.MIC_VOLUME, onMicVolume );
			
			//dispaches mic activity events
			_media.checkMicVolumeActivity = true;
		};
		
		public function get mediaManager():MediaManager
		{
			return _media;
		}
		
		private function tracer( p_msg:* ):void
		{
			//trace( "#MediaPresetView#  "+p_msg );
		}
	}
}
	