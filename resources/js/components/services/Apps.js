import axios from "axios";
import { reject } from "lodash";

const Apps = {

    async getAppsList () {

        let self = this;
        let status = false;
        let msg = [];

        let req = await axios.get(`/api/apps`)
                    .then(function(response){
                        if(response.data && response.status == 200){
                            status = true;
                            msg = response.data;
                        }else{
                            status = false;
                            if(response.data.errors){
                                msg = response.data.errors;
                            }else{
                                msg = ['ERROR_APPS_LIST'];
                            }
                        }

                        return true;
                    })
                    .catch(function(error){
                        status = false;
                        if(error.response.data.errors){
                            msg = error.response.data.errors;
                        }else{
                            msg = ['ERROR_APPS_LIST'];
                        }

                        return false;
                    });
        return {status: status, msg: msg };
    }
}

export default Apps;