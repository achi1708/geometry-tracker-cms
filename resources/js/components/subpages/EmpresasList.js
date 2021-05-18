import React, {Component} from 'react';
import PageHeading from '../elements/PageHeading';
import FlashMessages from '../elements/FlashMessages';
import LoadingSection from '../elements/LoadingSection';
import EmpresaCard from '../elements/EmpresaCard';
import Empresas from '../services/Empresas';
import { useHistory } from 'react-router';

class EmpresasList extends Component {
    constructor (props) {
        super(props);

        this.state = {
            data: [],
            isLoading: false
        };

        this.getEmpresasReq = this.getEmpresasReq.bind(this);

    }

    componentDidMount () {
        const self = this;
        let timer = setTimeout(() => self.getEmpresasReq(), 700);
    }

    async getEmpresasReq () {
        this.setState({isLoading: true});
        let empresasList = await Empresas.getEmpresas();
        if(empresasList.status == true){
            this.setState({data: empresasList.msg, isLoading: false});
        }
    }

    render () {

        var self = this;

        var empListRender = this.state.data.map(function(empresa, index){
            return (
                <div key={index} className="col-xl-3 col-md-6 mb-4">
                    <EmpresaCard info={empresa} userdata={self.props.userdata} />
                </div>
            );
        }, self);

        return (
            <div className="container-fluid">
                <PageHeading headingtxt="Empresas" />

                <div className="row">
                    {empListRender}
                </div>
            </div>
        )
    }
}

export default EmpresasList;