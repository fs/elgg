/**
* Player that reads all media formats Flash can read.
**/


import com.elggmediaplayer.players.*;
import com.elggmediaplayer.utils.BandwidthCheck;


class com.elggmediaplayer.players.MediaPlayer extends AbstractPlayer {


	/** Array with all config values **/
	private var config:Object = {
		clip:undefined,
		controlbar:20,
		height:undefined,
		width:undefined,
		file:undefined,
		displayheight:undefined,
		frontcolor:0xffffff,
		backcolor:undefined,
		lightcolor:0x4690D6,
		progressbar:0x333333,
		progressbarloaded:0x0054a7,
		scrubicncolor:0xffffff,
		scrubicncolorover:0xFF0000,
		screencolor:0x000000,
		autoscroll:"false",
		displaywidth:undefined,
		largecontrols:"false",
		logo:undefined,
		showdigits:"total",
		showdownload:"true",
		showeq:"false",
		showicons:"true",
		showvolume:"true",
		thumbsinplaylist:"false",
		usefullscreen:"true",
		fsbuttonlink:undefined,
		autostart:"false",
		bufferlength:3,
		overstretch:"true",
		repeat:"false",
		rotatetime:10,
		shuffle:undefined,
		smoothing:"true",
		volume:65,
		bwfile:"100k.jpg",
		bwstreams:undefined,
		callback:undefined,
		enablejs:"false",
		javascriptid:"",
		linkfromdisplay:"false",
		linktarget:undefined,
		streamscript:undefined,
		useaudio:"true",
		usecaptions:"false",
		usekeys:"false",
		version:undefined
	};


	/** Constructor **/
	public function MediaPlayer(tgt:MovieClip) {
		super(tgt);
	};


	/** Some player-specific config settings **/
	private function loadConfig(tgt:MovieClip) {
		for(var cfv in config) {
			if(_root[cfv] != undefined) {
				config[cfv] = unescape(_root[cfv]);
			}
		}
		config['largecontrols'] == "true" ? config["controlbar"] *= 2: null;
		if (config["displayheight"] == undefined) {
			config["displayheight"] = config["height"] - config['controlbar'];
		} else if(Number(config["displayheight"])>Number(config["height"])) {
			config["displayheight"] = config["height"];
		}
		if (config["displaywidth"] == undefined) {
			config["displaywidth"] = config["width"];
		}
		config["bwstreams"] == undefined ? loadFile(): checkStream();
	};


	/** check bandwidth for streaming **/
	private function checkStream() {
		var ref = this;
		var str = config["bwstreams"].split(",");
		var bwc = new BandwidthCheck(config["bwfile"]);
		bwc.onComplete = function(kbps) {
			trace("bandwidth: "+kbps);
			var bwc = new ContextMenuItem("Detected bandwidth: "+kbps+" kbps",
				_root.goTo);
			bwc.separatorBefore = true;
			_root.mnu.customItems.push(bwc);
			if(ref.config['enablejs'] == "true" && 
				flash.external.ExternalInterface.available) {
				flash.external.ExternalInterface.call("getBandwidth",kbps);
			}
			for (var i=1; i<str.length; i++) {
				if (kbps < Number(str[i])) {
					ref.loadFile(str[i-1]);
					return;
				}
			}
			ref.loadFile(str[str.length-1]);
		};
	};


	/** Setup all necessary MCV blocks. **/
	private function setupMCV() {
		// set controller
		controller = new PlayerController(config,feeder);
		// set default views
		var dpv = new DisplayView(controller,config,feeder);
		var cbv = new ControlbarView(controller,config,feeder);
		var vws:Array = new Array(dpv,cbv);
		// set optional views
		if(config["displayheight"] < config["height"]-config['controlbar'] ||
			config["displaywidth"] < config["width"]) {
		} else {
			config["clip"].playlist._visible = 
				config["clip"].playlistmask._visible  = false;
		}
		if(config["usekeys"] == "true") {
			var ipv = new InputView(controller,config,feeder);
			vws.push(ipv);
		}
		if(config["enablejs"] == "true") {
			var jsv = new JavascriptView(controller,config,feeder);
			vws.push(jsv);
		}
		if(config["callback"] != undefined) {
			var cav = new CallbackView(controller,config,feeder);
			vws.push(cav);
		}
		var flv = new FLVModel(vws,controller,config,feeder,
			config["clip"].display.video);
		var img = new ImageModel(vws,controller,config,feeder,
			config["clip"].display.image);
		var mds:Array = new Array(flv);

		controller.startMCV(mds);
	};


}