import React, {Component} from 'react';
import { Redirect, withRouter } from 'react-router-dom';
import PageHeading from '../elements/PageHeading';
import Empresas from './../services/Empresas';
import FacebookBtn from './../elements/facebook/FacebookBtn';
import PreRequestFbInfoReadModal from './../elements/facebook/PreRequestFbInfoReadModal';

class EmpresasFacebook extends Component {
    constructor (props) {
        super(props);
        this.state = {
            empresaData: {
                name: '',
                fai: '',
                fat: '',
                ftt: '',
                factive: false
            },
            empresaId: false,
            openModalPreRequest: false
        };

        this.getEmpresaData = this.getEmpresaData.bind(this);
        this.makeFacebookBtn = this.makeFacebookBtn.bind(this);
        this.mainReadInfoFn = this.mainReadInfoFn.bind(this);
        this.closeModalFn = this.closeModalFn.bind(this);
    }

    componentDidMount () {
        let {params} = this.props.match;
        const self = this;
        if(params.empresaId){
            this.setState({empresaId: params.empresaId}, function () {
                this.getEmpresaData();
            }.bind(this));
        }
    }

    async getEmpresaData () {
        let empresaInfo = await Empresas.getEmpresaInfo(this.state.empresaId);
        if(empresaInfo.status == true){
            if(empresaInfo.msg.data){
                this.setState( prevState => ({
                    empresaData : {
                        ...prevState.empresaData, 
                        name: empresaInfo.msg.data.name,
                        fai: empresaInfo.msg.data.f_a_i,
                        fat: empresaInfo.msg.data.f_a_t,
                        ftt: empresaInfo.msg.data.f_t_t,
                        factive: empresaInfo.msg.data.f_active
                    }
                }));
            }
        }
    }

    makeFacebookBtn () {
        if(this.state.empresaData.name != ''){
            return (
                <FacebookBtn mainreadinfo={this.mainReadInfoFn} empresadata={this.state.empresaData} />
            );
        }else{
            return '0';
        }
    }

    async mainReadInfoFn (fat, ftt, fuid) {
        console.log("mainReadInfoFn");
        this.setState({openModalPreRequest: true});
        /*console.log(fat);
        console.log(ftt);
        console.log(fuid);
        const params = {fat, ftt, fuid, emp: this.state.empresaId};
        let req = await Empresas.readFacebookInfo(params);
        console.log(req); 
        this.setState({openModalPreRequest: true});   */
    }

    closeModalFn (){
        this.setState({openModalPreRequest: false});
    }

    render () {

        let {name} = this.state.empresaData;
        const fbBtn = this.makeFacebookBtn();

        console.log(this.props.useStyles)

        /*const classes = useStyles();
        const [open, setOpen] = React.useState(false);

        const handleOpen = () => {
            setOpen(true);
        };

        const handleClose = () => {
            setOpen(false);
        };  

        const useStyles = makeStyles(theme => ({
            modal: {
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
            },
            paper: {
                backgroundColor: theme.palette.background.paper,
                border: '2px solid #000',
                boxShadow: theme.shadows[5],
                padding: theme.spacing(2, 4, 3),
            },
        }));*/

        return (
            <div className="container-fluid">
                <PageHeading headingtxt={`${name} - Facebook`} btn1={fbBtn} />

                <div className="row">
                </div>

                <PreRequestFbInfoReadModal clickopen={this.state.openModalPreRequest} clickclose={this.closeModalFn} />
            </div>
        )
    }
}

export default withRouter(EmpresasFacebook);