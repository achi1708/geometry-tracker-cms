import React from 'react';

const LoadingSection = () => {
    return (
        <div className="loading-section">
            <img style={{width: '2em'}} id="loading-img" src={`${process.env.MIX_IMAGE_PATH}loading-sec.gif`} />
        </div>
    )
};

export default LoadingSection;