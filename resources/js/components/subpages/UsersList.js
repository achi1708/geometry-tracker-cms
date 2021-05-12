import React, {Component} from 'react';
import PageHeading from '../elements/PageHeading';
import FlashMessages from '../elements/FlashMessages';
import LoadingSection from '../elements/LoadingSection';
import User from '../services/User';
import MUIDataTable from 'mui-datatables';
import {MuiThemeProvider, createMuiTheme} from '@material-ui/core';

class UsersList extends Component {
    constructor (props) {
        super(props);

        this.state = {
            datatable: {
                page: 1,
                count: 0,
                rowsPerPage: 15,
                setOrder: {},
                data: [['...Cargando información...']],
                columns: User.userDatatableColumns
            },
            isLoading: false
        };

        this.getUserReq = this.getUserReq.bind(this);
        this.setUsersDatatable = this.setUsersDatatable.bind(this);
        this.setOrderDatatable = this.setOrderDatatable.bind(this);
        this.setPageDatatable = this.setPageDatatable.bind(this);
    }

    componentDidMount () {
        if(this.state.datatable.count == 0){
            this.getUserReq();
        }
    }

    async getUserReq () {
        if(this.state.datatable.count == 0 
            || (this.state.datatable.count > 0 && (this.state.datatable.count / this.state.datatable.rowsPerPage) >= this.state.datatable.page)
            || (this.state.datatable.count > 0 && this.state.datatable.count < this.state.datatable.rowsPerPage && this.state.datatable.page == 1)){
            this.setState({isLoading: true});
            let preUsersList = await User.getUserListAdm(this.state.datatable.page, this.state.datatable.setOrder);
            if(preUsersList.status == true){
                this.setUsersDatatable(preUsersList.msg);
            }
        }
        
        
    }

    setUsersDatatable (userList) {
        if(userList.data.length && userList.meta){
            let dataFinal = [];
            dataFinal = userList.data.map((user, index) => {
                return {name: user.name,
                        email: user.email,
                        role: user.role_desc,
                        edit: (user.id != this.props.userdata.id) ? user.id : false
                       };
            });

            this.setState({isLoading: false});
            this.setState( prevState => ({
                datatable : {
                    ...prevState.datatable, 
                    page: userList.meta.current_page,
                    count: userList.meta.total,
                    rowsPerPage: userList.meta.per_page,
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
            this.getUserReq();
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
            this.getUserReq();
        }.bind(this));
    }
    
    render () {
        const { data, page, count, rowsPerPage, sortOrder } = this.state.datatable;
        const {isLoading} = this.state;
        

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
            <div className="container-fluid">
                <PageHeading headingtxt="Usuarios" />

                <div className="card shadow mb-4 p-3">
                    {(isLoading) ? 
                    <LoadingSection />
                    : ''}
                    <MuiThemeProvider theme={theme}>
                        <MUIDataTable
                            data={data}
                            columns={this.state.datatable.columns}
                            options={options}
                        />
                    </MuiThemeProvider>
                </div>
            </div>
        )
    }
}

export default UsersList;