import React from 'react';
import SearchBar from '../components/SearchBar';
import PastEventGrid from '../components/PastEventGrid';
import './PastEvents.css';
import UpcomingEventSlider from '../components/UpcomingEventSlider';

function PastEvents() {
  return (
    <div className="past-events">
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
          paddingLeft: '3rem',
          color: '#1a1a1a'
        }}
      >
        <h1 style={{
          fontFamily: 'Playfair Display',
          fontSize: '2rem',
          
          padding: '0.5rem 1rem',
          borderRadius: '5px'
        }}>
          PAST EVENTS
        </h1>
      </div>

      <SearchBar />
      <PastEventGrid />
      <div className="pagination-wrapper">
  <button className="page-btn active">1</button>
  <button className="page-btn">2</button>
  <button className="page-btn">3</button>
</div>
<UpcomingEventSlider />
    </div>
  );
}

export default PastEvents;
