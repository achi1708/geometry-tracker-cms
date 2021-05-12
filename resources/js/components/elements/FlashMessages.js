import React, {Component} from 'react';
import FlashMessage from 'react-flash-message';

class FlashMessages extends Component {
    constructor (props) {
        super(props);
        this.state = {
            messages: props.messages,
            style: props.type
        }
    }
    
    render () {
        var msgList = this.state.messages.map(function(msg, index){
            return <span key={index}>{msg}</span>
        });
        return (
            <FlashMessage duration={6000} persistOnHover={true}>
                <div className={`card mb-4 border-left-${this.state.style}`}>
                    <div className="card-body">
                        {msgList}
                    </div>
                </div>
            </FlashMessage>
        );
    }
}

export default FlashMessages;