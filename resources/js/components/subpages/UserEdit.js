import React, {Component} from 'react';
import { Redirect, withRouter } from 'react-router-dom';
import Select from 'react-select';
import PageHeading from '../elements/PageHeading';
import FlashMessages from '../elements/FlashMessages';
import User from '../services/User';
import Empresas from '../services/Empresas';

class UserEdit extends Component {
    constructor (props) {
        super(props);
        this.state = {
            success: [],
            error: [],
            formSubmitting: false,
            empresasOptions: [],
            regform: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                role: '',
                role_desc: '',
                empresas: []
            },
            editId: false
        };

        this.getUserData = this.getUserData.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleName = this.handleName.bind(this);
        this.handlePassword = this.handlePassword.bind(this);
        this.handlePasswordConfirmation = this.handlePasswordConfirmation.bind(this);
        this.handleEmpresas = this.handleEmpresas.bind(this);
    }

    componentDidMount () {
        let {params} = this.props.match;
        const self = this;
        if(params.editId){
            this.setState({editId: params.editId, formSubmitting: true});
            let timer = setTimeout(() => {
                self.getUserData();
                self.setEmpresasList();
            }, 1000);
        }
    }

    async setEmpresasList () {
        let getEmpresasList = await Empresas.getEmpresas();
        if(getEmpresasList.status == true && getEmpresasList.msg.length > 0){
            let newEmpresasArr = getEmpresasList.msg.map((emp, index) => {
                return {value: emp.id, label: emp.name};
            });

            this.setState({empresasOptions: newEmpresasArr});
        }
    }

    async getUserData () {
        let userInfo = await User.getUserInfoAdm(this.state.editId);
        if(userInfo.status == true){
            if(userInfo.msg.data){
                this.setState({formSubmitting: false});
                this.setState( prevState => ({
                    regform : {
                        ...prevState.regform, 
                        name: userInfo.msg.data.name,
                        email: userInfo.msg.data.email,
                        role: userInfo.msg.data.role_id,
                        role_desc: userInfo.msg.data.role_desc,
                        empresas: userInfo.msg.data.empresas.map((emp, idx) => {
                            return emp.id;
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

    handlePassword (e) {
        let valuePass = e.target.value;
        this.setState( prevState => ({
            regform : {
                ...prevState.regform, password: valuePass
            }
        }));
    }

    handlePasswordConfirmation (e) {
        let valuePassConf = e.target.value;
        this.setState( prevState => ({
            regform : {
                ...prevState.regform, password_confirmation: valuePassConf
            }
        }));
    }

    handleEmpresas (e) {
        this.setState( prevState => ({
            regform : {
                ...prevState.regform, empresas: e ? e.map(x => x.value) : []
            }
        }));
    }

    async handleSubmit (e) {
        e.preventDefault();
        this.setState({formSubmitting: true, error: [], success: []});
        let userForm = this.state.regform;
        const self = this;

        if(userForm.password == ''){
            let {password, password_confirmation, ...newUserForm} = userForm;
            userForm = newUserForm;
        }

        const editUserReq = await User.doEditUser(userForm, this.state.editId);
        this.setState({formSubmitting: false});

        if(editUserReq.status == true){
            this.setState({error: [], 
                           success: editUserReq.msg,
                           error: []
                        });
        }else{
            this.setState({error: editUserReq.msg, success: []});
        }
    }

    render () {
        let {params} = this.props.match;
        return (params.editId && params.editId != this.props.userdata.id) ? 
            <div className="container-fluid">
                <PageHeading headingtxt="Editar Usuario" />

                <div className="row">
                    <div className="col-xs-8 col-lg-7">
                        <div className="card shadow mb-4">
                            <div className="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 className="m-0 font-weight-bold text-primary">Por favor ingrese los siguientes datos</h6>
                            </div>
                            <div className="card-body">
                                <form id="edit-user-frm" className="user" onSubmit={this.handleSubmit}>
                                    <div className="form-group">
                                        <input type="text" className="form-control form-control-user" id="name" name="name" placeholder="Nombres y Apellidos" value={this.state.regform.name} required onChange={this.handleName} />
                                    </div>
                                    <div className="form-group">
                                        <input disabled={true} type="email" className="form-control form-control-user" id="email" name="email" aria-describedby="emailHelp" placeholder="E-mail" value={this.state.regform.email} />
                                    </div>
                                    <div className="form-group">
                                        <input type="password" className="form-control form-control-user" id="password" name="password" placeholder="Password" onChange={this.handlePassword} />
                                    </div>
                                    <div className="form-group">
                                        <input type="password" className="form-control form-control-user" id="password_confirmation" name="password_confirmation" placeholder="Confirmación Password" onChange={this.handlePasswordConfirmation} />
                                    </div>
                                    <div className="form-group">
                                        <label>Rol del usuario</label><br/>
                                        <span>{this.state.regform.role_desc}</span>
                                    </div>
                                    { this.state.regform.role == '1' ?
                                        <div className="form-group">
                                            <label>Empresas que puede manejar</label>
                                            <Select
                                                isMulti
                                                name="empresas"
                                                value={this.state.empresasOptions.filter(item => this.state.regform.empresas.includes(item.value))}
                                                options={this.state.empresasOptions}
                                                className="basic-multi-select"
                                                classNamePrefix="select"
                                                onChange={this.handleEmpresas}
                                            />
                                        </div>
                                        :
                                        ''
                                    }
                                    <button disabled={this.state.formSubmitting} type="submit" name="editUserBtn" className="btn btn-primary btn-user btn-block">{this.state.formSubmitting ? "Processing..." : "Editar"}</button>
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

export default withRouter(UserEdit);