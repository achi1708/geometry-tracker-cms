import React, {Component} from 'react';
import { withRouter } from 'react-router-dom';
import SidebarLogo from './sidebar/SidebarLogo';
import SidebarMenuModule from './sidebar/SidebarMenuModule';

class Sidebar extends Component {
    constructor (props) {
        super(props);
        this.state = { 
            userData: false,
            activeName: ''
        };

        this.buildUsuariosNavLink = this.buildUsuariosNavLink.bind(this);
        this.buildEmpresasNavLink = this.buildEmpresasNavLink.bind(this);
    }
    
    componentDidMount () {
        if(this.props.userdata){
            this.setState({userData: this.props.userdata});
        }

        if(this.props.activelink){
            this.setState({activeName: this.props.activelink});
        }
    }

    buildUsuariosNavLink () {
        if(this.state.userData){
            if(this.state.userData.access.includes('usuarios.access')){
                let optionMenuUsers = [];
                if(this.state.userData.access.includes('usuarios.list')){
                    optionMenuUsers.push({link: '/users/list', text: 'Ver Usuarios'});
                }
                if(this.state.userData.access.includes('usuarios.create')){
                    optionMenuUsers.push({link: '/users/create', text: 'Crear Usuario'});
                }

                if(optionMenuUsers.length > 0){
                    return (
                        <SidebarMenuModule mainname="Usuarios" mainicon="fa-user-friends" mainlink="" collapse={true} options={optionMenuUsers} activelink={this.state.activeName} />
                    );
                }else{
                    return '';
                }
            }else{
                return '';
            }
        }else{
            return '';
        }
    }

    buildEmpresasNavLink () {
        if(this.state.userData){
            if(this.state.userData.access.includes('empresas.access')){
                let optionMenuEmpresas = [];
                if(this.state.userData.access.includes('empresas.list')){
                    optionMenuEmpresas.push({link: '/empresas/list', text: 'Ver Empresas'});
                }
                if(this.state.userData.access.includes('empresas.create')){
                    optionMenuEmpresas.push({link: '/empresas/create', text: 'Crear Empresa'});
                }

                if(optionMenuEmpresas.length > 0){
                    return (
                        <SidebarMenuModule mainname="Empresas" mainicon="fa-building" mainlink="" collapse={true} options={optionMenuEmpresas} activelink={this.state.activeName} />
                    );
                }else{
                    return '';
                }
            }else{
                return '';
            }
        }else{
            return '';
        }
    }

    render () {
        return (
            <ul className="navbar-nav bg-gradient-light sidebar sidebar-light accordion" id="accordionSidebar">
                <SidebarLogo />
                <hr className="sidebar-divider my-0" />
                <SidebarMenuModule mainname="Home" mainicon="fa-home" mainlink="/" collapse={false} options={[]} activelink={this.state.activeName} />
                {this.buildUsuariosNavLink()}
                {this.buildEmpresasNavLink()}
            </ul>
        );
    }
}

export default withRouter(Sidebar);