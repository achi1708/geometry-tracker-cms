import React from 'react';
import {BrowserRouter, Link, Route, Switch} from 'react-router-dom';

import Home from "./Home";
import Login from "./Login";
import PrivateRoute from './PrivateRoute';

const Main = () => (
    <Switch>
        <Route exact path="/" component={Home} />
        <Route path="/login" component={Login} />
        <PrivateRoute path="/users" component='users' />
        <PrivateRoute path="/empresas" component='empresas' />
    </Switch>
);

export default Main;