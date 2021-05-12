import React, {Component} from 'react';
import { Redirect, Route, withRouter, useParams, useRouteMatch } from 'react-router-dom';
import Auth from './services/Auth';
import LoadingPage from './elements/LoadingPage';
import Sidebar from './elements/Sidebar';
import Header from './elements/Header';

import Users from "./Users";
import Empresas from "./Empresas";

class PrivateRoute extends Component {
    constructor (props) {
        super(props);
        this.state = { loadingPage: true, failPage: false, dataAuth: {} };
        this.init();

        this.renderRoute = this.renderRoute.bind(this);
    }
    async init () {
        const { resp } = await Auth.isAuthenticated();

        if(!resp) {
            console.log("Unauthorized");
            this.setState({ failPage: true, loadingPage: false });
        }else{
            console.log("authorized");
            console.log(Auth.dataAuthenticated);
            this.setState({ loadingPage: false, dataAuth: Auth.dataAuthenticated});
        }
        
    }

    renderRoute () {
        let retorno = '';

        switch(this.props.component){
            case 'users':
                retorno = <Route path={this.props.path} render={(props) =>  (<Users {...props} userdata={this.state.dataAuth} />)} />
                break;
            case 'empresas':
                retorno = <Route path={this.props.path} render={(props) =>  (<Empresas {...props} userdata={this.state.dataAuth} />)} />
                break;
        }

        return retorno;
    }

    render () {
        let activelink = '';
        if(this.props.path.includes('/users')){
            activelink = 'Usuarios';
        }else if(this.props.path.includes('/empresas')){
            activelink = 'Empresas';
        }
        return (this.state.loadingPage) ?
            <LoadingPage />
        :
            (this.state.failPage) ?
            <Redirect to={{pathname: "/login" }} />
            :
            <div id="wrapper">
                <Sidebar userdata={this.state.dataAuth} activelink={activelink} />
                <div id="content-wrapper" className="d-flex flex-column">
                    <div id="content">
                        <Header userdata={this.state.dataAuth} />
                        {this.renderRoute()}
                    </div>
                </div>
            </div>
        ;
    }
}

export default withRouter(PrivateRoute);