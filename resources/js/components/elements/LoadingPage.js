import React from 'react';

const LoadingPage = () => {
    return (
        <div className="loading-page">
            <img style={{width: '3em'}} id="loading-img" src={`${process.env.MIX_IMAGE_PATH}loading.gif`} />
        </div>
    )
};

export default LoadingPage;