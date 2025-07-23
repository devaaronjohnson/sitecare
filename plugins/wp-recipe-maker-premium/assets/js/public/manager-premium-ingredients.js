import  { parseQuantity, formatQuantity } from '../shared/quantities';

window.WPRecipeMaker = typeof window.WPRecipeMaker === "undefined" ? {} : window.WPRecipeMaker;

window.WPRecipeMaker.managerPremiumIngredients = {
	load() {
		// Listen for ingredient changes.
		document.addEventListener( 'wprm-recipe-change', ( event ) => {
			if ( 'servings' === event.detail.type || 'unitSystem' === event.detail.type ) {
				window.WPRecipeMaker.manager.getRecipe( event.detail.id ).then( ( recipe ) => {
					if ( recipe ) {
						WPRecipeMaker.managerPremiumIngredients.updateIngredientsDisplay( recipe );
					}
				});
			}
		});
	},
	getCurrentIngredients( recipe ) {
		// Needs to have happened first to make sure we have parsed amounts.
		if ( ! recipe.data.hasOwnProperty( 'ingredientsElements' ) ) {
			window.WPRecipeMaker.managerPremiumIngredients.findIngredientsElements( recipe );
		}

		let currentIngredients = [];

		const revertOriginal = recipe.data.currentServingsParsed === recipe.data.originalServingsParsed && recipe.data.currentSystem === recipe.data.originalSystem;

		for ( let i = 0; i < recipe.data.ingredients.length; i++ ) {
			const ingredient = recipe.data.ingredients[ i ];
			let currentIngredient = {}			

			for ( let i = 1; i <= 2; i++ ) {
				let unitSystem;
				let usingFallbackUnitSystem;

				if ( ingredient.unit_systems.hasOwnProperty( 'unit-system-' + i ) ) {
					unitSystem = ingredient.unit_systems[ 'unit-system-' + i ];
					usingFallbackUnitSystem = false;
				} else {
					// Pick the other one, which should be set
					unitSystem = ingredient.unit_systems[ 'unit-system-' + ( i % 2 + 1 ) ];
					usingFallbackUnitSystem = true;
				}

				// Check if fractions should be used in this unit system.
				let allowFractions = wprmp_public.settings.fractions_enabled;
			
				if ( wprmp_public.settings.unit_conversion_enabled && window.WPRecipeMaker.hasOwnProperty( 'conversion' ) ) {
					allowFractions = wprmp_public.settings.hasOwnProperty( `unit_conversion_system_${ i }_fractions` ) ? wprmp_public.settings[`unit_conversion_system_${ i }_fractions`] : true;
				}

				// Check if we can set a quantity.
				let currentAmount = unitSystem.amount; // Default to original amount.
				let currentAmountString = unitSystem.amountString;
				let currentAmountParsed = false;
				let amountIsSingular = false;

				// Calulate the quantity based on the current servings.
				for ( let k = 0; k < unitSystem.amountParts.length; k++ ) {
					const amountPart = unitSystem.amountParts[ k ];
					const calculatedQuantity = amountPart.numberUnitQuantity * recipe.data.currentServingsParsed;

					if ( ! isNaN( calculatedQuantity ) && 0 < calculatedQuantity ) {
						// Use the first part to determine if the amount is singular and for the parsed amount.
						if ( 0 === k ) {
							amountIsSingular = calculatedQuantity <= 1;
							currentAmountParsed = calculatedQuantity;
						}
						
						// Replace this part with its formatted value.
						currentAmountString = currentAmountString.replace( `%wprm${k}%`, this.format( calculatedQuantity, allowFractions ) );
					}
				}

				// Make sure there is no %wprm0% placeholder left in currentAmountString.
				if ( false === /%wprm\d+%/g.test( currentAmountString ) ) {
					currentAmount = currentAmountString;
				}

				// Maybe just use the original amount.
				if ( revertOriginal ) {
					currentAmount = unitSystem.amount;
				} else {
					// If the amount is the same as the original, and we filled in an explicit value, is that value.
					if ( recipe.data.currentServingsParsed === recipe.data.originalServingsParsed && ! usingFallbackUnitSystem ) {
						currentAmount = unitSystem.amount;
					}
				}

				// Get unit.
				let currentUnit = unitSystem.unitParsed;

				if ( ! revertOriginal ) {
					// If the amount is the same as the original, and we filled in an explicit value, is that value.
					if ( recipe.data.currentServingsParsed === recipe.data.originalServingsParsed && ! usingFallbackUnitSystem ) {
						currentUnit = unitSystem.unitParsed;
					} else {
						const unitSingular = unitSystem.hasOwnProperty( 'unit_singular' ) ? unitSystem.unit_singular : false;
						const unitPlural = unitSystem.hasOwnProperty( 'unit_plural' ) ? unitSystem.unit_plural : false;

						currentUnit = unitSystem.unitParsed;
						if ( unitSingular && unitPlural ) {
							currentUnit = amountIsSingular ? unitSingular : unitPlural;
						}
					}
				}

				// Get name.
				let currentName = ingredient.name;
				let needsNameChange = false;

				if ( ! revertOriginal ) {
					const nameSingular = ingredient.hasOwnProperty( 'name_singular' ) ? ingredient.name_singular : false;
					const namePlural = ingredient.hasOwnProperty( 'name_plural' ) ? ingredient.name_plural : false;

					if ( nameSingular && namePlural ) {
						needsNameChange = true;
						currentName = amountIsSingular ? nameSingular : namePlural;
					}
				}

				// Set ingredient data.
				currentIngredient[ 'unit-system-' + i ] = {
					amount: currentAmount,
					amountParsed: currentAmountParsed,
					amountIsSingular,
					unit: currentUnit,
					name: currentName,
					needsNameChange,
				};
			}

			currentIngredients.push( currentIngredient );
		}

		return currentIngredients;
	},
	updateIngredientsDisplay( recipe ) {
		if ( ! recipe.data.hasOwnProperty( 'ingredientsElements' ) ) {
			window.WPRecipeMaker.managerPremiumIngredients.findIngredientsElements( recipe );
		}

		const containerId = recipe.data.hasOwnProperty( 'overrideContainerId' ) && false !== recipe.data.overrideContainerId ? recipe.data.overrideContainerId : recipe.id;
		const revertOriginal = recipe.data.currentServingsParsed === recipe.data.originalServingsParsed && recipe.data.currentSystem === recipe.data.originalSystem;
		const currentIngredients = window.WPRecipeMaker.managerPremiumIngredients.getCurrentIngredients( recipe );

		for ( let i = 0; i < recipe.data.ingredients.length; i++ ) {
			const ingredient = recipe.data.ingredients[ i ];
			const ingredientElements = recipe.data.ingredientsElements[ i ];
			const currentIngredient = currentIngredients[ i ];

			// Update all ingredient elements for this ingredient.
			for ( let ingredientElement of ingredientElements ) {
				// Loop over all amount elements.
				for ( let j = 0; j < ingredientElement.amounts.length; j++ ) {
					const amount = ingredientElement.amounts[ j ];
					let system = recipe.data.currentSystem;

					if ( ingredientElement.showingBothUnitSystems && 1 === j ) {
						system = system % 2 + 1; // Show the other unit system.
					}

					// Ingredient values for this unit system.
					const currentSystemValues = currentIngredient[ 'unit-system-' + system ];

					// Set amounts first.
					if ( revertOriginal ) {
						amount.elem.innerHTML = amount.original;
					} else {
						amount.elem.innerHTML = currentSystemValues.amount;
					}

					// Set units (if there is one).
					if ( ingredientElement.units.hasOwnProperty( j ) ) {
						if ( revertOriginal ) {
							ingredientElement.units[ j ].elem.innerHTML = ingredientElement.units[ j ].original;
						} else {
							ingredientElement.units[ j ].elem.innerHTML = currentSystemValues.unit;
						}
					}
				}

				// Set ingredient name for current unit system.
				if ( ingredientElement.name ) {
					if ( currentIngredient[ 'unit-system-' + recipe.data.currentSystem ].needsNameChange ) {
						ingredientElement.name.elem.innerHTML = currentIngredient[ 'unit-system-' + recipe.data.currentSystem ].name;
					} else {
						ingredientElement.name.elem.innerHTML = ingredientElement.name.original;
					}
				}
			}

			// Update any associated ingredients based on UID.
			if ( false !== ingredient.uid && 1 <= ingredientElements.length ) {
				// Check if there are any linked ingredients to update.
				const linkedIngredients = document.querySelectorAll( '.wprm-inline-ingredient-' + containerId + '-' + ingredient.uid + ', .wprm-recipe-instruction-ingredient-' + containerId + '-' + ingredient.uid );
			
				if ( 0 < linkedIngredients.length ) {
					// Construct text to use first.
					let ingredientString = '';
					let notesString = '';

					for ( let ingredientElement of ingredientElements ) {
						const ingredientClone = document.createElement( 'div' );
						ingredientClone.innerHTML = ingredientElement.elem.innerHTML;

						// Remove commas and hyphens from direct text nodes (notes identifier).
						Array.from(ingredientClone.childNodes).forEach(node => {
							if (node.nodeType === Node.TEXT_NODE) {
								node.textContent = node.textContent.replace(/[,-]/g, '');
							}
						});

						// Store and remove notes.
						const ingredientCloneNotes = ingredientClone.querySelector( '.wprm-recipe-ingredient-notes' );
						if ( ingredientCloneNotes ) {
							notesString = ingredientCloneNotes.innerText;
							ingredientCloneNotes.remove();
						}

						// Remove checkbox.
						const ingredientCloneCheckbox = ingredientClone.querySelector( '.wprm-checkbox-container' );
						if ( ingredientCloneCheckbox ) { ingredientCloneCheckbox.remove(); }

						// Remove ingredient image.
						const ingredientCloneImage = ingredientClone.querySelector( '.wprm-recipe-ingredient-image' );
						if ( ingredientCloneImage ) { ingredientCloneImage.remove(); }

						// Get and clean up remaining text.
						ingredientString = ingredientClone.innerText;
						ingredientString = ingredientString.replace( /\s\s+/g, ' ' );
						ingredientString = ingredientString.trim();

						// Found a string to use in this element, no need to look further.
						if ( ingredientString ) {
							break;
						}
					}

					if ( ingredientString ) {
						for ( let linkedIngredient of linkedIngredients ) {
							let linkedIngredientText = ingredientString;

							// Maybe include notes with linked ingredient.
							if ( notesString ) {
								const notesSeparator = linkedIngredient.dataset.hasOwnProperty( 'notesSeparator' ) ? linkedIngredient.dataset.notesSeparator : false;
								if ( false !== notesSeparator ) {
									// If surrounded by parentheses, remove.
									if ( notesString.startsWith( '(' ) && notesString.endsWith( ')' ) ) {
										notesString = notesString.substring( 1, notesString.length - 1 );
									}

									// Added notes to linked ingredient.
									switch ( notesSeparator ) {
										case 'comma':
											linkedIngredientText += ', ' + notesString;
											break;
										case 'dash':
											linkedIngredientText += ' - ' + notesString;
											break;
										case 'parentheses':
											linkedIngredientText += ' (' + notesString + ')';
											break;
										default:
											linkedIngredientText += ' ' + notesString;
									}
								}
							}

							// Maybe separator after linked ingredient.
							if ( linkedIngredient.dataset.hasOwnProperty( 'separator' ) ) {
								linkedIngredientText += linkedIngredient.dataset.separator;
							}

							linkedIngredient.innerText = linkedIngredientText;
						}
					}
				}
			}
		}

		// Maybe need to update adjustables (for example in unit name).
		window.WPRecipeMaker.quantities.findAdjustables( recipe );
	},
	findIngredientsElements( recipe ) {
		let ingredientsElements = [];

		// Go through all ingredients first.
		for ( let i = 0; i < recipe.data.ingredients.length; i++ ) {
			const ingredient = recipe.data.ingredients[ i ];

			for ( let system of Object.keys( ingredient.unit_systems ) ) {
				const unitSystem = ingredient.unit_systems[ system ];

				const amountStringParsed = window.WPRecipeMaker.managerPremiumIngredients.parseAmountString( unitSystem.amount, recipe.data.originalServingsParsed );

				recipe.data.ingredients[i].unit_systems[ system ] = {
					...recipe.data.ingredients[i].unit_systems[ system ],
					...amountStringParsed,
				}
			}

			// There could be multiple elements for one ingredient.
			ingredientsElements[ i ] = [];
		}

		// Containers to check for adjustables.
		const containerId = recipe.data.hasOwnProperty( 'overrideContainerId' ) && false !== recipe.data.overrideContainerId ? recipe.data.overrideContainerId : recipe.id;
		const containers = document.querySelectorAll( `#wprm-recipe-container-${ containerId }, .wprm-recipe-roundup-item-${ containerId }, .wprm-print-recipe-${ containerId }, .wprm-recipe-${ containerId }-ingredients-container, .wprm-recipe-${ containerId }-instructions-container` );

		for ( let container of containers ) {
			// Look for ingredients.
			const ingredientElems = container.querySelectorAll( '.wprm-recipe-ingredient' );

			for ( let i = 0; i < ingredientElems.length; i++ ) {
				const ingredientElem = ingredientElems[ i ];

				if ( ! ingredientElem.dataset.hasOwnProperty( 'wprmParsed' ) ) {
					let ingredient = {
						elem: ingredientElem,
						showingBothUnitSystems: false,
						amounts: [],
						units: [],
						name: false,
						notes: false,
					}
					
					// Check if showing both unit systems. If class is present, we're showing both.
					if ( ingredientElem.querySelector( '.wprm-recipe-ingredient-unit-system' ) ) {
						ingredient.showingBothUnitSystems = true;
					}

					// Find amounts. Could be multiple if displaying multiple unit systems.
					const amountElems = ingredientElem.querySelectorAll( '.wprm-recipe-ingredient-amount' );

					for ( let amountElem of amountElems ) {
						ingredient.amounts.push( {
							elem: amountElem,
							original: amountElem.innerHTML,
							unitQuantity: parseQuantity( amountElem.innerText ) / recipe.data.originalServingsParsed,
						} );
					}

					// Find units. Could be multiple if displaying multiple unit systems.
					const unitElems = ingredientElem.querySelectorAll( '.wprm-recipe-ingredient-unit' );

					for ( let unitElem of unitElems ) {
						ingredient.units.push( {
							elem: unitElem,
							original: unitElem.innerHTML,
						} );
					}

					// Find name.
					const nameElem = ingredientElem.querySelector( '.wprm-recipe-ingredient-name' );

					if ( nameElem ) {
						ingredient.name = {
							elem: nameElem,
							original: nameElem.innerHTML,
						};
					}

					// Find notes.
					const notesElem = ingredientElem.querySelector( '.wprm-recipe-ingredient-notes' );

					if ( notesElem ) {
						ingredient.notes = {
							elem: notesElem,
							original: notesElem.innerHTML,
						};
					}

					// Mark element as parsed and add to array.
					ingredientElem.dataset.wprmParsed = true;
					if ( ingredientsElements.hasOwnProperty( i ) ) {
						ingredientsElements[ i ].push( ingredient );
					}
				}
			}
		}

		window.WPRecipeMaker.manager.changeRecipeData( recipe.id, {
			ingredientsElements,
		} );
	},
	parseAmountString( amountString, servings ) {
		// Replace HTML entities added by wp_json_encode.
		amountString = amountString.replace( /&quot;/g, '"' );
		amountString = amountString.replace( /&#39;/g, "'" );
		amountString = amountString.replace( /&amp;/g, '&' );

		// Find all numbers in the amount.
		let numbers = false;

		if ( /^\.\d+\s*$/.test( amountString ) ) {
			// Check for special case: .5
			numbers = [ amountString ];
		} else {
			const fractions = '\u00BC\u00BD\u00BE\u2150\u2151\u2152\u2153\u2154\u2155\u2156\u2157\u2158\u2159\u215A\u215B\u215C\u215D\u215E';
			const number_regex = '[\\d'+fractions+']([\\d'+fractions+'.,\\/\\s]*[\\d'+fractions+'])?';

			const matches = amountString.match( new RegExp( number_regex, 'g' ) );

			if ( matches ) {
				numbers = matches;
			}
		}

		let amountParts = [];

		// Replace parts with placeholders.
		if ( numbers ) {
			for ( let i = 0; i < numbers.length; i++ ) {
				const number = numbers[ i ];
				amountString = amountString.replace( number, `%wprmtemporaryplaceholder%` ); // Only replaces first occurrence. Don't use numbers in this placeholder to prevent issues with next replacements.

				const numberParsed = parseQuantity( number );
				amountParts.push(
					{
						number,
						numberParsed,
						numberUnitQuantity: numberParsed / servings,
					}
				);
			}

			// Now replace %wprmtemporaryplaceholder% with %wprm0%, %wprm1%, etc.
			for ( let i = 0; i < numbers.length; i++ ) {
				amountString = amountString.replace( '%wprmtemporaryplaceholder%', `%wprm${i}%` ); // Only replaces first occurrence.
			}
		}

		return {
			amountString,
			amountParts,
		};
	},
	format( quantity, allowFractions = true ) {
		return formatQuantity( quantity, wprmp_public.settings.adjustable_servings_round_to_decimals, allowFractions );
	},
}

ready(() => {
	window.WPRecipeMaker.managerPremiumIngredients.load();
});

function ready( fn ) {
    if (document.readyState != 'loading'){
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}