import React, {Component} from 'react';
import {Link, withRouter} from 'react-router-dom';
import Empresas from './../services/Empresas';

class EmpresaCard extends Component {
    constructor (props) {
        super(props);
        this.state = {
            empresaInfo: {}
        };
    }

    componentDidMount () {
        this.setState({empresaInfo: this.props.info});
    }

    renderDropdownMenu () {
        const {apps, id} = this.state.empresaInfo;
        let opts = [];

        if(apps){
            if(apps.length > 0){
                opts = apps.map(function(app, index){
                    return (
                        <Link key={index} className="dropdown-item" to={`/empresas/${app.name.toLowerCase()}/${id}`}>
                            Información {app.name}
                        </Link>
                    );
                }, id);
            }
        }

        if(this.props.userdata){
            if(this.props.userdata.access.includes('empresas.edit')){
                opts.push((<Link key="10" className="dropdown-item" to={`/empresas/edit/${id}`}>Editar</Link>))
            }
        }

        if(opts.length){
            return opts;
        }else{
            return '';
        }
    }
    
    render () {
        let {name, logo} = this.state.empresaInfo;
    
        const optsMenu = this.renderDropdownMenu();
        return (
            <div className="card border-left-primary shadow h-100 py-2">
                {(optsMenu != '') ?
                <div className="dropdown no-arrow" style={{display: 'flex', justifyContent: 'flex-end'}}>
                    <a className="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i className="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div className="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        {optsMenu}
                    </div>
                </div>
                :
                ''
                }
                <div className="card-body">
                    <div className="row no-gutters align-items-center">
                        <div className="col mr-2">
                            <div className="h5 mb-0 font-weight-bold text-gray-800">{(name) ? name : ''}</div>
                        </div>
                        <div className="col-auto">
                            {(logo) ? 
                            <img style={{width:'2em'}} src={"data:image/jpeg;base64," + logo} />
                            :
                            <i className="fas fa-calendar fa-2x text-gray-300"></i>
                            }
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default withRouter(EmpresaCard);