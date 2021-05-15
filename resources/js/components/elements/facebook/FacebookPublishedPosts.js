import React, {Component} from 'react';
import { Switch, Route, withRouter } from 'react-router-dom';
import LoadingSection from './../LoadingSection';
import FacebookBtn from './FacebookBtn';
import Facebook from './../../services/Facebook';
import MUIDataTable from 'mui-datatables';
import {MuiThemeProvider, createMuiTheme} from '@material-ui/core';

class FacebookPublishedPosts extends Component {
    constructor (props) {
        super(props);

        this.state = {
            datatable: {
                page: 1,
                count: 0,
                rowsPerPage: 50,
                setOrder: {},
                data: [['...Cargando información...']],
                columns: Facebook.publishedPostsDatatableColumns
            },
            isLoading: false
        };

        this.getData = this.getData.bind(this);
        this.setDataDatatable = this.setDataDatatable.bind(this);
        this.setOrderDatatable = this.setOrderDatatable.bind(this);
        this.setPageDatatable = this.setPageDatatable.bind(this);
        this.makeFacebookBtn = this.makeFacebookBtn.bind(this);
        this.readFacebookPublishedPosts = this.readFacebookPublishedPosts.bind(this);
    }

    componentDidMount () {
        if(this.state.datatable.count == 0 && this.props.empresaid){
            this.getData();
        }
    }

    componentDidUpdate (prevProps) {
        if(this.props.empresaid !== prevProps.empresaid && this.state.datatable.count == 0){
            this.getData();
        }
    }

    async getData () {
        if(this.state.datatable.count == 0 
            || (this.state.datatable.count > 0 && (this.state.datatable.count / this.state.datatable.rowsPerPage) >= this.state.datatable.page)
            || (this.state.datatable.count > 0 && this.state.datatable.count < this.state.datatable.rowsPerPage && this.state.datatable.page == 1)){
            this.setState({isLoading: true});
            let publishedPostsList = await Facebook.getPublishedPostsListAdm(this.state.datatable.page, this.state.datatable.setOrder, this.props.empresaid);
            if(publishedPostsList.status == true){
                this.setDataDatatable(publishedPostsList.msg);
            }
        }
        
        
    }

    setDataDatatable (publishedPostsList) {
        if(publishedPostsList.data.length && publishedPostsList.meta){
            let dataFinal = [];
            dataFinal = publishedPostsList.data.map((post, index) => {
                return {imagen: post.picture,
                        mensaje: post.message,
                        expirado: post.expired,
                        oculto: post.hidden,
                        popular: post.popular,
                        publicado: post.published,
                        tags: post.tags,
                        insights: post.insights,
                        fecha_creacion: post.created_time
                       };
            });

            this.setState({isLoading: false});
            this.setState( prevState => ({
                datatable : {
                    ...prevState.datatable, 
                    page: publishedPostsList.meta.current_page,
                    count: publishedPostsList.meta.total,
                    rowsPerPage: publishedPostsList.meta.per_page,
                    data: dataFinal
                }
            }));
        }
    }

    setOrderDatatable (setOrder) {
        this.setState({isLoading: true});
        this.setState( prevState => ({
            datatable : {
                ...prevState.datatable, 
                setOrder: setOrder
            }
        }), function () {
            this.getData();
        }.bind(this));
    }

    setPageDatatable (setPage) {
        this.setState({isLoading: true});
        this.setState( prevState => ({
            datatable : {
                ...prevState.datatable, 
                page: (setPage + 1)
            }
        }), function () {
            this.getData();
        }.bind(this));
    }

    makeFacebookBtn () {
        if(this.props.empresaid != false && this.props.empresadata.name != ''){
            return (
                <FacebookBtn mainreadinfo={this.readFacebookPublishedPosts} empresadata={this.props.empresadata} />
            );
        }else{
            return '0';
        }
    }

    readFacebookPublishedPosts() {
        
    }

    render () {
        const { data, page, count, rowsPerPage, sortOrder } = this.state.datatable;
        const {isLoading} = this.state;
        const fbBtn = this.makeFacebookBtn();
        

        const options = {
            filter: false,
            download: false,
            search: false,
            print: false,
            selectableRows: 'none',
            responsive: 'vertical',
            serverSide: true,
            count: count,
            rowsPerPage: rowsPerPage,
            rowsPerPageOptions: [],
            sortOrder: sortOrder,
            onTableChange: (action, tableState) => {
                console.log(action, tableState);
        
                // a developer could react to change on an action basis or
                // examine the state as a whole and do whatever they want
        
                switch (action) {
                    case 'changePage':
                    //this.changePage(tableState.page, tableState.sortOrder);
                    this.setPageDatatable(tableState.page);
                    break;
                    case 'sort':
                    //this.sort(tableState.page, tableState.sortOrder);
                    this.setOrderDatatable(tableState.sortOrder);
                    break;
                    default:
                    console.log('action not handled.');
                    break;
                }
            },
        };

        const theme = createMuiTheme({
            palette: {type: 'light'},
            typography: {useNextVariants: true},
            overrides: {
                MuiPaper: {
                    root: {
                        boxShadow: 'none !important'
                    }
                },
                MuiTableCell: {
                    head: {
                      fontFamily: '"Nunito"',
                    },
                    body: {
                        padding: '5px 16px',
                        fontFamily: '"Nunito"',
                    },
                },
            }
        });

        return (
            <div className="card shadow mb-4 p-3">
                {(isLoading) ? 
                <LoadingSection />
                : ''}
                {fbBtn}
                <MuiThemeProvider theme={theme}>
                    <MUIDataTable
                        data={data}
                        columns={this.state.datatable.columns}
                        options={options}
                    />
                </MuiThemeProvider>
            </div>
        )
    }
}

export default withRouter(FacebookPublishedPosts);