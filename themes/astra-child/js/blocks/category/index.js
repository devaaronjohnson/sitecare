/**
 * Internal block libraries
 */
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blockEditor;

const { PanelBody, TextControl, ToggleControl } = wp.components;

/**
 * Custom block components
 */
import icons from '../icons';

/**
 * External dependencies
 */
import { ReactSortable } from "react-sortablejs";

/**
 * Register Block.
 */
registerBlockType( 'grillseeker/category', {
	title: 'Category',
	description: 'Display the list of categories with icons.',
	category: 'grillseeker-blocks',
	icon: icons.category,
	keywords: [
		'investments',
	],
	attributes: {
		id: {
			type: 'string',
		},
		show: {
			type: 'array',
			default: [],
		}
	},
	edit: ( props ) => {
		const {
			attributes,
			className, 
			setAttributes,
		} = props;

		/**
		 * Set block ID.
		 */
		if ( ! attributes.id ) {
			let id = String.prototype.concat( ...new Set( Math.random().toString( 36 ).substring( 7 ).replace( /[0-9]/g, '' ) ) );

			setAttributes( { id } );
		}

		/**
		 * Add or remove category id from show array.
		 */
		const toggleCategory = ( id ) => {
			let show = [ ...attributes.show ];

			if ( show.includes( id ) ) {
				show = show.filter( ( item ) => item !== id );
			} else {
				show.push( id );
			}

			setAttributes( { show } );
		}

		// List categories in order based on show array.
		const showCategories = grillseeker.categories.filter( ( category ) => {
			return attributes.show.includes( category.term_id );
		} );

		showCategories.sort( ( a, b ) => {
			return attributes.show.indexOf( a.term_id ) - attributes.show.indexOf( b.term_id );
		} );

		return ( [
			<InspectorControls key='category-block-controls'>
				<PanelBody className='grillseeker-settings-panel category-panel' title='Categories' initialOpen={ true }>
					<p>Select the categories that should be shown.</p>

					{
						grillseeker.categories.map( ( category ) => {
							return (
								<div className='grillseeker-category'>  
									<ToggleControl
										label={ category.name }
										checked={ attributes.show.includes( category.term_id ) }
										onChange={ () => { toggleCategory( category.term_id ) } }
									/>
								</div>
							)
						} )
					}
				</PanelBody>

				<PanelBody className='grillseeker-settings-panel category-settings-panel' title='Categories Order' initialOpen={ true }>
					<p>Drag a category up or down to determine the order in which it should appear in the list.</p>

					<div className='grillseeker-sortable-category-list'>
					{
						// If there are no categories, show a message.
						showCategories.length === 0 ? (
							<p>No categories selected.</p>
						) : (
							// Otherwise, show a list of categories.
							<ReactSortable 
								list={ showCategories } 
								setList={ ( e ) => {
									let show = [ ...e ];

									// Get the term_id of each category.
									show = show.map( ( category ) => {
										return category.term_id;
									} );

									setAttributes( { show } )
								} }
							>
								{
									showCategories.map( ( category ) => {
										return (
											<div className='grillseeker-category' key={category.term_id}>  
												<h3>{ category.name }</h3>
											</div>
										)
									} )
								}
							</ReactSortable>
						)
					}
					</div>
				</PanelBody> 
			</InspectorControls>,

			<div className={ className + ' wp-block-grillseeker-category-block' } key='category-block-view'>       
				{	
					showCategories.length === 0 ? (
						<p>No categories selected.</p>
					) : (
						<div className="wp-block-grillseeker-category-block-content">
						{
							showCategories.map( ( category ) => {
								return (
									<div className='grillseeker-category' key={category.term_id}>
										<div className='grillseeker-category-icon'>
											<img class="grillseeker-category-icon-image remove-lazy-loading" src={ category.icon } decoding="sync" loading="nolazy" width="60" height="60" />
										</div>
										
										<div className='grillseeker-category-name'>
											<h3>{ category.name }</h3>
										</div>
									</div>
								)
							} )
						}
						</div>
					)
				}
			</div>
		] );
	},
	save: () => null,
} );
