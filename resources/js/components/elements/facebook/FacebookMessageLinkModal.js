import React, {useState} from 'react';
import {Modal, Button} from '@material-ui/core';
import {makeStyles} from '@material-ui/core/styles';

const useStyles = makeStyles((theme) => ({
    modal: {
        position: 'absolute',
        width: '85%',
        minWidth: 300,
        backgroundColor: 'white',
        border: '2px solid #000',
        boxShadow: theme.shadows[5],
        padding: '16px 32px',
        top: '50%',
        left: '50%',
        transform: 'translate(-50%, -50%)'
    },
    bloquediv: {
        marginBottom: '15px'
    },
    link: {
        cursor: 'pointer'
    }
}));

function FacebookMessageLinkModal(props) {
    const styles = useStyles();

    const [modal, setModal] = useState(false);

    const openCloseModal = () => {
        setModal(!modal);
    }

    const body = (
        <div className={styles.modal}>
            <div align="center" className={styles.bloquediv}>
                {props.bodytext}
            </div>
            <div align="right">
                <Button className="primary" onClick={()=>openCloseModal()}>Cerrar</Button>
            </div>
        </div>
    );
    return(
        <>
            <a className={styles.link} onClick={()=>openCloseModal()}>Ver más</a>
            <Modal
                open={modal}
                onClose={openCloseModal}
            >
                {body}
            </Modal>
        </>
    )
}

export default FacebookMessageLinkModal;