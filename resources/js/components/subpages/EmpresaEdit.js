import React, {Component} from 'react';
import { Redirect, withRouter } from 'react-router-dom';
import Select from 'react-select';
import PageHeading from '../elements/PageHeading';
import FlashMessages from '../elements/FlashMessages';
import User from '../services/User';
import Empresas from '../services/Empresas';
import Apps from '../services/Apps';

class EmpresaEdit extends Component {
    constructor (props) {
        super(props);
        this.state = {
            success: [],
            error: [],
            formSubmitting: false,
            appsOptions: [],
            usersOptions: [],
            regform: {
                name: '',
                logo: '',
                users: [],
                apps: []
            },
            editId: false
        };

        this.getEmpresaData = this.getEmpresaData.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleName = this.handleName.bind(this);
        this.handleLogo = this.handleLogo.bind(this);
        this.handleUsers = this.handleUsers.bind(this);
        this.handleApps = this.handleApps.bind(this);
    }

    componentDidMount () {
        let {params} = this.props.match;
        const self = this;
        if(params.editId){
            this.setState({editId: params.editId, formSubmitting: true});
            let timer = setTimeout(() => {
                self.getEmpresaData();
                self.setUsersList();
                self.setAppsList();
            }, 1000);
        }
    }

    async setUsersList () {
        const params = {role: 1};
        let getUsersList = await User.getUserFilter(params);
        if(getUsersList.status == true && getUsersList.msg.data.length > 0){
            let newUsersArr = getUsersList.msg.data.map((usr, index) => {
                return {value: usr.id, label: usr.name+" ("+usr.email+")"};
            });

            this.setState({usersOptions: newUsersArr});
        }
    }

    async setAppsList () {
        let getAppsList = await Apps.getAppsList();
        if(getAppsList.status == true && getAppsList.msg.data.length > 0){
            let newAppsArr = getAppsList.msg.data.map((app, index) => {
                return {value: app.id, label: app.name};
            });

            this.setState({appsOptions: newAppsArr});
        }
    }

    async getEmpresaData () {
        let empresaInfo = await Empresas.getEmpresaInfo(this.state.editId);
        if(empresaInfo.status == true){
            if(empresaInfo.msg.data){
                this.setState({formSubmitting: false});
                this.setState( prevState => ({
                    regform : {
                        ...prevState.regform, 
                        name: empresaInfo.msg.data.name,
                        users: empresaInfo.msg.data.users.map((usr, idx) => {
                            return usr.id;
                        }),
                        apps: empresaInfo.msg.data.apps.map((app, idx) => {
                            return app.id;
                        })
                    }
                }));
            }
        }
    }

    handleName (e) {
        let valueName = e.target.value;
        this.setState( prevState => ({
            regform : {
                ...prevState.regform, name: valueName
            }
        }));
    }

    handleLogo (e) {
        let valueFile = e.target.files[0];
        this.setState( prevState => ({
            regform : {
                ...prevState.regform, logo: valueFile
            }
        }));
    }

    handleUsers (e) {
        this.setState( prevState => ({
            regform : {
                ...prevState.regform, users: e ? e.map(x => x.value) : []
            }
        }));
    }

    handleApps (e) {
        this.setState( prevState => ({
            regform : {
                ...prevState.regform, apps: e ? e.map(x => x.value) : []
            }
        }));
    }

    async handleSubmit (e) {
        e.preventDefault();
        this.setState({formSubmitting: true, error: [], success: []});
        let empresaForm = this.state.regform;
        const self = this;
        const formData = new FormData();
        formData.append("name", empresaForm.name);
        formData.append("logo", empresaForm.logo);
        formData.append("users", empresaForm.users);
        formData.append("apps", empresaForm.apps);

        const editEmpresaReq = await Empresas.doEditEmpresa(formData, this.state.editId);
        this.setState({formSubmitting: false});

        if(editEmpresaReq.status == true){
            this.setState({error: [], 
                           success: editEmpresaReq.msg,
                           error: []
                        });
        }else{
            this.setState({error: editEmpresaReq.msg, success: []});
        }
    }

    render () {
        let {params} = this.props.match;
        return (params.editId) ? 
            <div className="container-fluid">
                <PageHeading headingtxt="Editar Empresa" />

                <div className="row">
                    <div className="col-xs-8 col-lg-7">
                        <div className="card shadow mb-4">
                            <div className="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 className="m-0 font-weight-bold text-primary">Por favor ingrese los siguientes datos</h6>
                            </div>
                            <div className="card-body">
                                <form id="edit-empresa-frm" className="user" onSubmit={this.handleSubmit}>
                                    <div className="form-group">
                                        <input type="text" className="form-control form-control-user" id="name" name="name" placeholder="Nombre Empresa" value={this.state.regform.name} required onChange={this.handleName} />
                                    </div>
                                    <div className="form-group">
                                      <input
                                        type="file"
                                        name="logo"
                                        onChange={this.handleLogo}
                                        />
                                    </div>
                                    <div className="form-group">
                                        <label>Usuarios que puede manejar esta empresa</label>
                                        <Select
                                            isMulti
                                            name="users"
                                            value={this.state.usersOptions.filter(item => this.state.regform.users.includes(item.value))}
                                            options={this.state.usersOptions}
                                            className="basic-multi-select"
                                            classNamePrefix="select"
                                            onChange={this.handleUsers}
                                        />
                                    </div>
                                    <div className="form-group">
                                        <label>Apps que se va a recolectar info para esta empresa</label>
                                        <Select
                                            isMulti
                                            name="apps"
                                            value={this.state.appsOptions.filter(item => this.state.regform.apps.includes(item.value))}
                                            options={this.state.appsOptions}
                                            className="basic-multi-select"
                                            classNamePrefix="select"
                                            onChange={this.handleApps}
                                        />
                                    </div>
                                    <button disabled={this.state.formSubmitting} type="submit" name="editEmpresaBtn" className="btn btn-primary btn-user btn-block">{this.state.formSubmitting ? "Processing..." : "Editar"}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-4 col-lg-5">
                        { this.state.error.length > 0 ?
                            <FlashMessages messages={this.state.error} type="danger" /> :
                            ''
                        }

                        { this.state.success.length > 0 ?
                            <FlashMessages messages={this.state.success} type="success" /> :
                            ''
                        }
                    </div>
                </div>
            </div>
        :
            <Redirect to={{pathname: "/" }} />
        ;
    }
}

export default withRouter(EmpresaEdit);