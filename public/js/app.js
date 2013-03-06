(function() {
	window.GraphData = {};
	
	$(document).ready(function() {
		(new Util.Router({
			_particleSystem: null,
			_Renderer: null,
			_onGraphClick: function(selected){},
			
			_fancyBoxSettings : {
				hideOnOverlayClick: false
			},
			
			init: 	function(){
				var self = this;
				
				self._applyLoadingLocker();
				self._applyModelFilter();
			},
			
			default_index_index: function() {
				var self = this;
				
				self._onGraphClick = function(selected) {
					$.get('/assoc-manage/form', {assoc: selected.node.data}, function(data) {
						$.fancybox(data, self._fancyBoxSettings);
					});
				}
				
				self._initGraph(Graph, '/graph/generated/tableName');
				self._attachModelEvents();
				self._attachAssocManagmentEvents(Graph);
			},
			
			default_index_assoc_suggestions: function() {
				var self = this;
				
				self._initGraph(Graph, '/graph/not-generated-assocs/tableName');
				self._attachModelEvents();
				self._attachAssocManagmentEvents(Graph);
			},
			
			default_index_graph: function() {
				var self = this;
				
				self._initGraph(Graph, '/graph/models/tableName');
			},
			
			default_index_model_assocs: function() {
				var self = this;
				self._onGraphClick = function(selected) {
					

					
					$.get('/graph/models?tables[]=' + encodeURIComponent(selected.node.data.tableName), function(resp) {
						
						
						
						for(var modelName in resp['nodes']) {
							
							resp['nodes'][modelName].label = resp['nodes'][modelName].modelName;
							
							var newNode = 
								(self._getParticleSystem().getNode(resp['nodes'][modelName].modelName)) ? 
									(self._getParticleSystem().getNode(resp['nodes'][modelName].modelName)) : 
										(self._getParticleSystem().addNode(
												resp['nodes'][modelName].modelName, 
												resp['nodes'][modelName]
										));
							
							self._getParticleSystem().addEdge(
								selected.node,
								newNode
							);
							
							self._getRenderer().redraw();
						}
					});
				};
				self._initGraph(Graph, '/graph/models/tables');
			},
			
			/**
			 * @return ParticleSystem
			 */
			_getParticleSystem: function() {
				if(this._particleSystem === null) {
					this._particleSystem = arbor.ParticleSystem({});

					this._particleSystem.renderer = this._getRenderer(); // our newly created renderer
															// will have its .init() method
															// called shortly by sys...
				}
				
				return this._particleSystem;
			},
					
			_assocMangeInit: function(form) {
				$.get('/assoc-manage/new-init', form.serialize(), function(resp){
					$.fancybox(resp);
				});
			},
			
			/**
			 * 
			 */
			_saveModel: function(url) {
				$.post(
						(($('body').hasClass('default-index-assoc-suggestions')) ? 
								('/model-manage/add-assoc') : 
									('/model-manage/save')),
					GraphData, 
					function(resp){
						(resp.result) ? 
							(noty({text: resp.result, force: true, dismissQueue: true, type: 'success'})) : 
								(noty({text: resp.error, force: true, dismissQueue: true, type: 'error'}));
					}
				);
			},
			
			/**
			 * ((($('body').hasClass('default-index-assoc-suggestions')) ? 
						('/graph/not-generated-assocs/tableName/') : 
							(
								($('body').hasClass('default-index-index') || $('body').hasClass('default-index-model-assocs')) ? 
								('/graph/generated/tableName/') : 
								('/graph/models/tableName/')
			 */
			_initGraph: function(Graph, graphPath) {
				var self = this;

				if (GraphData.length) {
					self._graphMergeNodes(GraphData);
				}


				$.getJSON((graphPath + '/' + Graph.currentTable),
					function(data) {
						GraphData = data;
						self._graphMergeNodes(data);
					});
			},
			
			_graphMergeNodes: function(data) {
				var self = this;
				// load the raw data into the particle system as is (since it's
				// already formatted correctly for .merge)
				var nodes = data.nodes;
				$.each(nodes, function(name, info) {
					info.label = name.replace(/(people's )?republic of /i, '')
							.replace(/ and /g, ' & ');
				});

				self._getParticleSystem().merge({
					nodes : nodes,
					edges : data.edges
				});
			},
			
			_applyLoadingLocker: function() {
				$('#loader-locker')
					.ajaxStart(function(){$(this).show();})
					.ajaxStop(function(){$(this).hide();});
			},
			
			_applyModelFilter: function() {
				$('#model_filter').keyup(function(){
					var val = $(this).val();
					if(val) {
						$('#smoothmenu ul li a').show();
						$('#smoothmenu ul li a:not(:contains-ci("' + val + '"))').hide();
					}else {
						$('#smoothmenu ul li a').show();
					}
				});
			},
			
			/**
			 * Attach Events for:
			 *  - Save model
			 *  - Select Master Model
			 */
			_attachModelEvents: function() {
				var self = this;
				$('#save-model').click(function() {
					self._saveModel();
				});
								
				
				$('#select-master-model').live('click', function() {
					$.get('/assoc-manage/test', $('#assoc-test-form').formToJson(), function(resp) {
						$.fancybox(resp, self._fancyBoxSettings);
					});
				});
			},
			
			/**
			 * Attach Events for
			 *  - Add Assoc
			 *  - Assoc Remove
			 *  - Add Assoc Init
			 *  
			 *  - Add Assoc Option
			 *  - Remove Assoc Option
			 *  - Assoc Options Save
			 *  
			 *  - Assoc Choose Type
			 *  - Assoc Model Init
			 *  
			 *  - Assoc Test
			 */
			_attachAssocManagmentEvents: function(Graph) {
				var self = this;
				
				$('.remove-assoc-opt').live('click', function() {
					$(this).closest('tr').remove();
					
					$.get('/assoc-manage/form', $('#assoc-data').serialize(), function(data) {
						$.fancybox(data, self._fancyBoxSettings);
					});
					
					return false;
				});
				
				$('#add-assoc-init').live('click', function() {
					$.get('/assoc-manage/form', $(this).closest('form').serialize(), function(data) {
						$.fancybox(data, self._fancyBoxSettings);
					});
				});
				
				$('#add-assoc-opts').live('click', function() {
					
					$('#allowed-option-keys').attr('name', 'assoc[options][' + $('#allowed-option-keys option:selected').text() + ']');
					$.get('/assoc-manage/form', $('#assoc-data').serialize(), function(data) {
						$.fancybox(data, self._fancyBoxSettings);
					});
					return false;
				});
				
				$('#add-assoc').click(function() {
					$.get('/assoc-manage/new-choose-type?tableName=' + Graph.currentTable , function(resp){
						$.fancybox(resp);
					});
				});
				
				$('#assoc-choose-type').live('click', function() {
					self._assocMangeInit($(this).closest('form'));
					return false;	
				});
				
				$('#assoc-model-init').live('change', function() {
					self._assocMangeInit($(this).closest('form'));
				});
				
				
				$('#assoc-remove').live('click', function() {
					var newNodeData = $('#assoc-data').formToJson().assoc;
					
					var node = self._getParticleSystem().getNode(newNodeData.label);
					var edges = self._getParticleSystem().getEdgesFrom(node);
					
					for(var i = 0; i < edges.length; i++){
						self._getParticleSystem().pruneEdge(edges[i]);
					}
					
					self._getParticleSystem().pruneNode(node);
					
					delete GraphData.nodes[newNodeData.label];
					delete GraphData.edges[Graph.currentModelName][newNodeData.label];
					
					if($('body').hasClass('default-index-index')) {
						self._saveModel();
					}
					
					$.fancybox.close();
				});
				
				$('#assoc-options-save').live('click', function() {
					var newNodeData = $('#assoc-data').formToJson().assoc;
					
					if(typeof GraphData.nodes[newNodeData.label] !== 'undefined') {
						self._getParticleSystem().getNode(newNodeData.label).data = GraphData.nodes[newNodeData.label] = newNodeData;
					} else {
						self._getParticleSystem().addNode(newNodeData.label);
						self._getParticleSystem().getNode(newNodeData.label).data = GraphData.nodes[newNodeData.label] = newNodeData;
						self._getParticleSystem().addEdge(self._getParticleSystem().getNode(newNodeData.label), self._getParticleSystem().getNode(newNodeData.masterModel.modelName));
					}
					
					if($('body').hasClass('default-index-index')) {
						self._saveModel();
					}
					
					
					$.fancybox.close();
					
					return false;
				});
				
				$('#assoc-test').live('click', function() {
					
					$.get('/assoc-manage/test', $('#assoc-data').formToJson(), function(resp) {
						$.fancybox(resp, self._fancyBoxSettings);
					});
				});
			},
			
			_getRenderer: function() {				
				var self = this;
				if(this._Renderer === null) {
					canvas = $('#viewport').get(0);
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
													self._onGraphClick(selected);
													
													//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
				} 
				
				return this._Renderer;
			}
		},$("body").attr("class"))).handle();
	});

})();
