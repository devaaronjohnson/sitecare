import React, { Component } from 'react';

import FieldContainer from 'Modal/fields/FieldContainer';
import FieldRadio from 'Modal/fields/FieldRadio';
import { __wprm } from 'Shared/Translations';

import Item from './Item';
import Api from 'Shared/Api';

import '../../../../css/admin/modal/recipe/products.scss';

export default class Products extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isUpdating: false,
        }
    }

    componentDidMount() {
        if ( wprm_admin.addons.elite ) {
            this.updateProducts();
        }
    }

    updateProducts() {
        let getProductsFor = {};

        for ( let i = 0; i < this.props.items.length; i++ ) {
            const item = this.props.items[ i ];

            if ( ! item.hasOwnProperty( 'product' ) || false === item.product ) {
                getProductsFor[ i ] = {
                    name: item.name,
                }
            }
        }

        if ( 0 < Object.keys( getProductsFor ).length ) {
            const updatingIndexes = Object.keys( getProductsFor ).map( (index) => parseInt( index ) );

            this.setState({
                isUpdating: updatingIndexes,
            }, () => {
                Api.product.getAll( this.props.taxonomy, getProductsFor ).then((data) => {
                    console.log( 'getAll', data );
                    if ( data && data.hasOwnProperty( 'products' ) ) {
                        let newItems = JSON.parse( JSON.stringify( this.props.items ) );
    
                        for ( let index in data.products ) {
                            newItems[ parseInt( index ) ].product = data.products[ index ];
                        }
    
                        // Update items and state.
                        this.props.onItemsChange(newItems);
                    }

                    this.setState({
                        isUpdating: false,
                    });
                });
            });
        }
    }

    render() {
        const { items } = this.props;

        console.log( 'items', items );

        if ( ! items.length ) {
            return (
                <p>{ __wprm( 'Nothing to add products to yet.' ) }</p>
            );
        }

        return (
            <div className="wprm-admin-modal-field-products-container">
                <table
                    className="wprm-admin-modal-field-products"
                >
                    <thead>
                    <tr>
                        <th>{ __wprm( 'In recipe' ) }</th>
                        <th>{ __wprm( 'Product' ) }</th>
                        <th>{ __wprm( 'Amount needed' ) }</th>
                    </tr>
                    </thead>
                    <tbody>
                    {
                        items.map((item, index) => {
                            return (
                                <Item
                                    item={ item }
                                    onItemChange={ ( changes ) => {
                                        let items = JSON.parse( JSON.stringify( this.props.items ) );

                                        newItems[ index ] = {
                                            ...item,
                                            ...changes,
                                        };

                                        this.props.onItemsChange( newItems );
                                    } }
                                    isUpdating={ this.state.isUpdating && this.state.isUpdating.includes( index ) }
                                    key={ index }
                                />
                            )
                        })
                    }
                    </tbody>
                </table>
            </div>
        );
    }
}