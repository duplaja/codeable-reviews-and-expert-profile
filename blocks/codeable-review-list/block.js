( function( wp ) {
	/**
	 * Registers a new block provided a unique name and an object defining its behavior.
	 * @see https://github.com/WordPress/gutenberg/tree/master/blocks#api
	 */
	var registerBlockType = wp.blocks.registerBlockType;
	/**
	 * Returns a new element of given type. Element is an abstraction layer atop React.
	 * @see https://github.com/WordPress/gutenberg/tree/master/element#element
	 */
	var el = wp.element.createElement;
	/**
	 * Retrieves the translation of text.
	 * @see https://github.com/WordPress/gutenberg/tree/master/i18n#api
	 */
	var __ = wp.i18n.__;

	/**
	 * Every block starts by registering a new block type definition.
	 * @see https://wordpress.org/gutenberg/handbook/block-api/
	 */
	registerBlockType( 'codeable-reviews-and-expert-profile/codeable-review-list', {
		/**
		 * This is the display title for your block, which can be translated with `i18n` functions.
		 * The block inserter will show this name.
		 */
		title: __( 'Codeable Reviews List' ),

		/**
		 * Blocks are grouped into categories to help users browse and discover them.
		 * The categories provided by core are `common`, `embed`, `formatting`, `layout` and `widgets`.
		 */
		category: 'widgets',

		/**
		 * Optional block extended support features.
		 */
		supports: {
			// Removes support for an HTML mode.
			html: false,
		},


		attributes: {
			codeable_id: {
				type: 'string',
				default: '',
			},
			number_to_pull: {
				type: 'string',
				default: '40',
			},
			show_x_more: {
				type: 'string',
				default: '10',
			},
			min_review_length: {
				type: 'string',
				default: '0',
			},
			min_score: {
				type: 'string',
				default: '1',
			},
			max_score: {
				type: 'string',
				default: 5,
			},
			show_title: {
				type: 'string',
				default: 'no',
			},
			show_date: {
				type: 'string',
				default: 'no',
			},
			show_rating: {
				type: 'string',
				default: 'yes',
			},
			sort: {
				type: 'string',
				default: '',
			},
			start_at: {
				type: 'string',
				default: '1',
			},
			has_picture: {
				type: 'string',
				default: 'no',
			},
			start_time: {
				type: 'string',
				default: '',
			},
			end_time: {
				type: 'string',
				default: '',
			},
			schema: {
				type: 'string',
				default: '',
			},
			schema_desc: {
				type: 'string',
				default: 'Custom WordPress work through Codeable.io',
			},
			filter_clients: {
				type: 'string',
				default: '',
			},
			filter_reviews: {
				type: 'string',
				default: '',
			},
			only_clients: {
				type: 'string',
				default: '',
			},
			only_reviews: {
				type: 'string',
				default: '',
			},
		},

		/**
		 * The edit function describes the structure of your block in the context of the editor.
		 * This represents what the editor will render when the block is used.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
		 *
		 * @param {Object} [props] Properties passed from the editor.
		 * @return {Element}       Element to render.
		 */
		edit: function(props) {
			var className = props.className;
			var codeable_id = props.attributes.codeable_id;
			var number_to_pull = props.attributes.number_to_pull;
			var show_x_more = props.attributes.show_x_more;
			var min_review_length = props.attributes.min_review_length;
			var min_score = props.attributes.min_score;
			var max_score = props.attributes.max_score;
			var show_title = props.attributes.show_title;
			var show_date = props.attributes.show_date;
			var show_rating = props.attributes.show_rating;
			var sort = props.attributes.sort;
			var start_at = props.attributes.start_at;
			var has_picture = props.attributes.has_picture;
			var start_time= props.attributes.start_time;
			var end_time= props.attributes.end_time;
			var schema= props.attributes.schema;
			var schema_desc= props.attributes.schema_desc;
			var filter_clients = props.attributes.filter_clients;
			var filter_reviews = props.attributes.filter_reviews;
			var only_clients = props.attributes.only_clients;
			var only_reviews = props.attributes.only_reviews;


			function updateID(event) {
				props.setAttributes({ codeable_id: event.target.value });
			}
			function updateNumPull(event) {
				props.setAttributes({ number_to_pull: event.target.value });
			}
			function updateShowXMore(event) {
				props.setAttributes({ show_x_more: event.target.value });
			}
			function updateMinReviewLength(event) {
				props.setAttributes({ min_review_length: event.target.value });
			}
			function updateMinScore(event) {
				props.setAttributes({ min_score: event.target.value });
			}
			function updateMaxScore(event) {
				props.setAttributes({ max_score: event.target.value});
			}
			function updateShowTitle(event) {
				props.setAttributes({ show_title: event.target.value});
			}
			function updateShowDate(event) {
				props.setAttributes({ show_date: event.target.value}); 
			}
			function updateShowRating(event) {
				props.setAttributes({ show_rating: event.target.value}); 
			}
			function updateSort(event) {
				props.setAttributes({ sort: event.target.value}); 
			}
			function updateStartAt(event) {
				props.setAttributes({ start_at: event.target.value}); 
			}
			function updateHasPicture(event) {
				props.setAttributes({ has_picture: event.target.value}); 
			}
			function updateStartTime(event) {
				props.setAttributes({ start_time: event.target.value}); 
			}
			function updateEndTime(event) {
				props.setAttributes({ end_time: event.target.value}); 
			}
			function updateSchema(event) {
				props.setAttributes({ schema: event.target.value}); 
			}
			function updateSchemaDesc(event) {
				props.setAttributes({ schema_desc: event.target.value}); 
			}
			function updateOnlyClients(event) {
				props.setAttributes({ only_clients: event.target.value}); 
			}
			function updateOnlyReviews(event) {
				props.setAttributes({ only_reviews: event.target.value}); 
			}
			function updateFilterClients(event) {
				props.setAttributes({ filter_clients: event.target.value}); 
			}
			function updateFilterReviews(event) {
				props.setAttributes({ filter_reviews: event.target.value}); 
			}

			function toggle_visibility(id) {
				var e = document.getElementById(id);
				if(e.style.display == 'block')
				   e.style.display = 'none';
				else
				   e.style.display = 'block';
			 }

			function toggleOnClick() {
			
				toggle_visibility('codeable-editor-ul-reviews-container-1');
				toggle_visibility('codeable-editor-ul-reviews-container-2');
			
			}


			return el('p', {className: className },
				el('h3',{ className: 'codeable-header-backend'},'Codeable Reviews List Block ',el('button',{onClick: toggleOnClick},'Show / Hide Inputs')), 
				el('ul',{ className: 'codeable-editor-ul', id: 'codeable-editor-ul-reviews-container-1', style: {'display':'none'}},
					el('li',{},
						el('label',{for: 'codeable_id' },'Codeable ID*: '),
						el('br'),
						el('input', { 
							value: codeable_id, 
							onChange: updateID,
							id: 'codeable_id',
							type: 'number',
							className: 'codeable-input-backend',
							title: 'Required: From Your "Hire Me" Link'
						}),		
					),
					el('li',{},
						el('label',{for: 'number_to_pull' },'Num To Pull: '),
						el('br'),
						el('select', { 
							value: number_to_pull, 
							onChange: updateNumPull,
							id: 'number_to_pull',
							className: 'codeable-input-backend',
							title: 'How many reviews to pull from API, pre filtering. If you filter a lot, pull more.'
						}, 
							el('option',{ value: '20' },'Pull 20'),
							el('option',{ value: '40' },'Pull 40'),
							el('option',{ value: '60' },'Pull 60'),
							el('option',{ value: '80' },'Pull 80'),
							el('option',{ value: '100' },'Pull 100'),
							el('option',{ value: '200' },'Pull 200'),
							el('option',{ value: '500' },'Pull 500'),
							el('option',{ value: '1000' },'Pull 1k'),
						)
										
					),
					el('li',{},
						el('label',{for: 'show_x_more' },'Num to Show'),
						el('br'),
						el('select', { 
							value: show_x_more, 
							onChange: updateShowXMore,
							id: 'show_x_more',
							className: 'codeable-input-backend'
						}, 
							el('option',{ value: '0' },'Max Possible'),
							el('option',{ value: '1' },'1 Review'),
							el('option',{ value: '2' },'2 Reviews'),
							el('option',{ value: '3' },'3 Reviews'),
							el('option',{ value: '5' },'5 Reviews'),
							el('option',{ value: '10' },'10 Reviews'),
							el('option',{ value: '20' },'20 Reviews'),
							el('option',{ value: '30' },'30 Reviews'),
							el('option',{ value: '40' },'40 Reviews'),
							el('option',{ value: '50' },'50 Reviews'),
							el('option',{ value: '100' },'100 Reviews'),
							el('option',{ value: '200' },'200 Reviews'),
							el('option',{ value: '500' },'500 Reviews'),
							el('option',{ value: '1000' },'1000 Reviews'),
						)
										
					),
					el('li',{},
						el('label',{for: 'min_review_length' },'Review Len (filter): '),
						el('br'),
						el('select', { 
							value: min_review_length, 
							onChange: updateMinReviewLength,
							id: 'min_review_length',
							className: 'codeable-input-backend'
						}, 
							el('option',{ value: '0' },'Show All'),
							el('option',{ value: '1' },'Not Blank'),
							el('option',{ value: '100' },'> 100 char'),
							el('option',{ value: '200' },'> 200 char'),
							el('option',{ value: '400' },'> 400 char')
						)				
					),
					el('li',{},
						el('label',{for: 'min_score' },'Min Score: (filter)'),
						el('br'),
						el('select', {
							value: min_score, 
							onChange: updateMinScore,
							id: 'min_score',
							className: 'codeable-input-backend'
						}, 
							el('option',{ value: '5' },'5'),
							el('option',{ value: '4' },'4'),
							el('option',{ value: '3' },'3'),
							el('option',{ value: '2' },'2'),
							el('option',{ value: '1' },'1')
						
						)				
					),
					el('li',{},
						el('label',{for: 'max_score' },'Max Score (filter): '),
						el('br'),
						el('select', {
							value: max_score, 
							onChange: updateMaxScore,
							id: 'max_score',
							className: 'codeable-input-backend'
						}, 
							el('option',{ value: '5' },'5'),
							el('option',{ value: '4' },'4'),
							el('option',{ value: '3' },'3'),
							el('option',{ value: '2' },'2'),
							el('option',{ value: '1' },'1')
						
						)				
					),
					el('li',{},
						el('label',{for: 'has_picture' },'Client Picture (filter): '),
						el('br'),
						el('select', {
							value: has_picture, 
							onChange: updateHasPicture,
							id: 'show_title',
							className: 'codeable-input-backend',
							title: 'Optionally only show clients who have set a profile picture (non-default)'
						}, 
							el('option',{ value: 'no' },'Show All'),
							el('option',{ value: 'yes' },'Only With Picture')
						
						)				
					),
					el('li',{},
						el('label',{for: 'show_title' },'Task Title: (display)'),
						el('br'),
						el('select', {
							value: show_title, 
							onChange: updateShowTitle,
							id: 'show_title',
							className: 'codeable-input-backend'
						}, 
							el('option',{ value: 'no' },'Don\'t Show'),
							el('option',{ value: 'yes' },'Show It')
						
						)				
					),
					el('li',{},
						el('label',{for: 'show_date' },'Task Date: (display)'),
						el('br'),
						el('select', {
							value: show_date, 
							onChange: updateShowDate,
							id: 'show_date',
							className: 'codeable-input-backend'
						}, 
							el('option',{ value: 'no' },'Don\'t Show'),
							el('option',{ value: 'yes' },'Show It')
						
						)				
					),
					el('li',{},
						el('label',{for: 'show_rating' },'Rating: (display)'),
						el('br'),
						el('select', {
							value: show_rating, 
							onChange: updateShowRating,
							id: 'show_rating',
							className: 'codeable-input-backend'
						}, 
							el('option',{ value: 'no' },'Don\'t Show'),
							el('option',{ value: 'yes' },'Show It')
						
						)				
					),
					el('li',{},
						el('label',{for: 'sort' },'Sort: (display)'),
						el('br'),
						el('select', {
							value: sort, 
							onChange: updateSort,
							id: 'sort',
							className: 'codeable-input-backend'
						}, 
							el('option',{ value: '' },'Profile Order'),
							el('option',{ value: 'rand' },'Random')
						
						),
					),	
					el('li',{},
						el('label',{for: 'start_at' },'Start at #: (filter)'),
						el('br'),
						el('input', { 
							value: start_at, 
							onChange: updateStartAt,
							id: 'start_at',
							type: 'number',
							className: 'codeable-input-backend',
							title: 'Used as an offset. Useful for listing x reviews, adding your own content, then x more.'
						})				
					),
					el('li',{},
						el('label',{for: 'start_time' },'Start Time: (filter)'),
						el('br'),
						el('input', { 
							value: start_time, 
							onChange: updateStartTime,
							id: 'start_time',
							type: 'number',
							className: 'codeable-input-backend',
							title: 'Only show reviews after this time (format: Unix Timestamp). Blank to disable'
						})				
					),
					el('li',{},
						el('label',{for: 'end_time' },'End Time: (filter)'),
						el('br'),
						el('input', { 
							value: end_time, 
							onChange: updateEndTime,
							id: 'end_time',
							type: 'number',
							className: 'codeable-input-backend',
							title: 'Only show reviews before this time (format: Unix Timestamp). Blank to disable.'
						})				
					),
					el('li',{},
						el('label',{for: 'schema' },'Build Schema: '),
						el('br'),
						el('select', {
							value: schema, 
							onChange: updateSchema,
							id: 'schema',
							className: 'codeable-input-backend',
							title: 'Experimental: Build Schema for your page for search engines'
						}, 
							el('option',{ value: '' },'No'),
							el('option',{ value: 'yes' },'Yes')
						
						)				
					),				
					
				), 
				el('ul',{ className: 'codeable-editor-ul-longer', id: 'codeable-editor-ul-reviews-container-2', style: {'display':'none'}},
					el('li',{},
						el('label',{for: 'schema_desc', className: 'codeable-line-input-backend-label'},'Schema Desc: '),
						el('br'),
						el('input', {
							type: 'text',
							onChange: updateSchemaDesc,
							value: schema_desc,
							id: 'schema_desc',
							title: 'Schema Product Desc (Only takes effect if Schema is set to yes)',
							className: 'codeable-line-input-backend'
						})
					),
					el('li',{},
						el('label',{for: 'filter_clients', className: 'codeable-line-input-backend-label'},'Filter Clients: '),
						el('br'),
						el('input', {
							type: 'text',
							onChange: updateFilterClients,
							value: filter_clients,
							id: 'filter_clients',
							title: 'Comma seperated list of client IDs to exclude from display',
							className: 'codeable-line-input-backend'
						})
					),
					el('li',{},
						el('label',{for: 'filter_reviews', className: 'codeable-line-input-backend-label'},'Filter Reviews: '),
						el('br'),
						el('input', {
							type: 'text',
							onChange: updateFilterReviews,
							value: filter_reviews,
							id: 'filter_reviews',
							title: 'Comma seperated list of review IDs to exclude from display',
							className: 'codeable-line-input-backend'
						})
					),
					el('li',{},
						el('label',{for: 'only_clients', className: 'codeable-line-input-backend-label'},'Show Only Clients: '),
						el('br'),
						el('input', {
							type: 'text',
							onChange: updateOnlyClients,
							value: only_clients,
							id: 'only_clients',
							title: 'Comma serpated list of client IDs to exclusively display (other filters still apply)',
							className: 'codeable-line-input-backend'
						})
					),
					el('li',{},
						el('label',{for: 'only_reviews', className: 'codeable-line-input-backend-label'},'Show Only Reviews: '),
						el('br'),
						el('input', {
							type: 'text',
							onChange: updateOnlyReviews,
							value: only_reviews,
							id: 'only_reviews',
							title: 'Comma serpated list of review IDs to exclusively display (other filters still apply)',
							className: 'codeable-line-input-backend'
						})
					)
				)	
			)

		},

		/**
		 * The save function defines the way in which the different attributes should be combined
		 * into the final markup, which is then serialized by Gutenberg into `post_content`.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#save
		 *
		 * @return {Element}       Element to render.
		 */
		save: function() {
			return null;
		}
	} );
} )(
	window.wp
);
