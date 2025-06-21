import React from 'react';
import './EventGrid.css'; // Reuse the same grid styles
import EventCard from './EventCard';

const pastEvents = [
  {
    image: '/assets/events/1.jpeg',
    title: 'Sneha Warsha 2025',
    university: 'University of Kelaniya',
  },
  {
    image: '/assets/events/2.jpeg',
    title: 'Sankramana 2025',
    university: 'University of Moratuwa',
  },
  {
    image: '/assets/events/3.jpeg',
    title: 'Champions League 2024',
    university: 'University of Kelaniya',
  },
  {
    image: '/assets/events/4.jpeg',
    title: 'Thun Dola 2024',
    university: 'University of Kelaniya',
  },
  {
    image: '/assets/events/5.jpeg',
    title: 'IDEATHON 4.0',
    university: 'University of Kelaniya',
  },
  {
    image: '/assets/events/6.jpeg',
    title: 'Frostipia 2024',
    university: 'University of Kelaniya',
  },
];

function PastEventGrid() {
  return (
    <div className="event-section">
      <div className="event-grid">
        {pastEvents.map((event, index) => (
          <EventCard
            key={index}
            image={event.image}
            title={event.title}
            university={event.university}
          />
        ))}
      </div>
    </div>
  );
}

export default PastEventGrid;
