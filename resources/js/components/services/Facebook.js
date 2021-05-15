import axios from "axios";
import { reject } from "lodash";
import FacebookMessageLinkModal from './../elements/facebook/FacebookMessageLinkModal';
import FacebookInsightsLinkModal from './../elements/facebook/FacebookInsightsLinkModal';

const Facebook = {

    async getPublishedPostsListAdm (page = 1, setOrder = {}, empresaId) {
        let self = this;
        let status = false;
        let msg = [];
        let paramsGet = {};
        paramsGet.emp = empresaId;
        paramsGet.page = page;
        if(setOrder.name){
            paramsGet.orderCol = setOrder.name;
        }

        if(setOrder.direction){
            paramsGet.orderDir = setOrder.direction;
        }

        let req = await axios.get(`/api/facebook/published_posts`, {params: paramsGet})
                    .then(function(response){
                        if(response.data && response.status == 200){
                            status = true;
                            msg = response.data;
                        }else{
                            status = false;
                            if(response.data.errors){
                                msg = response.data.errors;
                            }else{
                                msg = ['ERROR_LIST'];
                            }
                        }

                        return true;
                    })
                    .catch(function(error){
                        status = false;
                        if(error.response.data.errors){
                            msg = error.response.data.errors;
                        }else{
                            msg = ['ERROR_LIST'];
                        }

                        return false;
                    });
        return {status: status, msg: msg };
    },

    publishedPostsDatatableColumns: [
        {
            name: 'imagen',
            label: 'Imagen',
            options: {
                filter: false,
                sort: false,
                empty: true,
                customBodyRender: (value, tableMeta, updateValue) => {
                    if(value){
                        return (
                            <img style={{width: '3em'}} src={`${value}`} />
                        );
                    }else{
                        return('');
                    }
                }
            }
        },
        {
            name: 'mensaje',
            label: 'Mensaje',
            options: {
                filter: false,
                sort: false,
                empty: true,
                customBodyRender: (value, tableMeta, updateValue) => {
                    if(value){
                        console.log(tableMeta);
                        return (
                            <div>
                                {value.substr(0, 30)}
                                {(value.length > 30) ? 
                                <span>
                                    ...
                                    <FacebookMessageLinkModal key={tableMeta.rowIndex} bodytext={value} />
                                </span>
                                : ''}
                            </div>
                        );
                    }else{
                        return('');
                    }
                }
            }
        },
        {
            name: 'expirado',
            label: 'Expirado',
            options: {
                sort: false
            }
        },
        {
            name: 'oculto',
            label: 'Oculto',
            options: {
                sort: false
            }
        },
        {
            name: 'popular',
            label: 'Popular',
            options: {
                sort: false
            }
        },
        {
            name: 'publicado',
            label: 'Publicado',
            options: {
                sort: false
            }
        },
        {
            name: 'tags',
            label: 'Tags',
            options: {
                filter: false,
                sort: false,
                empty: true,
                customBodyRender: (value, tableMeta, updateValue) => {
                    if(value){
                        return (
                            <div>
                                {value.join(' - ')}
                            </div>
                        );
                    }else{
                        return('');
                    }
                }
            }
        },
        {
            name: 'insights',
            label: 'Insights',
            options: {
                filter: false,
                sort: false,
                empty: true,
                customBodyRender: (value, tableMeta, updateValue) => {
                    if(value){
                        if(value.length > 0){
                            return (
                                <div>
                                   <FacebookInsightsLinkModal key={tableMeta.rowIndex} insights={value} />
                                </div>
                            );
                        }else{
                            return('No se registraron insights');    
                        }
                    }else{
                        return('No se registraron insights');
                    }
                }
            }
        },
        {
            name: 'fecha_creacion',
            label: 'Fecha Creación',
            options: {
                sort: true
            }
        }
    ]
}

export default Facebook;