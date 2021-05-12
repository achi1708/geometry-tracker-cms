import React, {Component} from 'react';
import { Redirect, Route, withRouter } from 'react-router-dom';
import Auth from './services/Auth';
import LoadingPage from './elements/LoadingPage';
import Sidebar from './elements/Sidebar';
import Header from './elements/Header';

class Home extends Component {
    constructor () {
        super();
        this.state = { loadingPage: true, failPage: false, dataAuth: {} };
        this.init();
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
    render () {
        return (this.state.loadingPage) ?
            <LoadingPage />
        :
            (this.state.failPage) ?
            <Redirect to={{pathname: "/login" }} />
            :
            <div id="wrapper">
                <Sidebar userdata={this.state.dataAuth} activelink="Home"  />
                <div id="content-wrapper" className="d-flex flex-column">
                    <div id="content">
                        <Header userdata={this.state.dataAuth} />
                    </div>
                </div>
            </div>
        ;
    }
}

export default withRouter(Home);