(function(){
	"use strict"

	var getElementPosition = function(el){
	    var sumLeft = 0;
	    var sumTop = 0;
	    if (el.offsetParent) {
	        do {
	            sumLeft += el.offsetLeft;
	            sumTop += el.offsetTop;
	        } while (el = el.offsetParent);
	        return { x: sumLeft, y: sumTop };
	    }
	}

	// DOM block helper/repository
	var DOMBlock = {
		find: function(id){
			return $('.block-component')
				.filter(function(el){ return $(this).data('id') === id; })
				// there always should be 1 max, but whatever
				.first();
		}
	};

	var DOMBlockAttachment = {
		find: function(block_id, type){
			return DOMBlock.find(block_id)
			.find('.attachment.attachment-' + type);
		}
	};


	var blockComponent = {
		props: ['block'],
		template: '#block-component',
		data: function(){
			return {
				mouseOffset: {x: 0, y:0},
				active_attachments: {a:123}
			}
		},

		events:{
			'active_attachments:changed': function(v, old){
				var self = this;
				// filter, so we have attachments for this block
				var filtered = v.filter(function(att){
					return att.block_id == self.block.id;
				});
				// map them as type => true/false
				self.active_attachments = {};
				filtered.forEach(function(v){
					Vue.set(self.active_attachments, v.type, true);
				});
			}
		},

		methods: {
			dragStart: function(event){
				this.mouseOffset = {
					x: event.offsetX,
					y: event.offsetY
				};
			},
			dragEnd: function(event){
				var parentElementOffset = getElementPosition(event.target.parentElement);
				var windowPosition = {
					x: window.screenLeft,
					y: window.screenTop
				};
				this.block.position = {
					x: event.clientX - this.mouseOffset.x - parentElementOffset.x - windowPosition.x,
					y: event.clientY - this.mouseOffset.y - parentElementOffset.y - windowPosition.y
				};
			},
			attachmentClicked: function(type){
				this.$dispatch('active_attachment:push', {
					block_id: this.block.id,
					type: type
				});
			}
		}
	};

	var blockListComponent = {
		props: ['blocks', 'current_id'],
		template: '#block-list-component',

		methods:{
			cssClass: function(block){
				if(block.type === 'operand') return 'block-operand';
				if(block.type === 'predicate') return 'block-predicate';
				if(block.type === 'endpredicate') return 'block-endpredicate';
				if(block.type === 'input') return 'block-input';
				if(block.type === 'output') return 'block-output';
				return '';
			},
			createBlock: function(type){
				this.blocks.push({
					id: this.current_id + 1,
					type: type,
					text: prompt('text'),
					position: {x: 0, y: 0}
				});
			},
			remove: function(block){
				this.blocks.$remove(block);
			}
		}
	};

	var varListComponent = {
		props: ['variables'],
		template: '#var-list-component',
		methods:{
			addVariable: function(){

				this.variables.push({
					type: $('#var-type').val(),
					name: $('#var-name').val()
				});

				$('#var-type').val('');
				$('#var-name').val('');

			},

			removeVariable: function(variable){
				this.variables.$remove(variable);
			}
		}



	};

	var linkComponent = {
		template: '#link-component',
		props: ['link', 'blocks'],

		data: function(){
			return {
				// just change it to any non falsey val after dom loaded
				// this will recalculate computed properties
				// this should get changed in the 'attached' hook (so after dom load)
				dom_loaded: 0 
			};
		},

		computed:{
			a: function(){
				if(!this.dom_loaded){
					return {x:0,y:0};
				}
				return this.getAttachmentPosition(this.link.a.block_id, this.link.a.type);
			},
			b: function(){
				if(!this.dom_loaded){
					return {x:0,y:0};
				}
				return this.getAttachmentPosition(this.link.b.block_id, this.link.b.type);
			}
		},

		attached: function(){
			this.dom_loaded = +new Date();
		},

		methods:{
			getAttachmentPosition: function(block_id, type){
				var block = this.getBlock(block_id);
				var attachment = DOMBlockAttachment.find(block_id, type);

				//todo: move this code somewhere else (?)
				if(!block || !attachment){
					this.$dispatch('link:remove', this.link);
					return {x:0,y:0};
				}

				return {
					x: block.position.x + attachment.position().left,
					y: block.position.y + attachment.position().top
				}
			},
			getBlock: function(id){
				var block;
				this.blocks.forEach(function(b){
					if(b.id === id) block = b;
				});
				return block;
			}
		}
	};

	var diagramComponent = {
		props: ['blocks', 'links'],
		template: '#diagram-component',

		components: {
			'block-component': blockComponent,
			'link-component': linkComponent
		}
	};

	var blockEditorComponent = {
		props: ['block'],
		template: '#block-editor-component',
	};

	var varEditorComponent = {
		props: ['variable'],
		template: '#var-editor-component',
	};

	var jebando = new Vue({
		el: '#app',
		data: {
			active_block: false,
			active_attachments: [],
			blocks: [
				{id: 1,type: 'operand', text: 'block #1', position: {x: 100, y: 55}},
				{id: 2,type: 'predicate', text: 'block #2', position: {x: 235, y: 150}},
				{id: 3,type: 'operand', text: 'block #3', position: {x: 199, y: 270}},
				{id: 4,type: 'operand', text: 'block #4', position: {x: 370, y: 270}},
			],
			links: [
				{
					a: {block_id: 1, type: 'bottom'},
					b: {block_id: 2, type: 'top'}
				}
			],
			variables: [

			]
		},
		methods: {
			submitDiagram: function(event){
				event.preventDefault();
				var url = event.target.href;

			}
		},
		computed:{
			current_id: function(){
				var max = 0;
				this.blocks.forEach(function(block){
					max = Math.max(block.id, max);
				});
				return max;
			},
			exported_data: function(){
				return {
					blocks: this.blocks,
					links: this.links,
					variables: this.variables
				};
			}
		},

		watch: {
			'active_attachments': function(value, old){
				this.$broadcast('active_attachments:changed', value, old);
			}
		},

		components: {
			'diagram-component': diagramComponent,
			'block-editor-component': blockEditorComponent,
			'var-editor-component': varEditorComponent,
			'block-list-component': blockListComponent,
			'var-list-component' : varListComponent
		},

		events: {
			'active_block:set': function(active){
				this.active_block = active;
			},
			'link:remove': function(link){
				this.links.$remove(link);
			},
			'active_attachment:push': function(attachment){
				this.active_attachments.push(attachment);
				var active = this.active_attachments;

				if(active.length !== 2) return;
				// check if link already exists

				var isActive = function(attachment){
					for(var i=0;i<active.length;i++){
						var current = active[i];
						if(attachment.block_id != current.block_id){
							continue;
						}
						if(attachment.type != current.type){
							continue;
						}
						return true;
					}
					return false;
				}

				var filtered = this.links.filter(function(link){
					return isActive(link.a) && isActive(link.b);
				});

				// link already exists - remove it
				if(filtered.length){
					this.active_attachments = [];
					this.links.$remove(filtered.shift());
					return;
				}

				// check if linking direction is allowed
				var a = active.shift();
				var b = active.shift();

				var typesAllowedAsFirst = ['bottom', 'left', 'right'];
				var typesAllowedAsSecond = ['top'];

				if(! (~ typesAllowedAsFirst.indexOf(a.type))){
					return;
				}
				if(! (~ typesAllowedAsSecond.indexOf(b.type))){
					return;
				}
				// check if we arent trying to link to the same block
				if(a.block_id === b.block_id){
					return;
				}


				this.links.push({a: a, b: b});
			}
		}

	});

	$("#btnLoad").click( function () {
		if(window.File && window.FileReader && window.FileList && window.Blob){
			alert("co jest get_right kurwa?");
		} else {
			alert("chuj");
		}

	});

	function handleFileSelect(evt) {
		var files = evt.target.files; // FileList object

		// files is a FileList of File objects. List some properties.
		var output;
		for (var i = 0, f; f = files[i]; i++) {

			var reader = new FileReader();
			reader.onload = function(e) {
				//alert( reader.result);

				var object = JSON.parse(reader.result);

				jebando.blocks = object.blocks;
				jebando.links = object.links;
				jebando.variables = object.variables;

				//console.log(object.blocks);

				//alert(JSON.parse(reader.result));
			}

			reader.readAsText(f);

		}
		//document.getElementById('list').innerHTML = '<ul>' + output.join('') + '</ul>';
	}

	document.getElementById('files').addEventListener('change', handleFileSelect, false);

	//console.log(jebando.blocks);
})();