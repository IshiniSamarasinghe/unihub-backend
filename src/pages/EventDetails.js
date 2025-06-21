import React, { useState } from 'react';
import './EventDetails.css';
import PastEventSlider from '../components/PastEventSlider';
import {
  FaCalendarAlt, FaUniversity, FaClock, FaMapMarkerAlt,
  FaUsers, FaTag, FaLaptopCode, FaBook
} from 'react-icons/fa';
import { BsThreeDotsVertical } from 'react-icons/bs';

function EventDetails() {
  const [showMenu, setShowMenu] = useState(false);

  const handleDownload = () => {
    alert('Download triggered!');
  };

  return (
    <>
      <div className="event-details-hero">
        <div
          className="event-header"
          style={{
            backgroundImage: "url('/assets/hero-image2.png')",
            backgroundSize: 'cover',
            backgroundPosition: 'center',
            height: '100px',
            display: 'flex',
            alignItems: 'center',
            paddingLeft: '3rem',
            color: '#1a1a1a'
          }}
        >
          <h1>Sneha Warsha 2025</h1>
        </div>

        {/* 🔽 Hero Image WITH More Icon */}
        <div className="event-image-container">
          <img src="/assets/events/1.jpeg" alt="CodeCraft" className="hero-image" />

          <div className="more-menu-container">
            <BsThreeDotsVertical
              className="more-icon"
              onClick={() => setShowMenu(!showMenu)}
            />
            {showMenu && (
              <div className="dropdown-menu">
                <button onClick={handleDownload}>Download</button>
              </div>
            )}
          </div>
        </div>
      </div>

      <div className="event-details-grid">
        <p><FaLaptopCode style={{ marginRight: '8px' }} /><strong>Event Name:</strong> CodeCraft Hackathon 2025</p>
        <p><FaBook style={{ marginRight: '8px' }} /><strong>Faculty:</strong> Faculty of Computing & Technology</p>
        <p><FaCalendarAlt style={{ marginRight: '8px' }} /><strong>Event Date:</strong> 02/06/2025</p>
        <p><FaMapMarkerAlt style={{ marginRight: '8px' }} /><strong>Location:</strong> FCT Auditorium</p>
        <p><FaUniversity style={{ marginRight: '8px' }} /><strong>University:</strong> University of Kelaniya</p>
        <p><FaUsers style={{ marginRight: '8px' }} /><strong>Society:</strong> ITSA</p>
        <p><FaClock style={{ marginRight: '8px' }} /><strong>Start Time:</strong> 10.00 A.M</p>
        <p><FaTag style={{ marginRight: '8px' }} /><strong>Event Type:</strong> Physical</p>
        <p><FaUsers style={{ marginRight: '8px' }} /><strong>Audience:</strong> Open to All</p>
      </div>

      <PastEventSlider />
    </>
  );
}

export default EventDetails;
