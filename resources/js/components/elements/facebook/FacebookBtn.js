import React, {Component} from 'react';
import { Redirect, withRouter } from 'react-router-dom';

class FacebookBtn extends Component {
    constructor (props) {
        super(props);

        this.checkFb = this.checkFb.bind(this);
        this.callToAction = this.callToAction.bind(this);
        this.callToActionTwo = this.callToActionTwo.bind(this);
        this.scanBusiness = this.scanBusiness.bind(this);
    }

    callToAction () {
        const self = this;
        if(this.props.empresadata.factive == true){
            self.scanBusiness(this.props.empresadata.fat, this.props.empresadata.ftt);
        }else{
            this.checkFb(this.callToActionTwo);
        }
    }

    callToActionTwo (resp) {
        const {status, authResponse} = resp;
        const self = this;

        if(status){
            if(status == 'connected'){
                self.scanBusiness(authResponse.accessToken, authResponse.data_access_expiration_time, authResponse.userID);
            }else{
                FB.login(function(response){
                    console.log("FACEBOOK LOGIN");
                    console.log(response);
                    if(response.status == 'connected'){
                        self.scanBusiness(response.authResponse.accessToken, response.authResponse.data_access_expiration_time, response.authResponse.userID);
                    }else{
                        alert("No se cuenta con el acceso correcto al usuario de Facebook que administra esta empresa");
                    }
                }, {scope: 'email,read_insights,ads_management,ads_read,business_management'});
            }
        }
    }

    scanBusiness (fat, ftt, fuid) {
        this.props.mainreadinfo(fat, ftt, fuid);
    }


    checkFb (callback) {
        FB.getLoginStatus(function(response) {
            console.log("FACEBOOK STATUS");
            console.log(response);
            callback(response);
        });
    }

    render () {
       return (
        <a onClick={this.callToAction} className="btn btn-facebook btn-user">
            <i className="fab fa-facebook-f fa-fw"></i> Obtener información actualizada
        </a>
       );
    }
}

export default withRouter(FacebookBtn);