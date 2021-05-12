import React, {Component} from 'react';
import { withRouter, useHistory } from 'react-router-dom';
import Auth from './services/Auth';
import FlashMessages from './elements/FlashMessages';

class Login extends Component {
    constructor (props) {
        super(props);
        this.state = {
            loginDone: false,
            error: [],
            formSubmitting: false,
            user: {
                email: '',
                password: ''
            }
        }

        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleEmail = this.handleEmail.bind(this);
        this.handlePassword = this.handlePassword.bind(this);
    }

    handleEmail (e) {
        let valueEmail = e.target.value;
        this.setState( prevState => ({
            user : {
                ...prevState.user, email: valueEmail
            }
        }));
    }

    handlePassword (e) {
        let valuePass = e.target.value;
        this.setState( prevState => ({
            user : {
                ...prevState.user, password: valuePass
            }
        }));
    }

    async handleSubmit (e) {
        e.preventDefault();
        this.setState({formSubmitting: true, error: [], loginDone: false});
        let userForm = this.state.user;
        const self = this;

        const doLogin = await Auth.doLogin(userForm);
        this.setState({formSubmitting: false});

        if(doLogin.status == true){
            this.setState({error: [], loginDone: true});

            let timer = setTimeout(() => self.props.history.push('/'), 1000);
        }else{
            this.setState({error: doLogin.msg, loginDone: false});
        }
    }
    render () {
        return (
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-xl-9 col-lg-12 col-md-8">
                        <div className="card o-hidden border-0 shadow-lg my-5">
                            <div className="card-body p-0">
                                <div className="row justify-content-center">
                                    <div className="col-lg-8 d-lg-block">
                                        <div className="p-5">
                                            <div className="text-center">
                                                <img style={{width: '6em'}} className="rounded-circle" src={`${process.env.MIX_IMAGE_PATH}logo.png`} />
                                                <h1 className="h4 text-gray-900 mb-4">¡Bienvenido!</h1>
                                            </div>
                                            <form className="user" onSubmit={this.handleSubmit}>
                                                <div className="form-group">
                                                    <input type="email" className="form-control form-control-user" id="email" name="email" aria-describedby="emailHelp" placeholder="E-mail" required onChange={this.handleEmail} />
                                                </div>
                                                <div className="form-group">
                                                    <input type="password" className="form-control form-control-user" id="password" name="password" placeholder="Password" required onChange={this.handlePassword} />
                                                </div>
                                                <button disabled={this.state.formSubmitting} type="submit" name="doLoginBtn" className="btn btn-primary btn-user btn-block">{this.state.formSubmitting ? "Processing..." : "Login"}</button>
                                            </form>
                                            <br />
                                            { this.state.error.length > 0 ?
                                                <FlashMessages messages={this.state.error} type="danger" /> :
                                                ''
                                            }
                                            <hr />
                                            <div className="text-center">
                                                <a className="small" href="#">Forgot Password?</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default withRouter(Login);