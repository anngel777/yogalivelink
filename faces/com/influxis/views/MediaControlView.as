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
	
	public class MediaControlView extends MovieClip
	{
		public static const ON_PUBLISH_START:String = 		"onPublishStart";
		public static const ON_PUBLISH_STOP:String = 		"onPublishStop";
		public static const PRESET_PRESSED:String = 		"presetPressed";
		public static const ON_CLOSE:String = 				"onClose";
		
		protected var _media:MediaManager;
		protected var _isPublish:Boolean = 				false;
		protected var _rbGroup:RadioButtonGroup;
		protected var _rbgMic:RadioButtonGroup;
		
		//cannot deslect radio buttons, override with dummy rb
		protected var _dummyRB:RadioButton;
		
		public var camSizeSelections:Object = {
			r0:{width:256,height:192},
			r1:{width:320,height:240},
			r2:{width:448,height:336},
			r3:{width:640,height:480}
		}
		public var micRateSelections:Object = {
			m0:5,
			m1:8,
			m2:11,
			m3:22,
			m4:44
		}
		public var isReady:Boolean = 					false;
		
		//UI
		public var camsList:ComboBox;
		public var micList:ComboBox;
		public var volumeMeter:MovieClip;
		public var gainSlider:Slider;
		public var qualitySlider:Slider;
		public var fpsSlider:Slider;
		public var fpsText:TextField;
		public var qualityText:TextField;
		public var sizeText:TextField;
		public var volText:TextField;
		public var rateText:TextField;
		public var presetButton:Button;
		public var closeButton:Button;
		
		
		public function MediaControlView()
		{
			
		};
		
		public function init():void
		{
			camsList.addEventListener( Event.CHANGE, onMediaListUpdate );
			micList.addEventListener( Event.CHANGE, onMediaListUpdate );
			
			gainSlider.liveDragging = true;
			qualitySlider.liveDragging = true;
			fpsSlider.liveDragging = true;
			gainSlider.addEventListener( SliderEvent.CHANGE, onSliderChange );
			qualitySlider.addEventListener( SliderEvent.CHANGE, onSliderChange );
			fpsSlider.addEventListener( SliderEvent.CHANGE, onSliderChange );
			gainSlider.addEventListener( MouseEvent.CLICK, onSliderClick );
			qualitySlider.addEventListener( MouseEvent.CLICK, onSliderClick );
			fpsSlider.addEventListener( MouseEvent.CLICK, onSliderClick );
			
			volumeMeter.scaleY = 0.01;
			
			// set microphone radio buttons.
			var i:int=0;
			while( i<10 ){
				try{
					var rb:RadioButton = this["m"+i] as RadioButton;
					rb.addEventListener(MouseEvent.CLICK, onMicRadioButtonEvent);
					this._rbgMic = rb.group;
				}
				catch(e:Error){ i=55; }
				i++;
			}
			if( _rbgMic == null ){
				_rbgMic = new RadioButtonGroup("micRadioButtonGroup");
			}
			
			//dynamically set radio buttons for camera settings.
			i=0;
			while( i<10 ){
				try{ 
					var item:RadioButton = this["r"+i] as RadioButton;
					item.addEventListener(MouseEvent.CLICK, onRadioButtonEvent);
					if( _rbGroup == null ){
						if( item.group != null ) _rbGroup = item.group;
					}
				} 
				catch(e:Error){ i=55; }
				i++;
			}
			if( _rbGroup == null ){
				_rbGroup = new RadioButtonGroup("RadioButtonGroup");
			}
			
			//set dummy radio button. do not display.
			_dummyRB = new RadioButton();
			_dummyRB.name = "dummy";
			_dummyRB.group = _rbGroup;
			
			presetButton.addEventListener(MouseEvent.CLICK, onPresets );
			closeButton.addEventListener(MouseEvent.CLICK, onClose );
			
			this.updateControls();
			isReady = true;
		}
		
		protected function onRadioButtonEvent( p_e:MouseEvent ):void
		{
			//tracer("onRadioButtonEvent "+p_e);
			var rad:RadioButton = p_e.target as RadioButton;
			if( rad == null ) return;
			var data:Object = this.camSizeSelections[rad.name] as Object;
			if( data == null) {
				trace("ERROR: No data for this radio button.");
				return;
			}
			this.sizeText.text = data.width+" x "+data.height;
			this._media.setCamSizeProps( data.width, data.height );
		}
		
		protected function onMicRadioButtonEvent( p_e:MouseEvent ):void
		{
			//tracer("onMicRadioButtonEvent "+p_e);
			var rad:RadioButton = p_e.target as RadioButton;
			if( rad == null ) return;
			var data:int = this.micRateSelections[rad.name] as int;
			if( isNaN(data) || data == 0 ) {
				trace("ERROR: No data for this radio button.");
				return;
			}
			this.rateText.text = data+" khz";
			this._media.micRate = data;
		}
		
		protected function onMediaUpdate( p_e:Event ):void
		{
			var type:String = p_e.type;
			tracer("onMediaUpdate "+type);
			switch( type ){
				case MediaManager.MIC_CHANGE:
					tracer("mic "+_media.mic);
					if( micList.selectedIndex != _media.selectedMicIndex ) micList.selectedIndex = _media.selectedMicIndex;
					if( this._media.isReady ) updateControls();//update only after we are setup.
					break;
				case MediaManager.CAM_CHANGE:
					tracer("cam "+this._media.selectedCamIndex);
					if( this.camsList.selectedIndex != this._media.selectedCamIndex ) this.camsList.selectedIndex = this._media.selectedCamIndex;
					if( this._media.isReady ) updateControls();//update only after we are setup.
					break;
				case MediaManager.MIC_GAIN_CHANGE:
					var gain:Number = this._media.micGain;
					if( this.gainSlider.value != gain ) {
						gainSlider.value = gain;
						volText.text = gain.toString();
					}
					break;
				case MediaManager.MIC_RATE_CHANGE:
					if( this.isReady ){
						var sel:RadioButton = this._rbgMic.selection as RadioButton;
							tracer("s s  "+sel);
						if( sel != null ){
							var val:Number = this.micRateSelections[sel.name];
							if( val != this._media.micRate && this._media.isReady ) {
								tracer("Update Rate value.");
								updateControls();//update only after we are setup.
							}
						}
					}
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
					
					if( isReady ) updateControls();
					
					break;
			}
		};
		
		//update the sliders, buttons, text fields with the correct cam and mic settings.
		protected function updateControls():void
		{
			if( this._media == null ) return;
			tracer("updateControls "+this._media.cam+"  "+this._media.mic);
			
			//mic:
			if( micList.selectedIndex != _media.selectedMicIndex ) micList.selectedIndex = _media.selectedMicIndex;
			tracer("gain "+this._media.micGain);
			gainSlider.value = this._media.micGain;
			this.volText.text = this._media.micGain.toString();
			if( !gainSlider.enabled ) gainSlider.enabled = true;
			var rate:Number = this._media.micRate;
			var rb:RadioButton;
			for( var r:String in this.micRateSelections ){
				tracer(r+": match rate: "+rate);
				var val:Number = this.micRateSelections[r];
				if( val == rate ){
					try{ 
						rb = this[r] as RadioButton;
						rb.selected = true;
						rateText.text = rate+" khz";
						break;
					} 
					catch(e:Error){ trace("ERROR: Could not find "+r+" radio button !"); }
				}
			}
			
			//cam:
			if( this.camsList.selectedIndex != this._media.selectedCamIndex ) this.camsList.selectedIndex = this._media.selectedCamIndex;
			
			var w:int = this._media.camWidth;
			var h:int = this._media.camHeight;
			//set radio button based on width and height selected.
			if( w > 0 && h > 0 ){
				for( var i:String in this.camSizeSelections ){
					var item:Object = this.camSizeSelections[i] as Object;
					tracer(i+": "+item.width+" x "+item.height+" - match size: "+w+" x "+h);
					if( item.width == w && item.height == h ){
						try{ 
							rb = this[i] as RadioButton;
							rb.selected = true;
							tracer("found "+rb.name);
						} 
						catch(e:Error){ trace("ERROR: Could not find "+i+" !"); }
						
						break;
					}
				}
				this.sizeText.text = w+" x "+h;
				//clear radio buttons if there is no matching selection.
				if( rb == null && this._rbGroup != null ) {
					//workaround - fl radio button group controls will not clear selected, so we select a dummy control to clear the real ones.
					var rbSel:RadioButton = this._rbGroup.selection;
					if( rbSel != null ){
						this._rbGroup.selection = this._dummyRB;
						_dummyRB.selected = true;
					}
				}
				
			}
			
			
			
			//quality
			var camQual:int = _media.camQuality;
			qualitySlider.value = camQual;
			this.qualityText.text = camQual.toString();
			if( !qualitySlider.enabled ) qualitySlider.enabled = true;
			
			//Frames per second
			var camFPS:int = _media.camFps;
			fpsSlider.value = camFPS;
			fpsText.text = camFPS.toString();
			if( !fpsSlider.enabled ) fpsSlider.enabled = true;
		}
		
		protected function onMicVolume( p_e:ActivityLevelEvent ):void
		{
			var level:Number = p_e.activityLevel;
			//tracer("onMicVolume "+level);
			this.volumeMeter.scaleY = level;
		}
		
		protected function onSliderChange( p_e:SliderEvent ):void
		{
			tracer("onSliderChange "+p_e);
			var sl:Slider = p_e.target as Slider;
			var val:String = p_e.value.toString();
			switch( sl ){
				case this.gainSlider:
					this.volText.text = val;
					break;
				case this.qualitySlider:
					this.qualityText.text = val;
					break;
				case this.fpsSlider:
					fpsText.text = val;
					break;
			}
			
		}
		
		protected function onSliderClick( p_e:MouseEvent ):void
		{
			tracer("onSliderClick "+p_e);
			var sl:Slider = p_e.currentTarget as Slider;
			var val:int = sl.value;
			switch( sl ){
				case this.gainSlider:
					this._media.micGain = val;
					this.volText.text = val.toString();
					break;
				case this.qualitySlider:
					this._media.setCamQuality( val, 0 );
					this.qualityText.text = val.toString();
					break;
				case this.fpsSlider:
					this._media.camFps = val;
					fpsText.text = val.toString();
					break;
			}
			
		}
		
		protected function onMediaListUpdate( p_e:Event ):void
		{
			var target:ComboBox = p_e.target as ComboBox;
			tracer("onMediaListUpdate "+target);
			switch( target ) {
				case this.camsList:
					_media.selectedCamIndex = this.camsList.selectedIndex;
					break;
				case this.micList:
					_media.selectedMicIndex = this.micList.selectedIndex;
					break;
			}
		}
		
		protected function onPresets( p_e:MouseEvent ):void
		{
			this.dispatchEvent( new Event(MediaControlView.PRESET_PRESSED) );
		}
		
		protected function onClose( p_e:MouseEvent ):void
		{
			this.dispatchEvent( new Event( MediaControlView.ON_CLOSE ) );
		}
		
		public function set mediaManager( p_m:MediaManager ):void
		{
			_media = p_m;
			_media.addEventListener( MediaManager.MIC_CHANGE, onMediaUpdate );
			_media.addEventListener( MediaManager.CAM_CHANGE, onMediaUpdate );
			_media.addEventListener( MediaManager.MIC_RATE_CHANGE, onMediaUpdate );
			_media.addEventListener( MediaManager.MIC_GAIN_CHANGE, onMediaUpdate );
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
			//trace( "#MediaControlView#  "+p_msg );
		}
	}
}