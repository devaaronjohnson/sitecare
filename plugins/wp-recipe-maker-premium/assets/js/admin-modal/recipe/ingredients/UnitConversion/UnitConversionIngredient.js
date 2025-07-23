import React from 'react';

import striptags from 'striptags';

import { __wprm } from 'Shared/Translations';
import Loader from 'Shared/Loader';
import FieldDropdown from 'Modal/fields/FieldDropdown';
import FieldRichText from 'Modal/fields/FieldRichText';

const unitConversionOptions = ( convertedUnitSystem ) => {
    let options = [
        {
            label: __wprm( 'Convert' ),
            options: [
                {
                    value: 'none',
                    label: __wprm( 'Keep Unit' ),
                },
                {
                    value: 'automatic',
                    label: __wprm( 'Automatically' ),
                }
            ],
        }
    ];

    let weightOptions = [];
    wprm_admin_modal.unit_conversion.systems[ convertedUnitSystem ].weight.map( (unit) => {
        weightOptions.push({
            value: unit,
            label: wprm_admin_modal.unit_conversion.units.data[ unit ].label,
        })
    });

    if ( 0 < weightOptions.length ) {
        options.push({
            label: __wprm( 'Weight Units' ),
            options: weightOptions,
        })
    }

    let volumeOptions = [];
    wprm_admin_modal.unit_conversion.systems[ convertedUnitSystem ].volume.map( (unit) => {
        volumeOptions.push({
            value: unit,
            label: wprm_admin_modal.unit_conversion.units.data[ unit ].label,
        })
    });

    if ( 0 < volumeOptions.length ) {
        options.push({
            label: __wprm( 'Volume Units' ),
            options: volumeOptions,
        })
    }

    return options;
}

const UnitConversionIngredient = (props) => {
    const { ingredient, isConverting, method } = props;
    let converted = ingredient.converted ? ingredient.converted : false;

    // Check if converted is set up correctly.
    if ( typeof converted !== 'object' || ! converted.hasOwnProperty( 2 ) || ! converted[ 2 ].hasOwnProperty( 'amount' ) || ! converted[ 2 ].hasOwnProperty( 'unit' ) ) {
        converted = false;
    }

    if ( ! converted ) {
        converted = { 2: { amount: '', unit: '' } };
    }

    const methodOptions = unitConversionOptions( props.convertedUnitSystem );

    let originalIngredient = `${ingredient.amount} ${ingredient.unit}`.trim();
    originalIngredient = `${originalIngredient} ${ingredient.name}`.trim();

    if ( ingredient.notes ) {
        originalIngredient += ` (${ingredient.notes})`;
    }

    return (
        <tr>
            <td>
                <FieldDropdown
                    isDisabled={ isConverting }
                    options={ methodOptions }
                    placeholder={ __wprm( 'Convert...' ) }
                    value={ method }
                    onChange={ (method) => {
                        props.onMethodChange( method );
                    }}
                    width={ 150 }
                />
            </td>
            <td
                style={ 'failed' === method ? { color: 'darkred' } : null }
            >
                {
                    isConverting
                    ?
                    <Loader />
                    :
                    <div className="wprm-admin-modal-field-ingredient-unit-conversion-fields">
                        <FieldRichText
                            singleLine
                            value={ '' + converted[2].amount }
                            onChange={(amount) => {
                                let newConverted = converted;
                                newConverted[2].amount = amount;
                                props.onConvertedChange(newConverted);
                            }}
                        />
                        <FieldRichText
                            singleLine
                            value={ '' + converted[2].unit }
                            onChange={(unit) => {
                                let newConverted = converted;
                                newConverted[2].unit = unit;
                                props.onConvertedChange(newConverted);
                            }}
                        />
                    </div>
                }
            </td>
            <td>{ striptags( originalIngredient ) }</td>
        </tr>
    );
}
export default UnitConversionIngredient;