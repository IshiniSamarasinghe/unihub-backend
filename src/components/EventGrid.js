import React from 'react';
import './EventGrid.css';
import EventCard from './EventCard';

function EventGrid({ events }) {
  return (
    <div id="event-section" className="event-section">

      <div className="event-grid">
        {events.length > 0 ? (
          events.map((event, index) => (
            <EventCard
              key={index}
              image={event.image}
              title={event.title}
              university={event.university}
            />
          ))
        ) : (
          <p>No matching events found.</p>
        )}
      </div>

      <div className="view-all-wrapper">
        <a href="/events" className="view-all-button">
          View All Events
        </a>
      </div>
    </div>
  );
}

export default EventGrid;
