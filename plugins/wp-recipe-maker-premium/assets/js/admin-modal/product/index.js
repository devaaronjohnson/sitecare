import React, { Component, Fragment } from 'react';

import '../../../css/admin/modal/product.scss';

import Api from 'Shared/Api';
import { __wprm } from 'Shared/Translations';

import Header from 'Modal/general/Header';
import Footer from 'Modal/general/Footer';

import FieldContainer from 'Modal/fields/FieldContainer';
import FieldDropdown from 'Modal/fields/FieldDropdown';
import FieldText from 'Modal/fields/FieldText';

import SelectProduct from './SelectProduct';

const emptyProduct = {
    plugin: 'woocommerce',
    id: 0,
    name: '',
};

export default class Menu extends Component {
    constructor(props) {
        super(props);

        let product = emptyProduct;

        let editing = false;
        if ( props.args.hasOwnProperty( 'product' ) && props.args.product ) {
            editing = true;
            product = JSON.parse( JSON.stringify( props.args.product ) );
        }

        this.state = {
            editing,
            label: props.args.hasOwnProperty( 'label' ) ? props.args.label : false,
            product,
            originalProduct: JSON.parse( JSON.stringify( product ) ),
            savingChanges: false,
        };

        this.changesMade = this.changesMade.bind(this);
        this.saveChanges = this.saveChanges.bind(this);
    }

    saveChanges() {
        this.setState({
            savingChanges: true,
        }, () => {
            Api.manage.updateTaxonomyMeta( this.props.args.taxonomy, this.props.args.term, { product: this.state.product } ).then(() => {
                this.setState({
                    savingChanges: false,
                },() => {
                    if ( 'function' === typeof this.props.args.saveCallback ) {
                        this.props.args.saveCallback( this.state.product );
                    }
                    this.props.maybeCloseModal();
                });
            });
        })
    }

    allowCloseModal() {
        return ! this.state.savingChanges && ( ! this.changesMade() || confirm( __wprm( 'Are you sure you want to close without saving changes?' ) ) );
    }

    changesMade() {
        return JSON.stringify( this.state.product ) !== JSON.stringify( this.state.originalProduct );
    }

    render() {
        return (
            <Fragment>
                <Header
                    onCloseModal={ this.props.maybeCloseModal }
                >
                    {
                        this.state.editing
                        ?
                        `${ __wprm( 'Editing Product' ) }${ this.state.label ? ` - ${ this.state.label }` : '' }`
                        :
                        `${ __wprm( 'Setting Product' ) }${ this.state.label ? ` - ${ this.state.label }` : '' }`
                    }
                </Header>
                <div className="wprm-admin-modal-product-container">
                    <FieldContainer id="search" label={ __wprm( 'Search' ) }>
                        <SelectProduct
                            value={ this.state.product.id }
                            onValueChange={(product) => {
                                let newProduct = JSON.parse( JSON.stringify( this.state.product ) );
                                newProduct.id = product.id;
                                newProduct.name = product.name;

                                this.setState({ product: newProduct });
                            }}
                        />
                    </FieldContainer>
                    <FieldContainer id="id" label={ __wprm( 'Product ID' ) }>
                        <p>
                            {
                                0 < this.state.product.id
                                ?
                                this.state.product.id
                                :
                                __wprm( 'No product set yet' )
                            }
                        </p>
                    </FieldContainer>
                    <FieldContainer id="name" label={ __wprm( 'Product Name' ) }>
                        <p>
                            {
                                0 < this.state.product.id
                                ?
                                this.state.product.name
                                :
                                __wprm( 'No product set yet' )
                            }
                        </p>
                    </FieldContainer>
                </div>
                <Footer
                    savingChanges={ this.state.savingChanges }
                >
                    {
                        0 < this.state.product.id
                        &&
                        <button
                            className="button"
                            onClick={ () => {
                                this.setState({
                                    product: emptyProduct,
                                });
                            } }
                        >
                            { __wprm( 'Unset Product' ) }
                        </button>
                    }
                    <button
                        className="button button-primary"
                        onClick={ this.saveChanges }
                        disabled={ ! this.changesMade() }
                    >
                        { __wprm( 'Save' ) }
                    </button>
                </Footer>
            </Fragment>
        );
    }
}