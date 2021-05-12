import React, {Component} from 'react';
import { Redirect, withRouter } from 'react-router-dom';
import PageHeading from '../elements/PageHeading';
import Empresas from './../services/Empresas';
import FacebookBtn from './../elements/facebook/FacebookBtn';

class EmpresasFacebook extends Component {
    constructor (props) {
        super(props);
        this.state = {
            empresaData: {
                name: '',
                fai: '',
                fat: '',
                ftt: '',
                factive: false
            },
            empresaId: false
        };

        this.getEmpresaData = this.getEmpresaData.bind(this);
        this.makeFacebookBtn = this.makeFacebookBtn.bind(this);
        this.mainReadInfoFn = this.mainReadInfoFn.bind(this);
    }

    componentDidMount () {
        let {params} = this.props.match;
        const self = this;
        if(params.empresaId){
            this.setState({empresaId: params.empresaId}, function () {
                this.getEmpresaData();
            }.bind(this));
        }
    }

    async getEmpresaData () {
        let empresaInfo = await Empresas.getEmpresaInfo(this.state.empresaId);
        if(empresaInfo.status == true){
            if(empresaInfo.msg.data){
                this.setState( prevState => ({
                    empresaData : {
                        ...prevState.empresaData, 
                        name: empresaInfo.msg.data.name,
                        fai: empresaInfo.msg.data.f_a_i,
                        fat: empresaInfo.msg.data.f_a_t,
                        ftt: empresaInfo.msg.data.f_t_t,
                        factive: empresaInfo.msg.data.f_active
                    }
                }));
            }
        }
    }

    makeFacebookBtn () {
        if(this.state.empresaData.name != ''){
            return (
                <FacebookBtn mainreadinfo={this.mainReadInfoFn} empresadata={this.state.empresaData} />
            );
        }else{
            return '0';
        }
    }

    async mainReadInfoFn (fat, ftt, fuid) {
        console.log("mainReadInfoFn");
        console.log(fat);
        console.log(ftt);
        console.log(fuid);
        const params = {fat, ftt, fuid, emp: this.state.empresaId};
        let req = await Empresas.readFacebookInfo(params);
        console.log(req);    
    }

    render () {

        let {name} = this.state.empresaData;
        const fbBtn = this.makeFacebookBtn();

        return (
            <div className="container-fluid">
                <PageHeading headingtxt={`${name} - Facebook`} btn1={fbBtn} />

                <div className="row">
                </div>
            </div>
        )
    }
}

export default withRouter(EmpresasFacebook);