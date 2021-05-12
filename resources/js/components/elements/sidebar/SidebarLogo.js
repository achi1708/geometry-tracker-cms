import React from 'react';
import {Link, withRouter} from 'react-router-dom';

const SidebarLogo = () => {
    return (
        <Link to="/">
            <div className="sidebar-brand d-flex align-items-center justify-content-center">
                <div className="sidebar-brand-icon">
                <img style={{width: '2rem'}} className="rounded-circle" src={`${process.env.MIX_IMAGE_PATH}logo.png`} />
                </div>
                <div className="sidebar-brand-text mx-3">Geometry</div>
            </div>
        </Link>
    )
};

export default withRouter(SidebarLogo);