import axios from "axios";
import { reject } from "lodash";

const Empresas = {
    
    async getEmpresas () {

        let self = this;
        let status = false;
        let msg = [];

        let req = await axios.get('/api/empresas/')
                    .then(function(response){
                        if(response.data && response.status == 200){
                            if(response.data.data){
                                status = true;
                                msg = response.data.data;   
                            }else{
                                status = false;
                                msg = 'ERROR_GET_RESOURCE';
                            }
                        }else{
                            status = false;
                            msg = 'ERROR_GET_RESOURCE';
                        }

                        return true;
                    })
                    .catch(function(error){
                        status = false;
                            msg = 'ERROR_GET_RESOURCE';

                        return false;
                    });

        return {status: status, msg: msg };
    },

    async getEmpresaInfo (empresaId) {

        let self = this;
        let status = false;
        let msg = [];

        let req = await axios.get(`/api/empresas/${empresaId}`)
                    .then(function(response){
                        if(response.data && response.status == 200){
                            status = true;
                            msg = response.data;
                        }else{
                            status = false;
                            if(response.data.errors){
                                msg = response.data.errors;
                            }else{
                                msg = ['ERROR_EMPRESA_INFO'];
                            }
                        }

                        return true;
                    })
                    .catch(function(error){
                        status = false;
                        if(error.response.data.errors){
                            msg = error.response.data.errors;
                        }else{
                            msg = ['ERROR_EMPRESA_INFO'];
                        }

                        return false;
                    });
        return {status: status, msg: msg };
    },

    /*async readFacebookInfo (params) {

        let self = this;
        let status = false;
        let msg = [];

        let req = await axios.post('/api/empresas/readFbData', params)
                    .then(function(response){
                        console.log(response);
                        if(response.data && (response.status == 200 || response.status == 201)){
                            status = true;
                            msg = ['ok'];
                        }else{
                            status = false;
                            if(response.data.errors){
                                msg = response.data.errors;
                            }else{
                                msg = ['Error'];
                            }
                        }

                        return true;
                    })
                    .catch(function(error){
                        status = false;
                        if(error.response.data.errors){
                            msg = error.response.data.errors;
                        }else{
                            msg = ['Error'];
                        }

                        return false;
                    });
        return {status: status, msg: msg };
    },*/

    convertLogoToImage: (blob) => new Promise((resolve, reject) => {
        const newBlob = new Blob(blob);
        const reader = new FileReader;
        reader.onerror = reject;
        reader.onload = () => {
            resolve(reader.result);
        };
        console.log(typeof newBlob);
        console.log(newBlob);
        reader.readAsDataURL(newBlob);
    })
}

export default Empresas;