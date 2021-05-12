import axios from "axios";
import { reject } from "lodash";
import UserEditBtn from './../elements/UserExtraElements';

const User = {
    
    async doSignUp (formData) {

        let self = this;
        let status = false;
        let msg = [];

        let req = await axios.post('/api/users', formData)
                    .then(function(response){
                        if(response.data && (response.status == 200 || response.status == 201)){
                            status = true;
                            msg = ['Usuario registrado correctamente'];
                        }else{
                            status = false;
                            if(response.data.errors){
                                msg = response.data.errors;
                            }else{
                                msg = ['Ocurrió un error al hacer el registro, por favor intente de nuevo.'];
                            }
                        }

                        return true;
                    })
                    .catch(function(error){
                        status = false;
                        if(error.response.data.errors){
                            msg = error.response.data.errors;
                        }else{
                            msg = ['Ocurrió un error al hacer el registro, por favor intente de nuevo.'];
                        }

                        return false;
                    });

        return {status: status, msg: msg };
    },

    async getUserListAdm (page = 1, setOrder = {}) {

        let self = this;
        let status = false;
        let msg = [];
        let paramsGet = {};
        paramsGet.page = page;
        if(setOrder.name){
            paramsGet.orderCol = setOrder.name;
        }

        if(setOrder.direction){
            paramsGet.orderDir = setOrder.direction;
        }

        let req = await axios.get(`/api/users`, {params: paramsGet})
                    .then(function(response){
                        if(response.data && response.status == 200){
                            status = true;
                            msg = response.data;
                        }else{
                            status = false;
                            if(response.data.errors){
                                msg = response.data.errors;
                            }else{
                                msg = ['ERROR_USER_LIST'];
                            }
                        }

                        return true;
                    })
                    .catch(function(error){
                        status = false;
                        if(error.response.data.errors){
                            msg = error.response.data.errors;
                        }else{
                            msg = ['ERROR_USER_LIST'];
                        }

                        return false;
                    });
        return {status: status, msg: msg };
    },

    async getUserInfoAdm (userId) {

        let self = this;
        let status = false;
        let msg = [];

        let req = await axios.get(`/api/users/${userId}`)
                    .then(function(response){
                        if(response.data && response.status == 200){
                            status = true;
                            msg = response.data;
                        }else{
                            status = false;
                            if(response.data.errors){
                                msg = response.data.errors;
                            }else{
                                msg = ['ERROR_USER_INFO'];
                            }
                        }

                        return true;
                    })
                    .catch(function(error){
                        status = false;
                        if(error.response.data.errors){
                            msg = error.response.data.errors;
                        }else{
                            msg = ['ERROR_USER_INFO'];
                        }

                        return false;
                    });
        return {status: status, msg: msg };
    },

    async doEditUser (formData, editId) {

        let self = this;
        let status = false;
        let msg = [];

        let req = await axios.put(`/api/users/${editId}`, formData)
                    .then(function(response){
                        if(response.data && (response.status == 200 || response.status == 201)){
                            status = true;
                            msg = ['Usuario editado correctamente'];
                        }else{
                            status = false;
                            if(response.data.errors){
                                msg = response.data.errors;
                            }else{
                                msg = ['Ocurrió un error al editar el usuario, por favor intente de nuevo.'];
                            }
                        }

                        return true;
                    })
                    .catch(function(error){
                        status = false;
                        if(error.response.data.errors){
                            msg = error.response.data.errors;
                        }else{
                            msg = ['Ocurrió un error al editar el usuario, por favor intente de nuevo.'];
                        }

                        return false;
                    });

        return {status: status, msg: msg };
    },

    userDatatableColumns: [
        {
            name: 'name',
            label: 'Nombre',
            options: {
                sort: true
            }
        },
        {
            name: 'email',
            label: 'E-mail',
            options: {
                sort: true
            }
        },
        {
            name: 'role',
            label: 'Rol',
            options: {
                sort: false
            }
        },
        {
            name: "edit",
            label: 'Acciones',
            options: {
              filter: false,
              sort: false,
              empty: true,
              customBodyRender: (value, tableMeta, updateValue) => {
                if(value){
                    return (
                        <UserEditBtn editid={value} />
                    );
                }else{
                    return('');
                }
              }
            }
        }
    ]
}

export default User;