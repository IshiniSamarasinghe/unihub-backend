// src/components/Navbar.js
import React, { useState } from 'react';
import './Navbar.css';
import { Link } from 'react-router-dom';
import { FaStore } from 'react-icons/fa';

function Navbar() {
  const [isHoverSignUp, setIsHoverSignUp] = useState(false);
  const [isHoverCreate, setIsHoverCreate] = useState(false);

  const signUpStyle = {
    backgroundColor: isHoverSignUp ? '#fff' : '#EAD8B1',
    color: isHoverSignUp ? '#000' : '#000',
    border: 'none',
    padding: '0.5rem 1rem',
    borderRadius: '5px',
    cursor: 'pointer',
    fontWeight: 'regular',
    transition: 'all 0.3s ease',
    fontSize: '12px',
    fontFamily: 'Poppins, sans-serif',
  };

  const createEventStyle = {
    backgroundColor: isHoverCreate ? '#fff' : '#000',
    color: isHoverCreate ? '#000' : '#fff',
    border: 'none',
    padding: '0.5rem 1rem',
    borderRadius: '5px',
    cursor: 'pointer',
    fontWeight: 'regular',
    transition: 'all 0.3s ease',
    fontSize: '12px',
    fontFamily: 'Poppins, sans-serif',
  };

  return (
    <nav style={{
      backgroundColor: '#B4511F',
      padding: '1rem 2rem',
      color: 'white',
      display: 'flex',
      justifyContent: 'space-between',
      alignItems: 'center',
      fontFamily: 'Poppins, sans-serif',
      position: 'sticky',
      top: 0,
      zIndex: 1000,
    }}>
      <div style={{ fontWeight: 'bold', fontSize: '1.5rem' }}>
        UniHub
      </div>

      <div style={{ display: 'flex', gap: '1.5rem', fontSize: '12px', justifyContent: 'center', alignItems: 'center' }}>
        <a href="/" className="nav-link">Home</a>
        <a href="/events" className="nav-link">Events</a>
        <a href="/about" className="nav-link">About</a>
         
      </div>

      <div style={{ display: 'flex', gap: '1rem', alignItems: 'center' }}>
       

        <button
          style={signUpStyle}
          onMouseEnter={() => setIsHoverSignUp(true)}
          onMouseLeave={() => setIsHoverSignUp(false)}
        >
          <Link to="/signup" className="nav-signup-button" style={{ textDecoration: 'none', color: 'inherit' }}>
            Sign Up
          </Link>
        </button>

        <Link to="/create-event" className="nav-create-button">
          <button
            style={createEventStyle}
            onMouseEnter={() => setIsHoverCreate(true)}
            onMouseLeave={() => setIsHoverCreate(false)}
          >
            Create Event
          </button>
        </Link>
          <Link to="/store" title="Go to Store" className="store-icon-link" style={{ color: '#fff', fontSize: '1.2rem' }}>
          <FaStore />
        </Link>
      </div>
    </nav>
  );
}

export default Navbar;
