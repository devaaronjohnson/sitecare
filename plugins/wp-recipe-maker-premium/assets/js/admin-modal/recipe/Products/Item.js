import React from 'react';

import striptags from 'striptags';

import Icon from 'Shared/Icon';
import { __wprm } from 'Shared/Translations';

import Loader from 'Shared/Loader';
import FieldDropdown from 'Modal/fields/FieldDropdown';
import FieldRichText from 'Modal/fields/FieldRichText';

const Item = (props) => {
    const { item, isUpdating } = props;

    let originalItem = `${item.amount} ${ item.hasOwnProperty('unit') ? item.unit : '' }`.trim();
    originalItem = `${originalItem} ${item.name}`.trim();

    if ( item.notes ) {
        originalItem += ` (${item.notes})`;
    }

    console.log( item );

    return (
        <tr className="wprm-admin-modal-field-product-container">
            <td>
                { striptags( originalItem ) }
            </td>
            <td>
                {
                    isUpdating
                    ?
                    <Loader />
                    :
                    <div className="wprm-admin-modal-field-product">
                        <Icon
                            type="pencil"
                            title={ __wprm( 'Change Product' ) }
                            onClick={() => {
                                WPRM_Modal.open( 'product', {
                                    label: item.name, // TODO
                                    taxonomy: 'ingredient', // TODO
                                    term: 0, // TODO
                                    product: item.product,
                                    saveCallback: () => {},
                                } );
                            }}
                        />
                        {
                            item.product
                            &&
                            <a href={ item.product.url } target="_blank">{ item.product.name } (#{ item.product.id })</a>
                        }
                    </div>
                }
            </td>
            <td>...</td>
        </tr>
    );
}
export default Item;