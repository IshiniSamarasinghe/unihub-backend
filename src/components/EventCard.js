import React from 'react';
import { Link } from 'react-router-dom';

function EventCard({ image, title, university }) {
  return (
    <Link to="/event/codecraft2025" style={{ textDecoration: 'none', color: 'inherit' }}>
      <div className="event-card">
        <img src={image} alt="event" className="event-image" />

        <h3 className="event-title">{title}</h3>
        <p className="event-university">{university}</p>
      </div>
    </Link>
  );
}

export default EventCard;
