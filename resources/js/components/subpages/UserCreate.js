import React, {Component} from 'react';
import Select from 'react-select';
import PageHeading from '../elements/PageHeading';
import FlashMessages from '../elements/FlashMessages';
import User from '../services/User';
import Empresas from '../services/Empresas';

class UserCreate extends Component {
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
                role: '1',
                empresas: []
            }
        };

        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleName = this.handleName.bind(this);
        this.handleEmail = this.handleEmail.bind(this);
        this.handlePassword = this.handlePassword.bind(this);
        this.handlePasswordConfirmation = this.handlePasswordConfirmation.bind(this);
        this.handleRole = this.handleRole.bind(this);
        this.handleEmpresas = this.handleEmpresas.bind(this);
        this.resetFormFn = this.resetFormFn.bind(this);
    }

    componentDidMount () {
        this.setEmpresasList();
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

    handleName (e) {
        let valueName = e.target.value;
        this.setState( prevState => ({
            regform : {
                ...prevState.regform, name: valueName
            }
        }));
    }

    handleEmail (e) {
        let valueEmail = e.target.value;
        this.setState( prevState => ({
            regform : {
                ...prevState.regform, email: valueEmail
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

    handleRole (e) {
        let valueRole = e.target.value;
        this.setState( prevState => ({
            regform : {
                ...prevState.regform, role: valueRole
            }
        }));

        if(valueRole != '1'){
            this.setState( prevState => ({
                regform : {
                    ...prevState.regform, empresas: []
                }
            }));
        }
    }

    handleEmpresas (e) {
        this.setState( prevState => ({
            regform : {
                ...prevState.regform, empresas: e ? e.map(x => x.value) : []
            }
        }));
    }

    resetFormFn () {
        document.getElementById("signup-user-frm").reset();
    }

    async handleSubmit (e) {
        e.preventDefault();
        this.setState({formSubmitting: true, error: [], success: []});
        let userForm = this.state.regform;
        const self = this;

        const doSignUp = await User.doSignUp(userForm);
        this.setState({formSubmitting: false});

        if(doSignUp.status == true){
            this.setState({error: [], 
                           success: doSignUp.msg,
                           regform: {
                                name: '',
                                email: '',
                                password: '',
                                password_confirmation: '',
                                role: '1',
                                empresas: []
                           }
                        });
            
            this.resetFormFn();
        }else{
            this.setState({error: doSignUp.msg, success: []});
        }
    }
    
    render () {
        return (
            <div className="container-fluid">
                <PageHeading headingtxt="Crear Usuario" />

                <div className="row">
                    <div className="col-xs-8 col-lg-7">
                        <div className="card shadow mb-4">
                            <div className="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 className="m-0 font-weight-bold text-primary">Por favor ingrese los siguientes datos</h6>
                            </div>
                            <div className="card-body">
                                <form id="signup-user-frm" className="user" onSubmit={this.handleSubmit}>
                                    <div className="form-group">
                                        <input type="text" className="form-control form-control-user" id="name" name="name" placeholder="Nombres y Apellidos" required onChange={this.handleName} />
                                    </div>
                                    <div className="form-group">
                                        <input type="email" className="form-control form-control-user" id="email" name="email" aria-describedby="emailHelp" placeholder="E-mail" required onChange={this.handleEmail} />
                                    </div>
                                    <div className="form-group">
                                        <input type="password" className="form-control form-control-user" id="password" name="password" placeholder="Password" required onChange={this.handlePassword} />
                                    </div>
                                    <div className="form-group">
                                        <input type="password" className="form-control form-control-user" id="password_confirmation" name="password_confirmation" placeholder="Confirmación Password" required onChange={this.handlePasswordConfirmation} />
                                    </div>
                                    <div className="form-group">
                                        <label>Rol del usuario</label>
                                        <div className="form-check">
                                            <input className="form-check-input" type="radio" name="role" id="role1" value="2" checked={this.state.regform.role === '2'} onChange={this.handleRole} />
                                            <label className="form-check-label">Super Administrador</label>
                                        </div>
                                        <div className="form-check">
                                            <input className="form-check-input" type="radio" name="role" id="role2" value="1" checked={this.state.regform.role === '1'} onChange={this.handleRole} />
                                            <label className="form-check-label">Usuario Regular Cms</label>
                                        </div>
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
                                    <button disabled={this.state.formSubmitting} type="submit" name="doSignUpBtn" className="btn btn-primary btn-user btn-block">{this.state.formSubmitting ? "Processing..." : "Registrar"}</button>
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
        )
    }
}

export default UserCreate;