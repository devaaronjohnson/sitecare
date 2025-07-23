import React, { Component } from 'react';
import AsyncSelect from 'react-select/async';

import { __wprm } from 'Shared/Translations';
import Api from 'Shared/Api';

export default class SelectProduct extends Component {
    getOptions(input) {
        if (!input) {
			return Promise.resolve({ options: [] });
        }

		return Api.product.search(input).then((data) => {
            if ( data ) {
                return data.products;
            } else {
                return [];
            }
        });
    }

    render() {
        return (
            <AsyncSelect
                placeholder={ __wprm( 'Search for products' ) }
                value={this.props.value}
                onChange={this.props.onValueChange}
                getOptionValue={({id}) => id}
                getOptionLabel={({text}) => text} 
                loadOptions={this.getOptions.bind(this)}
                noOptionsMessage={() => __wprm( 'No products found' ) }
                clearable={false}
            />
        );
    }
}
