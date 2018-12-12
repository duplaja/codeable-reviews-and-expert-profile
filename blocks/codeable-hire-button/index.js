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
	registerBlockType( 'codeable-reviews-and-expert-profile/codeable-hire-button', {
		/**
		 * This is the display title for your block, which can be translated with `i18n` functions.
		 * The block inserter will show this name.
		 */
		title: __( 'Codeable Hire Button' ),

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
			message: {
				type: 'string',
				default: 'Hire Me',
			},
			class: {
				type: 'string',
				default: '',
			},
			referoo: {
				type: 'string',
				default: '',
			},
			theme: {
				type: 'string',
				default: 'black',
			}
		},


		/**
		 * The edit function describes the structure of your block in the context of the editor.
		 * This represents what the editor will render when the block is used.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
		 *
		 * @param {Object} [props] Properties passed from the editor.
		 * @return {Element}       Element to render.
		 */
		edit: function( props ) {


			var className = props.className;
			var codeable_id = props.attributes.codeable_id;
			var referoo = props.attributes.referoo;
			var message = props.attributes.message;
			var classtemp = props.attributes.class;
			var theme = props.attributes.theme;

			function updateID(event) {
				props.setAttributes({ codeable_id: event.target.value });
			}
			function updateReferoo(event) {
				props.setAttributes({ referoo: event.target.value });
			}
			function updateMessage(event) {
				props.setAttributes({ message: event.target.value });
			}
			function updateTheme(event){
				props.setAttributes({ theme: event.target.value });
			}
			function updateClass(event) {
				props.setAttributes({ class: event.target.value });
			}

			return el('p', { className: props.className },
				el('h3',{ className: 'codeable-header-backend'},'Codeable Hire Button'),
				el('ul',{className: 'codeable-editor-ul'},
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
						el('label',{for: 'referoo' },'Referoo Code: '),
						el('br'),
						el('input', { 
							value: referoo, 
							onChange: updateReferoo,
							id: 'referoo',
							type: 'text',
							className: 'codeable-input-backend',
							title: 'See Referoo Panel for your code (string)'
						}),
					),
					el('li',{},
						el('label',{for: 'message' },'Button Text: '),
						el('br'),
						el('input', { 
							value: message, 
							onChange: updateMessage,
							id: 'message',
							type: 'text',
							className: 'codeable-input-backend',
							title: 'Button Text'
						}),
					),
					el('li',{},
						el('label',{for: 'theme' },'Theme'),
						el('br'),
						el('select', {
							value: theme, 
							onChange: updateTheme,
							id: 'theme',
							className: 'codeable-input-backend',
							title: 'Pick from some pre-created button themes'
						}, 
							el('option',{ value: 'black' },'Black'),
							el('option',{ value: 'white' },'White'),
							el('option',{ value: 'none' },'No Theme')
						)
					),
					el('li',{},
						el('label',{for: 'class' },'Custom Class: '),
						el('br'),
						el('input', { 
							value: classtemp, 
							onChange: updateClass,
							id: 'class',
							type: 'text',
							className: 'codeable-input-backend',
							title: 'Add a custom class to your button for additional styling.'
						}),
					)
				)
			);



		},

		/**
		 * The save function defines the way in which the different attributes should be combined
		 * into the final markup, which is then serialized by Gutenberg into `post_content`.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#save
		 *
		 * @return {Element}       Element to render.
		 */
		save: function() {
			return null; //Display handled by callback function
		}
	} );
} )(
	window.wp
);
