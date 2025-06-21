import React, { useState } from 'react';
import SearchBar from '../components/SearchBar';
import UpcomingEventGrid from '../components/UpcomingEventGrid';
import './UpcomingEvents.css';
import { Link } from 'react-router-dom';

function UpcomingEvents() {
  // All upcoming events
  const upcomingEvents = [
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
      university: 'University of Colombo',
    },
     {
      image: '/assets/events/4.jpeg',
      title: 'Thun Dola 2024',
      university: 'University of Kelaniya',
    },
    {
      image: '/assets/events/5.jpeg',
      title: 'Ideathon 4.0',
      university: 'University of Kelaniya',
    },
    {
      image: '/assets/events/6.jpeg',
      title: 'Frostopia 2024',
      university: 'University of Kelaniya',
    },
  ];

  // State to hold filtered events
  const [filteredEvents, setFilteredEvents] = useState(upcomingEvents);

  // Triggered by SearchBar
  const handleSearch = (query) => {
    const results = upcomingEvents.filter((event) =>
      event.title.toLowerCase().includes(query.toLowerCase())
    );
    setFilteredEvents(results);
  };

  return (
    <div className="upcoming-events">
      <div
        className="events-hero"
        style={{
          backgroundImage: "url('/assets/hero-image2.png')",
          backgroundSize: 'cover',
          backgroundPosition: 'center',
          height: '10px',
          display: 'flex',
          justifyContent: 'flex-start',
          alignItems: 'center',
        }}
      >
        <h1
          style={{
            fontFamily: 'Playfair Display',
            fontSize: '2rem',
            padding: '0.5rem 1rem',
            borderRadius: '5px',
            textAlign: 'left',
          }}
        >
          UPCOMING EVENTS
        </h1>
      </div>

      {/* 🔍 Search bar with callback */}
      <SearchBar onSearch={handleSearch} />

      {/* 🎯 Only matching events */}
      <UpcomingEventGrid events={filteredEvents} />

      <div className="events-heropast">
        <h1>PAST EVENTS</h1>
      </div>

      {/* You can later split past events properly */}
      <UpcomingEventGrid events={filteredEvents} />

      <div className="view-past-wrapper">
        <Link to="/past-events">
          <button className="view-past-button">View All Past Events</button>
        </Link>
      </div>
    </div>
  );
}

export default UpcomingEvents;
