import React, {Component} from 'react';
import {Link, withRouter} from 'react-router-dom';

class SidebarMenuModule extends Component {
    constructor (props) {
        super(props);
        this.state = { 
            mainName: '',
            mainIcon: '',
            mainLink: '',
            activeName: '',
            collapse: false,
            options: []
        };

        this.buildNavLink = this.buildNavLink.bind(this);
        this.buildCollapse = this.buildCollapse.bind(this);
    }
    
    componentDidMount () {
        if(this.props.mainname){
            this.setState({
                mainName: this.props.mainname,
                mainIcon: this.props.mainicon,
                mainLink: this.props.mainlink,
                activeName: this.props.activelink,
                collapse: this.props.collapse,
                options: this.props.options,
            });
        }
    }

    buildNavLink () {
        if(this.state.collapse){
            return (
                <a className="nav-link collapsed" href="#" data-toggle="collapse" data-target={`#collapse${this.state.mainName}`}
                    aria-expanded="true" aria-controls={`collapse${this.state.mainName}`}>
                    <i className={`fas fa-fw ${this.state.mainIcon}`}></i>
                    <span>{this.state.mainName}</span>
                </a>
            );
        }else if(this.state.mainLink != ''){
            return (
                <Link className="nav-link" to={this.state.mainLink}>
                    <i className={`fas fa-fw ${this.state.mainIcon}`}></i>
                    <span>{this.state.mainName}</span>
                </Link>
            );
        }else{
            return (
                <a className="nav-link" href="#!">
                    <i className={`fas fa-fw ${this.state.mainIcon}`}></i>
                    <span>{this.state.mainName}</span>
                </a>
            );
        }
    }

    buildCollapse () {
        var optList = this.state.options.map(function(opt, index){
            return <div key={index}><Link className="collapse-item" to={opt.link}>{opt.text}</Link></div>
        });
        return (
            <div id={`collapse${this.state.mainName}`} className="collapse" aria-labelledby={`heading${this.state.mainName}`} data-parent="#accordionSidebar">
                <div className="bg-white py-2 collapse-inner rounded">
                    {optList}
                </div>
            </div>
        );
    }

    render () {
        var active = (this.props.activelink == this.state.mainName) ? 'active' : '';
        return (
            <li className={"nav-item "+active}>
                {this.buildNavLink()}
                {this.state.collapse ?
                this.buildCollapse()
                :
                ''
                }
            </li>
        );
    }
}

export default withRouter(SidebarMenuModule);