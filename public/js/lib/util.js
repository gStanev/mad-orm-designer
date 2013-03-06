/**
 * 
 */

var Util = {};

Util.Router = function(routers,path){
	this.routers = routers;
	this.refactoredPath = path.split("-").join("_");
};

$.extend(Util.Router.prototype, {
	handle: function() {
	// Call dispatch method for rendering and delegate
		this.dispatch();
	},
	dispatch: function(){
	// Check to see if our Util has a method like the searched one and call it if it does
		if(this.routers.hasOwnProperty("init")){
			this.routers.init();
		}
		if(this.routers.hasOwnProperty(this.refactoredPath)){
			this.routers[this.refactoredPath]();
		}
	}
});
