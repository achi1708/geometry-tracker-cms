import React, {Component} from 'react';
import { Switch, Route, withRouter } from 'react-router-dom';
import LoadingSection from './../LoadingSection';
import FacebookBtn from './FacebookBtn';
import FlashMessages from './../FlashMessages';
import Facebook from './../../services/Facebook';
import MUIDataTable from 'mui-datatables';
import {MuiThemeProvider, createMuiTheme} from '@material-ui/core';

class FacebookPageInsights extends Component {
    constructor (props) {
        super(props);

        this.state = {
            datatable: {
                page: 1,
                count: 0,
                rowsPerPage: 50,
                setOrder: {},
                data: [['...Cargando información...']],
                columns: Facebook.pageInsightsDatatableColumns
            },
            isLoading: false,
            msgFlashReadFacebook: [],
            typeMsgFlashReadFb: ''

        };

        this.getData = this.getData.bind(this);
        this.setDataDatatable = this.setDataDatatable.bind(this);
        this.setOrderDatatable = this.setOrderDatatable.bind(this);
        this.setPageDatatable = this.setPageDatatable.bind(this);
        this.makeFacebookBtn = this.makeFacebookBtn.bind(this);
        this.readFacebookPageInsights = this.readFacebookPageInsights.bind(this);
        this.reloadDatatable = this.reloadDatatable.bind(this);
        
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

    reloadDatatable (){
        this.setState({isLoading: true});
        this.setState( prevState => ({
            datatable : {
                ...prevState.datatable, 
                page: 1,
                count: 0,
                rowsPerPage: 50,
                setOrder: {},
                data: [['...Cargando información...']]
            },
            msgFlashReadFacebook: [],
            typeMsgFlashReadFb: ''
        }), function () {
            this.getData();
        }.bind(this));
    }

    async getData () {
        if(this.state.datatable.count == 0 
            || (this.state.datatable.count > 0 && (this.state.datatable.count / this.state.datatable.rowsPerPage) >= this.state.datatable.page)
            || (this.state.datatable.count > 0 && this.state.datatable.count < this.state.datatable.rowsPerPage && this.state.datatable.page == 1)){
            this.setState({isLoading: true});
            let pageInsightsList = await Facebook.getPageInsightsListAdm(this.state.datatable.page, this.state.datatable.setOrder, this.props.empresaid);
            if(pageInsightsList.status == true){
                this.setDataDatatable(pageInsightsList.msg);
            }
        }
        
        
    }

    setDataDatatable (pageInsightsList) {
        if(pageInsightsList.data.length && pageInsightsList.meta){
            let dataFinal = [];
            dataFinal = pageInsightsList.data.map((post, index) => {
                return {metric: post.metric,
                        metric_date: post.metric_date,
                        metric_value: post.metric_value
                       };
            });

            this.setState({isLoading: false});
            this.setState( prevState => ({
                datatable : {
                    ...prevState.datatable, 
                    page: pageInsightsList.meta.current_page,
                    count: pageInsightsList.meta.total,
                    rowsPerPage: pageInsightsList.meta.per_page,
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
                <FacebookBtn mainreadinfo={this.readFacebookPageInsights} empresadata={this.props.empresadata} />
            );
        }else{
            return '0';
        }
    }

    async readFacebookPageInsights(fat, ftt, fuid) {
        console.log(fat);
        console.log(ftt);
        console.log(fuid);
        this.setState({isLoading: true, msgFlashReadFacebook: [], typeMsgFlashReadFb: ''},
        async function () {
            const self = this;
            const params = {fat, ftt, fuid, emp: this.props.empresaid, process: 'page_insights'};
            let req = await Facebook.readFacebookInfo(params);
            if(req.status == true) {
                const {data} = req.resp_data;
                if(data.msg == 'PROCESO_OK'){
                    this.setState({
                        msgFlashReadFacebook: [data.msg_extra],
                        typeMsgFlashReadFb: 'success'
                    });

                    let timer = setTimeout(() => self.reloadDatatable(), 1500);
                }
                
            }else{
                this.setState({
                    msgFlashReadFacebook: req.msg,
                    typeMsgFlashReadFb: 'danger'
                });
            }
            this.setState({isLoading: false});
        }.bind(this));
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
                { this.state.msgFlashReadFacebook.length > 0 ?
                    <FlashMessages messages={this.state.msgFlashReadFacebook} type={this.state.typeMsgFlashReadFb} /> :
                    ''
                }
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

export default withRouter(FacebookPageInsights);