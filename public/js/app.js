(function() {
	window.GraphData = {};
	var fancyBoxSettings = {
		hideOnOverlayClick: false
	};
	
	Renderer = function(canvas) {
		
		canvas = $(canvas).get(0);
		var ctx = canvas.getContext("2d");
		var particleSystem = null;

		var that = {
			highLightByType: 'all',
			init : function(system) {
				particleSystem = system;
				particleSystem.screen({
					padding : [ 100, 330, 100, 100 ], // leave some space at
														// the bottom for the
														// param sliders
					step : .02
				}); // have the ‘camera’ zoom somewhat slowly as the graph
					// unfolds
				$(window).resize(that.resize);
				that.resize();

				that.initMouseHandling();
			},
			redraw : function(highLightByType) {
				
				var self = this;
				if(typeof highLightByType !== 'undefined') {
					self.highLightByType = highLightByType;
				}
				
				if (particleSystem === null)
					return					
				

				ctx.clearRect(0, 0, canvas.width, canvas.height);
				ctx.strokeStyle = "#d3d3d3";
				ctx.lineWidth = 1;
				ctx.beginPath();
				particleSystem
						.eachEdge(function(edge, pt1, pt2) {
							// edge: {source:Node, target:Node, length:#,
							// data:{}}
							// pt1: {x:#, y:#} source position in screen coords
							// pt2: {x:#, y:#} target position in screen coords

							var weight = edge.data.weight || 1;
							var color = edge.data.color || 'silver'; // 
							// if (!color || (""+color).match(/^[ \t]*$/)) color
							// = null;

							if (color !== undefined || weight !== undefined) {
								ctx.save();
								ctx.beginPath();

								if (!isNaN(weight))
									ctx.lineWidth = weight;

								if (edge.source.data.region == edge.target.data.region) {
									// ctx.strokeStyle =
									// palette[edge.source.data.region];
								}

								// if (color) ctx.strokeStyle = color
								// ctx.fillStyle = null;

								ctx.moveTo(pt1.x, pt1.y);
								ctx.lineTo(pt2.x, pt2.y);
								ctx.stroke();
								ctx.restore();
								ctx.fillStyle = color;
								ctx.strokeStyle = color;
								// console.log(ctx); return;
							} else {
								// draw a line from pt1 to pt2
								ctx.moveTo(pt1.x, pt1.y);
								ctx.lineTo(pt2.x, pt2.y);
							}
						});
				ctx.stroke();

				particleSystem.eachNode(function(node, pt) {
					// node: {mass:#, p:{x,y}, name:"", data:{}}
					// pt: {x:#, y:#} node position in screen coords

					// determine the box size and round off the coords if we'll
					// be
					// drawing a text label (awful alignment jitter
					// otherwise...)
					var w = ctx.measureText(node.data.label || "").width + 6;
					var label = node.data.label;
					if (!(label || "").match(/^[ \t]*$/)) {
						pt.x = Math.floor(pt.x);
						pt.y = Math.floor(pt.y);
					} else {
						label = null;
					}

					// clear any edges below the text label

					ctx.fillStyle = 'rgba(255,255,255,.6)'
					// ctx.fillRect(pt.x-w/2, pt.y-7, w,14)

					ctx.clearRect(pt.x - w / 2, pt.y - 7, w, 14);

					// draw the text

					if (label) {
						ctx.font = "bold 11px Arial";
						ctx.textAlign = "center";

						// if (node.data.region) ctx.fillStyle =
						// palette[node.data.region]
						// else ctx.fillStyle = "#888888"
						
						ctx.fillStyle  = (node.data.type === self.highLightByType) ? ('red') : ("#888888");
						

						// ctx.fillText(label||"", pt.x, pt.y+4)
						ctx.fillText(label || "", pt.x, pt.y + 4);
					}
				});
			},

			resize : function() {
				var w = $(window).width(), h = $(window).height();
				canvas.width = w;
				canvas.height = h; // resize the canvas element to fill the
									// screen
				particleSystem.screenSize(w, h); // inform the system so it
													// can map coords for us
				that.redraw();
			},

			initMouseHandling : function() {
				// no-nonsense drag and drop (thanks springy.js)
				selected = null;
				nearest = null;
				var dragged = null;
				var oldmass = 1;

				$(canvas).mousedown(function(e) {
					var pos = $(this).offset();
					var p = {
						x : e.pageX - pos.left,
						y : e.pageY - pos.top
					}
					selected = nearest = dragged = particleSystem.nearest(p);

					if (selected.node !== null) {
						dragged.node.tempMass = 50;
						dragged.node.fixed = true;
					}
					return false;
				});

				$(canvas)
						.click(
								function(e) {
									var pos = $(this).offset();
									var p = {
										x : e.pageX - pos.left,
										y : e.pageY - pos.top
									}
									var selected = particleSystem.nearest(p);

									if (selected.node !== null) {
										//delete GraphData.nodes[selected.node.data.label];
										//delete GraphData.edges['Branch'][selected.node.data.label];
										
										$.get('/assoc-manage/form', {assoc: selected.node.data}, function(data) {
											$.fancybox(data, fancyBoxSettings);
										});
										

										
										fire(GraphData);
										// $.each(particleSystem.getEdgesFrom(selected.node),
										// function(edge) {

										// });

									}
									return false;
								});

				$(canvas).mousemove(function(e) {
					var old_nearest = nearest && nearest.node._id;
					var pos = $(this).offset();
					var s = {
						x : e.pageX - pos.left,
						y : e.pageY - pos.top
					};

					nearest = particleSystem.nearest(s);
					if (!nearest)
						return

					

					if (dragged !== null && dragged.node !== null) {
						var p = particleSystem.fromScreen(s);
						dragged.node.p = {
							x : p.x,
							y : p.y
						};
						// dragged.tempMass = 10000
					}

					return false;
				});

				$(window).bind('mouseup', function(e) {
					if (dragged === null || dragged.node === undefined)
						return;

					dragged.node.fixed = false;
					dragged.node.tempMass = 100;
					dragged = null;
					selected = null;
					return false;
				});

			},

		};

		return that;
	};

	var Maps = function() {
		window.sys = arbor.ParticleSystem({

		});

		sys.renderer = Renderer('#viewport'); // our newly created renderer
												// will have its .init() method
												// called shortly by sys...

		fire = function(data) {

			// load the raw data into the particle system as is (since it's
			// already formatted correctly for .merge)
			var nodes = data.nodes;
			$.each(nodes, function(name, info) {
				info.label = name.replace(/(people's )?republic of /i, '')
						.replace(/ and /g, ' & ');
			});

			sys.merge({
				nodes : nodes,
				edges : data.edges
			});
		};

		if (GraphData.length) {
			fire(GraphData);
		}


		$.getJSON(
			((($('body').hasClass('default-index-assoc-suggestions')) ? 
				('/graph/not-generated-assocs/tableName/') : 
					('/graph/generated/tableName/')) + 
			Graph.currentTable),
			function(data) {
				GraphData = data;
				fire(data);
			});
	};

	$(document).ready(function() {
		Maps();
		var saveModel = function() {
				$.post(
					(($('body').hasClass('default-index-assoc-suggestions')) ? 
						('/model-manage/add-assoc') : 
							('/model-manage/save')),
					GraphData
				);
		};
		$('#save-model').click(function() {
			saveModel();
		});
		
		$('.remove-assoc-opt').live('click', function() {
			$(this).closest('tr').remove();
			
			$.get('/assoc-manage/form', $('#assoc-data').serialize(), function(data) {
				$.fancybox(data, fancyBoxSettings);
			});
			
			return false;
		});
		
		$('#add-assoc-init').live('click', function() {
			$.get('/assoc-manage/form', $(this).closest('form').serialize(), function(data) {
				$.fancybox(data, fancyBoxSettings);
			});
		});
		
		$('#add-assoc-opts').live('click', function() {
			
			$('#allowed-option-keys').attr('name', 'assoc[options][' + $('#allowed-option-keys option:selected').text() + ']');
			$.get('/assoc-manage/form', $('#assoc-data').serialize(), function(data) {
				$.fancybox(data, fancyBoxSettings);
			});
			return false;
		});
		
		$('#add-assoc').click(function() {
			$.get('/assoc-manage/new-choose-type?tableName=' + Graph.currentTable , function(resp){
				$.fancybox(resp);
			});
		});
		
		var assocInit = function(form) {
			$.get('/assoc-manage/new-init', form.serialize(), function(resp){
				$.fancybox(resp);
			});
		};
		
		$('#assoc-choose-type').live('click', function() {
			assocInit($(this).closest('form'));
			return false;	
		});
		
		$('#assoc-model-init').live('change', function() {
			assocInit($(this).closest('form'));
		});
		

		$('#assoc-remove').live('click', function() {
			var newNodeData = $('#assoc-data').formToJson().assoc;
			
			var node = sys.getNode(newNodeData.label);
			var edges = sys.getEdgesFrom(node);
			
			for(var i = 0; i < edges.length; i++){
				sys.pruneEdge(edges[i]);
			}
			
			sys.pruneNode(node);
			
			delete GraphData.nodes[newNodeData.label];
			delete GraphData.edges[Graph.currentModelName][newNodeData.label];
			
			if($('body').hasClass('default-index-index')) {
				saveModel();
			}
			
			$.fancybox.close();
		});
		
		$('#assoc-options-save').live('click', function() {
			var newNodeData = $('#assoc-data').formToJson().assoc;
			
			if(typeof GraphData.nodes[newNodeData.label] !== 'undefined') {
				sys.getNode(newNodeData.label).data = GraphData.nodes[newNodeData.label] = newNodeData;
			} else {
				sys.addNode(newNodeData.label);
				sys.getNode(newNodeData.label).data = GraphData.nodes[newNodeData.label] = newNodeData;
				sys.addEdge(sys.getNode(newNodeData.label), sys.getNode(newNodeData.masterModel.modelName));
			}
			
			if($('body').hasClass('default-index-index')) {
				saveModel();
			}
			
			
			$.fancybox.close();
			
			return false;
		});
		
		$('#assoc-test').live('click', function() {
			
			$.get('/assoc-manage/test', $('#assoc-data').formToJson(), function(resp) {
				$.fancybox(resp, fancyBoxSettings);
			});
		});
		
		
		$('#select-master-model').live('click', function() {
			$.get('/assoc-manage/test', $('#assoc-test-form').formToJson(), function(resp) {
				$.fancybox(resp, fancyBoxSettings);
			});
		});
		
		$('.sql-queries-trigger').live('click', function(){
			if($('.sql-queries:visible').size()) {
				$('.sql-queries').hide();
			} else {
				$('.sql-queries').show();
			}
		});
		
		$('#highlight-type input[type=radio]').click(function(){
			sys.renderer.redraw($(this).val());
		});
		
		$('#model_filter').keyup(function(){
			var val = $(this).val();
			if(val) {
				$('#smoothmenu ul li a').show();
				$('#smoothmenu ul li a:not(:contains-ci("' + val + '"))').hide();
			}else {
				$('#smoothmenu ul li a').show();
			}
		});
		
		$('#loader-locker')
			.ajaxStart(function(){$(this).show();})
			.ajaxStop(function(){$(this).hide();});
	});
})();
