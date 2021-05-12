import React, {Component} from 'react';
import { Switch, Route, withRouter } from 'react-router-dom';
import UsersList from './subpages/UsersList';
import UserCreate from './subpages/UserCreate';
import UserEdit from './subpages/UserEdit';

class Users extends Component {
    constructor (props) {
        super(props);
    }
    
    render () {
        let {path, url} = this.props.match;
        return (
            <Switch>
                <Route path={`${path}/list`}>
                    {this.props.userdata.access ?
                        (this.props.userdata.access.includes('usuarios.list')) ?
                        <UsersList userdata={this.props.userdata} />
                        :
                        'Error de acceso'
                    :
                    'Error de acceso'
                    }
                </Route>
                <Route path={`${path}/create`}>
                    {this.props.userdata.access ?
                        (this.props.userdata.access.includes('usuarios.create')) ?
                        <UserCreate userdata={this.props.userdata} />
                        :
                        'Error de acceso'
                    :
                    'Error de acceso'
                    }
                </Route>
                <Route path={`${path}/edit/:editId`}>
                    {this.props.userdata.access ?
                        (this.props.userdata.access.includes('usuarios.edit')) ?
                        <UserEdit userdata={this.props.userdata} />
                        :
                        'Error de acceso'
                    :
                    'Error de acceso'
                    }
                </Route>
            </Switch>
        )
    }
}

export default withRouter(Users);