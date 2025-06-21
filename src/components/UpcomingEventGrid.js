import React from 'react';
import './EventGrid.css';
import EventCard from './EventCard';

function UpcomingEventGrid({ events }) {
  return (
    <div className="event-section">
      <div className="event-grid">
        {events.length === 0 ? (
          <p style={{ fontFamily: 'Poppins', fontSize: '14px' }}>No upcoming events found.</p>
        ) : (
          events.map((event, index) => (
            <EventCard
              key={index}
              image={event.image}
              title={event.title}
              university={event.university}
            />
          ))
        )}
      </div>
    </div>
  );
}

export default UpcomingEventGrid;
