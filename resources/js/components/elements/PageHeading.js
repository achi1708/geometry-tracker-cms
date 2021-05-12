import React, {Component} from 'react';

class PageHeading extends Component {
    constructor (props) {
        super(props);

        this.state = { headingText: '',
                       btn1: '' };
    }

    componentDidMount () {
        if(this.props.headingtxt){
            this.setState({headingText: this.props.headingtxt});
        }

        if(this.props.btn1){
            this.setState({btn1: this.props.btn1});
        }
    }

    componentDidUpdate (prevProps) {
        if(this.props.headingtxt !== prevProps.headingtxt){
            this.setState({headingText: this.props.headingtxt});
        }

        if(this.props.btn1 !== prevProps.btn1){
            this.setState({btn1: this.props.btn1});
        }
    }
    
    render () {
        return (
            <div className="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 className="h3 mb-0 text-gray-800">{this.state.headingText}</h1>
                {this.state.btn1}
            </div>
        )
    }
}

export default PageHeading;