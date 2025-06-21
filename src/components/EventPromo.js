import React from 'react';
import './EventPromo.css';
import { Link } from 'react-router-dom';
 

function EventPromo() {
    return (
        <div className="promo-section">
            <div className="promo-image">
                <img src="/assets/event-promo.png" alt="Promo" />
            </div>
            <div className="promo-text">
                <p className="promo-label">CREATE</p>
                <h2 className="promo-heading">Your Latest Event Posts</h2>
                <p className="promo-description">
                    Stay in the loop with the latest university events, insights, and success stories. From tech meetups to creative showcases, explore a collection of posts curated to keep you inspired and informed.
                </p>
                
                 <Link to="/create-event" className="event-promo-button"> 
                <button className="promo-button">Create Event</button>
                </Link>
                
            </div>
        </div>
    );
}

export default EventPromo;
