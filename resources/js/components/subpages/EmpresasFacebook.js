import React, {Component} from 'react';
import { Switch, Route, withRouter, Redirect } from 'react-router-dom';
import EmpresasFacebookTabs from './EmpresasFacebookTabs';
import PageHeading from '../elements/PageHeading';
import Empresas from './../services/Empresas';

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
    }
    componentDidMount () {
        let {params} = this.props.match;
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

    render () {
        let {path, url} = this.props.match;
        let {name} = this.state.empresaData;

        return (
            <div className="container-fluid">
                <PageHeading headingtxt={`${name} - Facebook`} />

                <div className="row">
                    <Switch>
                        <Redirect exact from={`${path}/`} to={`${path}/publish_post`} />
                        <Redirect exact from={`${path}`} to={`${path}/publish_post`} />
                        <Route path={`${path}/:tabname?`}>
                            <EmpresasFacebookTabs 
                                empresaid={this.state.empresaId} 
                                empresadata={this.state.empresaData} 
                                userdata={this.props.userdata} />
                        </Route>
                    </Switch>
                </div>
            </div>
        )
    }
}

export default withRouter(EmpresasFacebook);